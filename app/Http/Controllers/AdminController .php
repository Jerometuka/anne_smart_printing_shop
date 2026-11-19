<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /* =====================================================================
       Helpers
       ===================================================================== */

    // if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') -> admin_login
    private function guard()
    {
        if (!session()->has('user_id') || session('role') !== 'admin') {
            return redirect()->route('admin.login');
        }
        return null;
    }

    // $pdo->query(...)->fetchAll()  (array ng arrays, gaya ng PDO::FETCH_ASSOC)
    private function rows(string $sql, array $bindings = []): array
    {
        return array_map(fn ($r) => (array) $r, DB::select($sql, $bindings));
    }

    private function uploadsDir(): string
    {
        $dir = public_path('uploads');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    // Helpers para sa file na inupload ng customer (para maprint)
    private function orderFile($o): string
    {
        foreach (['document_file', 'file_path', 'file_name', 'uploaded_file', 'file', 'document', 'attachment'] as $k) {
            if (!empty($o[$k])) return (string) $o[$k];
        }
        return '';
    }

    private function findUploadedFile($fname): string
    {
        $fname = basename((string) $fname);
        if ($fname === '') return '';

        $bases = [base_path(), public_path()];
        $subs = ['uploads/', 'uploads/orders/', 'uploads/documents/', 'uploads/files/', 'uploads/docs/', 'storage/', 'storage/uploads/',
                 'storage/app/public/', 'storage/app/public/uploads/', 'public/uploads/', 'public/storage/', 'public/storage/uploads/', 'files/', 'images/', ''];

        foreach ($bases as $b) {
            foreach ($subs as $sub) {
                $path = $b . '/' . $sub . $fname;
                if (is_file($path)) return $path;
            }
        }

        // Huling paraan: hanapin sa loob ng mga folder ng project
        $skip = ['vendor', 'node_modules', '.git'];
        foreach ($bases as $root) {
            try {
                $it = new \RecursiveIteratorIterator(new \RecursiveCallbackFilterIterator(
                    new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                    function ($cur) use ($skip) { return !($cur->isDir() && in_array($cur->getFilename(), $skip, true)); }
                ), \RecursiveIteratorIterator::LEAVES_ONLY);
                $it->setMaxDepth(5);
                foreach ($it as $f) {
                    if ($f->isFile() && $f->getFilename() === $fname) return $f->getPathname();
                }
            } catch (\Throwable $e) { }
        }
        return '';
    }

    /* =====================================================================
       GET /admin  (dashboard + pag-serve ng order file)
       ===================================================================== */
    public function index(Request $request)
    {
        if ($r = $this->guard()) return $r;

        // ===== Ihatid ang file na inupload ng customer (admin lang) =====
        if ($request->query('order_file') !== null) {
            $oid = (int) $request->query('order_file');
            $row = $this->rows("SELECT * FROM orders WHERE order_id = ?", [$oid])[0] ?? null;
            $fname = $row ? basename($this->orderFile($row)) : '';
            $found = $fname !== '' ? $this->findUploadedFile($fname) : '';

            if ($found === '') {
                return response('File not found: ' . e($fname ?: '(walang naka-save na filename sa order na ito)'), 404);
            }

            $mimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
                      'pdf' => 'application/pdf', 'txt' => 'text/plain', 'doc' => 'application/msword',
                      'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $ext = strtolower(pathinfo($found, PATHINFO_EXTENSION));

            return response()->file($found, [
                'Content-Type'           => $mimes[$ext] ?? 'application/octet-stream',
                'Content-Disposition'    => 'inline; filename="' . $fname . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $active_orders = $this->rows("SELECT orders.*, users.full_name FROM orders JOIN users ON orders.user_id = users.user_id WHERE orders.status != 'Successful Pickup' ORDER BY orders.order_id ASC");

        $order_history = $this->rows("SELECT orders.*, users.full_name FROM orders JOIN users ON orders.user_id = users.user_id WHERE orders.status = 'Successful Pickup' ORDER BY orders.order_id DESC");

        $inventory_items = $this->rows("SELECT * FROM inventory ORDER BY item_name ASC");

        $services_items = $this->rows("SELECT * FROM services_offered ORDER BY service_id ASC");

        $stats = $this->rows("SELECT SUM(total) as total_sales, COUNT(*) as total_orders FROM orders")[0] ?? [];
        $total_sales = $stats['total_sales'] ?? 0;

        $current_month = date('m');
        $current_year = date('Y');
        $month_data = $this->rows("SELECT SUM(total) as month_total FROM orders WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?", [$current_month, $current_year])[0] ?? [];
        $actual_monthly_sales = $month_data['month_total'] ?? 0;

        $monthly_target = 25000;
        $monthly_percentage = ($actual_monthly_sales / $monthly_target) * 100;
        if ($monthly_percentage > 100) { $monthly_percentage = 100; }

        $monthly_sales = array_fill(1, 12, 0);
        foreach ($this->rows("SELECT MONTH(created_at) as m, SUM(total) as total FROM orders GROUP BY MONTH(created_at)") as $row) {
            if ($row['m'] !== null) {
                $monthly_sales[(int) $row['m']] = floatval($row['total']);
            }
        }
        $monthly_sales_array = array_values($monthly_sales);

        $customers_with_chats = $this->rows("SELECT DISTINCT users.user_id, users.full_name, users.email FROM users JOIN messages ON users.user_id = messages.user_id WHERE users.role = 'customer'");

        $selected_chat_user = $request->query('chat_user', $customers_with_chats[0]['user_id'] ?? null);
        $chat_messages = [];
        if ($selected_chat_user) {
            $chat_messages = $this->rows("SELECT * FROM messages WHERE user_id = ? ORDER BY created_at ASC", [$selected_chat_user]);
        }

        $orderFile = fn ($o) => $this->orderFile($o);

        return view('admin', compact(
            'active_orders',
            'order_history',
            'inventory_items',
            'services_items',
            'total_sales',
            'actual_monthly_sales',
            'monthly_percentage',
            'monthly_sales_array',
            'customers_with_chats',
            'selected_chat_user',
            'chat_messages',
            'orderFile'
        ));
    }

    /* =====================================================================
       GET /admin/notifications  (JSON para sa notification bell ng admin)
       ===================================================================== */
    public function notifications(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'unauthorized'], 401)->header('Cache-Control', 'no-store');
        }
        if (session('role', '') !== 'admin') {
            return response()->json(['error' => 'forbidden'], 403)->header('Cache-Control', 'no-store');
        }

        $stats = DB::table('orders')
            ->selectRaw("COALESCE(MAX(order_id), 0) AS last_id, SUM(CASE WHEN status = 'Pending' OR status = '' OR status IS NULL THEN 1 ELSE 0 END) AS pending")
            ->first();

        $latest = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->select('orders.order_id', 'orders.order_code', 'orders.product_name', 'orders.total', 'users.full_name')
            ->orderByDesc('orders.order_id')
            ->limit(15)
            ->get();

        $msgs = [];
        $msgs_error = null;
        try {
            $rawMsgs = DB::table('messages as m')
                ->join('users as u', 'm.user_id', '=', 'u.user_id')
                ->whereRaw("LOWER(TRIM(m.sender)) <> 'admin'")
                ->groupBy('m.user_id', 'u.full_name')
                ->selectRaw('m.user_id, u.full_name, COUNT(*) AS c')
                ->get();
            foreach ($rawMsgs as $mm) {
                $msgs[] = [
                    'user_id'   => (int) $mm->user_id,
                    'full_name' => $mm->full_name,
                    'c'         => (int) $mm->c,
                ];
            }
        } catch (\Throwable $e) {
            $msgs_error = $e->getMessage();
        }

        return response()->json([
            'last_id'    => (int) ($stats->last_id ?? 0),
            'pending'    => (int) ($stats->pending ?? 0),
            'orders'     => $latest,
            'msgs'       => $msgs,
            'msgs_error' => $msgs_error,
        ])->header('Cache-Control', 'no-store');
    }

    /* =====================================================================
       ORDERS  (update_order_status / delete_order_history)
       ===================================================================== */
    public function updateOrderStatus(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $order_id = (int) $request->input('order_id');
        $new_status = trim((string) $request->input('status'));

        $proof_filename = null;
        if ($new_status === 'Successful Pickup' && $request->hasFile('proof_image') && $request->file('proof_image')->isValid()) {
            $file = $request->file('proof_image');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                $proof_filename = "proof_" . time() . "_" . uniqid() . "." . $file_ext;
                $file->move($this->uploadsDir(), $proof_filename);
            }
        }

        if ($proof_filename) {
            DB::update("UPDATE orders SET status = ?, proof_image = ? WHERE order_id = ?", [$new_status, $proof_filename, $order_id]);
        } else {
            DB::update("UPDATE orders SET status = ? WHERE order_id = ?", [$new_status, $order_id]);
        }

        if ($new_status === 'Successful Pickup') {
            return redirect()->route('admin.index', ['tab' => 'history']);
        }
        return redirect()->route('admin.index', ['tab' => 'orders']);
    }

    public function deleteOrderHistory(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $order_id = (int) $request->input('order_id');
        DB::delete("DELETE FROM orders WHERE order_id = ?", [$order_id]);

        return redirect()->route('admin.index', ['tab' => 'history']);
    }

    /* =====================================================================
       INVENTORY  (add_stock / update_stock / delete_stock)
       ===================================================================== */
    public function addStock(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $name = trim((string) $request->input('item_name'));
        $cat  = trim((string) $request->input('category'));
        $qty  = (int) $request->input('stock_quantity');
        $unit = trim((string) $request->input('unit'));
        DB::insert("INSERT INTO inventory (item_name, category, stock_quantity, unit) VALUES (?, ?, ?, ?)", [$name, $cat, $qty, $unit]);

        return redirect()->route('admin.index', ['tab' => 'inventory']);
    }

    public function updateStock(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $id  = (int) $request->input('item_id');
        $qty = (int) $request->input('stock_quantity');
        DB::update("UPDATE inventory SET stock_quantity = ? WHERE item_id = ?", [$qty, $id]);

        return redirect()->route('admin.index', ['tab' => 'inventory']);
    }

    public function deleteStock(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $id = (int) $request->input('item_id');
        DB::delete("DELETE FROM inventory WHERE item_id = ?", [$id]);

        return redirect()->route('admin.index', ['tab' => 'inventory']);
    }

    /* =====================================================================
       SERVICES  (add_service / update_service / delete_service)
       ===================================================================== */
    private function saveProductImage(Request $request): ?string
    {
        $product_image = null;
        if ($request->hasFile('product_image') && $request->file('product_image')->isValid()) {
            $file = $request->file('product_image');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                $product_image = "prod_" . time() . "_" . uniqid() . "." . $file_ext;
                $file->move($this->uploadsDir(), $product_image);
            }
        }
        return $product_image;
    }

    public function addService(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $sname  = trim((string) $request->input('service_name'));
        $sdesc  = trim((string) $request->input('description'));
        $sprice = floatval($request->input('price'));

        $product_image = $this->saveProductImage($request);

        DB::insert("INSERT INTO services_offered (service_name, description, price, product_image) VALUES (?, ?, ?, ?)", [$sname, $sdesc, $sprice, $product_image]);

        return redirect()->route('admin.index', ['tab' => 'services']);
    }

    public function updateService(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $sid     = (int) $request->input('service_id');
        $sname   = trim((string) $request->input('service_name'));
        $sdesc   = trim((string) $request->input('description'));
        $sprice  = floatval($request->input('price'));
        $sstatus = trim((string) $request->input('status'));

        $product_image = $this->saveProductImage($request);

        if ($product_image) {
            DB::update("UPDATE services_offered SET service_name = ?, description = ?, price = ?, status = ?, product_image = ? WHERE service_id = ?", [$sname, $sdesc, $sprice, $sstatus, $product_image, $sid]);
        } else {
            DB::update("UPDATE services_offered SET service_name = ?, description = ?, price = ?, status = ? WHERE service_id = ?", [$sname, $sdesc, $sprice, $sstatus, $sid]);
        }

        return redirect()->route('admin.index', ['tab' => 'services']);
    }

    public function deleteService(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $sid = (int) $request->input('service_id');
        DB::delete("DELETE FROM services_offered WHERE service_id = ?", [$sid]);

        return redirect()->route('admin.index', ['tab' => 'services']);
    }

    /* =====================================================================
       MESSAGES  (send_admin_message)
       ===================================================================== */
    public function sendMessage(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $customer_id = (int) $request->input('customer_id');
        $msg = trim((string) $request->input('message'));

        $attachment = null;
        if ($request->hasFile('chat_file') && $request->file('chat_file')->isValid()) {
            $file = $request->file('chat_file');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $attachment = "chat_" . time() . "_" . uniqid() . "." . $file_ext;
            $file->move($this->uploadsDir(), $attachment);
        }

        if (!empty($msg) || $attachment) {
            DB::insert("INSERT INTO messages (user_id, sender, message, attachment) VALUES (?, 'admin', ?, ?)", [$customer_id, $msg, $attachment]);
        }

        return redirect()->route('admin.index', ['tab' => 'messages', 'chat_user' => $customer_id]);
    }
}