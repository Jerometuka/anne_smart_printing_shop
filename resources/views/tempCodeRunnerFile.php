<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdminController extends Controller
{
    private const MONTHLY_TARGET = 25000;

    /** Hanapin ang filename ng inupload na file ng customer sa order. */
    public static function orderFileName($o): string
    {
        $o = (array) $o;
        foreach (['document_file', 'file_path', 'file_name', 'uploaded_file', 'file', 'document', 'attachment'] as $k) {
            if (!empty($o[$k])) return (string) $o[$k];
        }
        return '';
    }

    /** I-save ang inupload na file sa public/uploads at ibalik ang filename. */
    private function saveUpload(Request $request, string $field, string $prefix, ?array $allowedExts = null): ?string
    {
        if (!$request->hasFile($field) || !$request->file($field)->isValid()) {
            return null;
        }
        $file = $request->file($field);
        $ext = strtolower($file->getClientOriginalExtension());
        if ($allowedExts !== null && !in_array($ext, $allowedExts, true)) {
            return null;
        }
        $name = $prefix . '_' . time() . '_' . uniqid() . '.' . $ext;
        $dir = public_path('uploads');
        if (!is_dir($dir)) { mkdir($dir, 0777, true); }
        $file->move($dir, $name);
        return $name;
    }

    private function backTo(string $tab, array $extra = [])
    {
        return redirect()->route('admin.index', array_merge(['tab' => $tab], $extra));
    }

    /* ---------------- Dashboard ---------------- */

    public function index(Request $request)
    {
        $activeOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->where('orders.status', '!=', 'Successful Pickup')
            ->orderBy('orders.order_id')
            ->select('orders.*', 'users.full_name')->get();

        $orderHistory = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->where('orders.status', 'Successful Pickup')
            ->orderByDesc('orders.order_id')
            ->select('orders.*', 'users.full_name')->get();

        $inventoryItems = DB::table('inventory')->orderBy('item_name')->get();
        $servicesItems  = DB::table('services_offered')->orderBy('service_id')->get();

        $totalSales = (float) (DB::table('orders')->sum('total') ?? 0);

        $actualMonthlySales = (float) DB::table('orders')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $monthlyTarget = self::MONTHLY_TARGET;
        $monthlyPercentage = min(100, ($actualMonthlySales / $monthlyTarget) * 100);

        $monthly = array_fill(1, 12, 0.0);
        $rows = DB::table('orders')
            ->selectRaw('MONTH(created_at) as m, SUM(total) as total')
            ->groupByRaw('MONTH(created_at)')->get();
        foreach ($rows as $r) {
            $monthly[(int) $r->m] = (float) $r->total;
        }
        $monthlySalesArray = array_values($monthly);

        $customersWithChats = DB::table('users')
            ->join('messages', 'users.user_id', '=', 'messages.user_id')
            ->where('users.role', 'customer')
            ->select('users.user_id', 'users.full_name', 'users.email')
            ->distinct()->get();

        $selectedChatUser = $request->query('chat_user', $customersWithChats->first()->user_id ?? null);
        $chatMessages = collect();
        if ($selectedChatUser) {
            $chatMessages = DB::table('messages')
                ->where('user_id', $selectedChatUser)
                ->orderBy('created_at')->get();
        }

        return view('admin', compact(
            'activeOrders', 'orderHistory', 'inventoryItems', 'servicesItems',
            'totalSales', 'actualMonthlySales', 'monthlyPercentage',
            'monthlySalesArray', 'customersWithChats', 'selectedChatUser', 'chatMessages'
        ));
    }

    /* ---------------- File ng customer (admin lang) ---------------- */

    public function orderFile(int $orderId)
    {
        $row = DB::table('orders')->where('order_id', $orderId)->first();
        $fname = $row ? basename(self::orderFileName($row)) : '';
        $found = '';

        if ($fname !== '') {
            foreach (['uploads/', 'uploads/orders/', 'uploads/documents/', 'uploads/files/'] as $dir) {
                $path = public_path($dir . $fname);
                if (is_file($path)) { $found = $path; break; }
            }
        }

        if ($found === '') {
            abort(404, 'File not found: ' . ($fname ?: '(walang naka-save na filename sa order na ito)'));
        }

        $mimes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'pdf' => 'application/pdf', 'txt' => 'text/plain',
        ];
        $ext = strtolower(pathinfo($found, PATHINFO_EXTENSION));

        return response()->file($found, [
            'Content-Type' => $mimes[$ext] ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $fname . '"',
        ]);
    }

    /* ---------------- Orders ---------------- */

    public function updateOrderStatus(Request $request)
    {
        $orderId = (int) $request->input('order_id');
        $newStatus = trim((string) $request->input('status'));

        $prev = DB::table('orders')->where('order_id', $orderId)->first();

        $proof = null;
        if ($newStatus === 'Successful Pickup') {
            $proof = $this->saveUpload($request, 'proof_image', 'proof', ['jpg', 'jpeg', 'png', 'webp']);
        }

        $data = ['status' => $newStatus];
        if ($proof) { $data['proof_image'] = $proof; }
        DB::table('orders')->where('order_id', $orderId)->update($data);

        // Bawas sa inventory (isang beses lang, kapag kakatapos lang ng transaction)
        if ($newStatus === 'Successful Pickup' && $prev && trim((string) $prev->status) !== 'Successful Pickup') {
            $this->deductInventory($prev);
        }

        return $this->backTo($newStatus === 'Successful Pickup' ? 'history' : 'orders');
    }

    public function deleteOrderHistory(Request $request)
    {
        DB::table('orders')->where('order_id', (int) $request->input('order_id'))->delete();
        return $this->backTo('history');
    }

    /* ---------------- Inventory ---------------- */

    public function addStock(Request $request)
    {
        DB::table('inventory')->insert([
            'item_name' => trim((string) $request->input('item_name')),
            'category' => trim((string) $request->input('category')),
            'stock_quantity' => (int) $request->input('stock_quantity'),
            'unit' => trim((string) $request->input('unit')),
        ]);
        return $this->backTo('inventory');
    }

    public function updateStock(Request $request)
    {
        DB::table('inventory')
            ->where('item_id', (int) $request->input('item_id'))
            ->update(['stock_quantity' => (int) $request->input('stock_quantity')]);
        return $this->backTo('inventory');
    }

    public function deleteStock(Request $request)
    {
        DB::table('inventory')->where('item_id', (int) $request->input('item_id'))->delete();
        return $this->backTo('inventory');
    }

    /* ---------------- Services / Products ---------------- */

    public function addService(Request $request)
    {
        $image = $this->saveUpload($request, 'product_image', 'prod', ['jpg', 'jpeg', 'png', 'webp']);

        DB::table('services_offered')->insert([
            'service_name' => trim((string) $request->input('service_name')),
            'description' => trim((string) $request->input('description')),
            'price' => (float) $request->input('price'),
            'product_image' => $image,
        ]);
        return $this->backTo('services');
    }

    public function updateService(Request $request)
    {
        $data = [
            'service_name' => trim((string) $request->input('service_name')),
            'description' => trim((string) $request->input('description')),
            'price' => (float) $request->input('price'),
            'status' => trim((string) $request->input('status')),
        ];
        $image = $this->saveUpload($request, 'product_image', 'prod', ['jpg', 'jpeg', 'png', 'webp']);
        if ($image) { $data['product_image'] = $image; }

        DB::table('services_offered')->where('service_id', (int) $request->input('service_id'))->update($data);
        return $this->backTo('services');
    }

    public function deleteService(Request $request)
    {
        DB::table('services_offered')->where('service_id', (int) $request->input('service_id'))->delete();
        return $this->backTo('services');
    }

    /* ---------------- Messages ---------------- */

    public function sendMessage(Request $request)
    {
        $customerId = (int) $request->input('customer_id');
        $msg = trim((string) $request->input('message'));
        $attachment = $this->saveUpload($request, 'chat_file', 'chat');

        if ($msg !== '' || $attachment) {
            DB::table('messages')->insert([
                'user_id' => $customerId,
                'sender' => 'admin',
                'message' => $msg,
                'attachment' => $attachment,
            ]);
        }
        return $this->backTo('messages', ['chat_user' => $customerId]);
    }

    /* ---------------- Notifications polling (JSON) ---------------- */

    public function notifications()
    {
        $out = [
            'pending' => 0,
            'last_id' => 0,
            'orders' => [],
            'msgs' => [],
        ];

        $out['pending'] = (int) DB::table('orders')
            ->where(function ($q) { $q->where('status', 'Pending')->orWhereNull('status')->orWhere('status', ''); })
            ->count();

        $out['last_id'] = (int) (DB::table('orders')->max('order_id') ?? 0);

        $out['orders'] = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->orderByDesc('orders.order_id')->limit(20)
            ->select('orders.order_id', 'orders.order_code', 'orders.product_name', 'orders.total', 'users.full_name')
            ->get();

        try {
            $out['msgs'] = DB::table('messages')
                ->join('users', 'messages.user_id', '=', 'users.user_id')
                ->where('messages.sender', '!=', 'admin')
                ->groupBy('messages.user_id', 'users.full_name')
                ->selectRaw('messages.user_id, users.full_name, COUNT(*) as c')
                ->get();
        } catch (Throwable $e) {
            $out['msgs_error'] = $e->getMessage();
        }

        return response()->json($out)->header('Cache-Control', 'no-store');
    }

    /* ---------------- Inventory deduction (Successful Pickup) ---------------- */

    private function deductInventory(object $order): void
    {
        $sheetsPerReam = 500;
        $qty     = max(1, (int) ($order->quantity ?? 1));
        $pages   = (int) ($order->pages_count ?? 0);
        $product = trim((string) ($order->product_name ?? ''));
        $paper   = trim((string) ($order->paper_size ?? ''));

        try {
            if (!Schema::hasTable('inventory_sheet_usage')) {
                DB::statement('CREATE TABLE IF NOT EXISTS inventory_sheet_usage (item_id INT NOT NULL PRIMARY KEY, sheets_used INT NOT NULL DEFAULT 0)');
            }

            DB::transaction(function () use ($sheetsPerReam, $qty, $pages, $product, $paper) {
                $item = null;
                $isSheets = false;
                $amount = 0;

                if ($product !== '') {
                    $row = DB::table('inventory')->whereRaw('LOWER(item_name) = ?', [mb_strtolower($product)])->first();
                    if ($row) { $item = $row; $amount = $qty; }
                }

                if (!$item && $pages > 0) {
                    $isSheets = true;
                    $amount = $pages * $qty;

                    $sizeWord = '';
                    if ($paper !== '') {
                        $parts = preg_split('/[\s(]+/', strtolower($paper), -1, PREG_SPLIT_NO_EMPTY);
                        $sizeWord = $parts[0] ?? '';
                    }

                    if ($sizeWord !== '') {
                        $item = DB::table('inventory')
                            ->whereRaw('LOWER(item_name) LIKE ?', ['%' . $sizeWord . '%'])
                            ->where(function ($q) {
                                $q->whereRaw("LOWER(item_name) LIKE '%paper%'")->orWhereRaw("LOWER(category) LIKE '%paper%'");
                            })
                            ->orderByDesc('stock_quantity')->first();
                    }
                    if (!$item) {
                        $item = DB::table('inventory')
                            ->where(function ($q) {
                                $q->whereRaw("LOWER(item_name) LIKE '%paper%'")->orWhereRaw("LOWER(category) LIKE '%paper%'");
                            })
                            ->orderByDesc('stock_quantity')->first();
                    }
                }

                if (!$item || $amount <= 0) { return; }

                $deduct = $amount;
                if ($isSheets && str_contains(strtolower((string) $item->unit), 'ream')) {
                    $prev = DB::table('inventory_sheet_usage')->where('item_id', $item->item_id)->value('sheets_used');
                    $total = ($prev === null ? 0 : (int) $prev) + $amount;
                    $deduct = intdiv($total, $sheetsPerReam);
                    $remain = $total % $sheetsPerReam;

                    DB::table('inventory_sheet_usage')->updateOrInsert(
                        ['item_id' => $item->item_id],
                        ['sheets_used' => $remain]
                    );
                }

                if ($deduct > 0) {
                    $newStock = max(0, (int) $item->stock_quantity - $deduct);
                    DB::table('inventory')->where('item_id', $item->item_id)->update(['stock_quantity' => $newStock]);
                }
            });
        } catch (Throwable $e) {
            Log::error('Inventory deduction failed: ' . $e->getMessage());
        }
    }
}