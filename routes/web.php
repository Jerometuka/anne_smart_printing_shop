<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ServiceController;

/* =========================================================================
1. AUTH HELPER FUNCTIONS (includes/auth.php)
   ========================================================================= */
if (!function_exists('checkCustomer')) {
    function checkCustomer() {
        if (!session()->has('user_id') || session('role') !== 'customer') {
            return redirect()->route('login')->send();
        }
    }
}

if (!function_exists('checkAdmin')) {
    function checkAdmin() {
        if (!session()->has('user_id') || session('role') !== 'admin') {
            return redirect()->route('login')->send();
        }
    }
}

/* =========================================================================
2. ADMIN LOGIN + ADMIN DASHBOARD ROUTES
   (Dashboard = http://localhost:8000/admin)
   ========================================================================= */
Route::get('/admin/login', function () {
    $error = "";
    return view('admin_login', compact('error'));
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $email = trim((string) $request->input('email'));
    $password = (string) $request->input('password');

    $user = DB::table('users')->where('email', $email)->first();
    $hash = $user->password_hash ?? $user->password ?? null;

    if ($user && $hash && password_verify($password, $hash) && ($user->role ?? '') === 'admin') {
        $request->session()->regenerate();
        session([
            'user_id'   => $user->user_id ?? $user->id,
            'user_name' => $user->full_name ?? $user->name ?? '',
            'role'      => $user->role,
        ]);
        return redirect()->route('admin.dashboard');
    }

    $error = "Maling admin credentials o walang pahintulot!";
    return view('admin_login', compact('error'));
})->name('admin.login.submit');

// Admin-only (EnsureAdmin = session user_id + role 'admin'; walang kailangang alias sa bootstrap/app.php)
Route::middleware(EnsureAdmin::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('orders/{order}/file', [OrderController::class, 'file'])->name('orders.file');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Inventory
    Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('inventory/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Services / Products
    Route::post('services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Messages
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');

    // Notification polling (JSON)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
});

/* =========================================================================
                3. CHECKOUT & PAYMENT ROUTES (checkout.php)
   ========================================================================= */
Route::post('/checkout', function (Request $request) {
    checkCustomer();

    $product_type = $request->input('product_type', 'paper');
    $specifications = trim((string) $request->input('specifications', ''));
    $is_hq = $request->has('is_high_quality') ? 1 : 0;

    $total_amount = 0;
    $details_summary = "";

    // File Upload Handling
    $uploaded_file_name = "";
    if ($request->hasFile('document_file') && $request->file('document_file')->isValid()) {
        $file = $request->file('document_file');
        $upload_dir = public_path('uploads');
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $safe_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $file->getClientOriginalName());
        $file->move($upload_dir, $safe_name);
        $uploaded_file_name = $safe_name;
    }

    // Pricing Calculation Logic
    if ($product_type === 'paper') {
        $pages = max(1, (int) $request->input('pages_count', 1));
        $size = $request->input('paper_size', 'A4');

        if ($pages <= 2 && $size !== 'Long' && !$is_hq) {
            $total_amount = 0.00;
            $details_summary = "Paper Print ($size) - $pages page(s) [FREE 2-Page Promo]";
        } else {
            $rate = $pages >= 21 ? 2.50 : ($pages >= 11 ? 3.00 : 4.00);
            $std_extra = ($size === 'Long') ? 0.25 : 0.00;
            $hq_extra = $is_hq ? 1.00 : 0.00;
            $total_amount = $pages * ($rate + $std_extra + $hq_extra);
            $hq_label = $is_hq ? " [High Quality]" : "";
            $details_summary = "Paper Print ($size) - $pages page(s)$hq_label";
        }
    } elseif ($product_type === 'xerox') {
        $pages = max(1, (int) $request->input('xerox_pages', 1));
        $copies = max(1, (int) $request->input('xerox_copies', 1));
        $total_amount = $pages * $copies * 3.00;
        $details_summary = "Xerox - $pages page(s) x $copies cop(ies) @ ₱3.00";
    } elseif ($product_type === 'photo') {
        $size = $request->input('photo_size', '4R');
        $qty = max(1, (int) $request->input('photo_qty', 1));
        $rate = ($size === '4R') ? 10.00 : 50.00;
        $hq_extra = $is_hq ? 5.00 : 0.00;
        $total_amount = $qty * ($rate + $hq_extra);
        $hq_label = $is_hq ? " [High Quality]" : "";
        $details_summary = "Photo Paper ($size) - $qty pc(s)$hq_label";
    } elseif ($product_type === 'tarp') {
        $w = max(1, (float) $request->input('tarp_width', 2));
        $h = max(1, (float) $request->input('tarp_height', 3));
        $qty = max(1, (int) $request->input('tarp_qty', 1));
        $rate = $is_hq ? 18.00 : 15.00;
        $total_amount = ($w * $h) * $rate * $qty;
        $hq_label = $is_hq ? " [High Quality]" : "";
        $details_summary = "Tarpaulin ({$w}ft x {$h}ft) - $qty pc(s)$hq_label";
    } elseif ($product_type === 'sticker') {
        $size = $request->input('sticker_size', 'A4');
        $qty = max(1, (int) $request->input('sticker_qty', 1));
        $rate = ($size === 'A4') ? 35.00 : 65.00;
        $hq_extra = $is_hq ? 10.00 : 0.00;
        $total_amount = $qty * ($rate + $hq_extra);
        $hq_label = $is_hq ? " [High Quality]" : "";
        $details_summary = "Vinyl Sticker ($size) - $qty sheet(s)$hq_label";
    }

    if ($specifications !== '') {
        $details_summary .= " | Note: " . $specifications;
    }

    return view('checkout', compact(
        'product_type',
        'details_summary',
        'total_amount',
        'uploaded_file_name',
        'specifications'
    ));
})->name('checkout');

Route::post('/payment-process', function (Request $request) {
    checkCustomer();

    $userId = session('user_id') ?? session('id');
    if (!$userId) {
        return redirect()->route('login');
    }

    $fileName = $request->input('uploaded_doc'); 
    if ($request->hasFile('document_file') && $request->file('document_file')->isValid()) {
        $file = $request->file('document_file');
        $fileName = 'doc_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $fileName);
    }

    $proofImage = null;
    if ($request->hasFile('proof_payment') && $request->file('proof_payment')->isValid()) {
        $proofFile = $request->file('proof_payment');
        $proofImage = 'proof_' . time() . '_' . uniqid() . '.' . $proofFile->getClientOriginalExtension();
        $proofFile->move(public_path('uploads'), $proofImage);
    }

    DB::table('orders')->insert([
        'user_id'          => $userId,
        'order_code'       => 'SP-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        'product_name'     => $request->input('product_name', 'Standard Print Service'),
        'document_file'    => $fileName, 
        'proof_image'      => $proofImage, 
        'pages_count'      => (int) $request->input('pages_count', 1),
        'paper_size'       => $request->input('paper_size', 'A4'),
        'quantity'         => (int) $request->input('photo_qty', 1),
        'specifications'   => $request->input('specifications', ''),
        'is_high_quality'  => $request->has('is_high_quality') ? 1 : 0,
        'estimated_time'   => 'Within 20 mins',
        'total'            => (float) $request->input('total_amount', 0.00),
        'payment_method'   => $request->input('payment_method', 'cash_on_pickup'),
        'payment_status'   => $request->input('payment_method') === 'cash_on_pickup' ? 'Unpaid' : 'Paid',
        'status'           => 'Pending',
        'created_at'       => now(),
    ]);

    return redirect()->route('my.orders')->with('ordered', 'success');
})->name('payment.process');

/* =========================================================================
4. DEBUG ORDERS & DELETE ORDER ROUTES
   ========================================================================= */
Route::get('/debug-orders', function () {
    checkAdmin();
    $orders = DB::table('orders')->orderByDesc('order_id')->get();
    return response()->json($orders);
})->name('debug.orders');

Route::match(['get', 'post'], '/delete-order', function (Request $request) {
    if (!session()->has('user_id')) {
        return redirect()->route('login');
    }

    $uid = (int) session('user_id');
    $role = session('role', '');
    $order_id = (int) ($request->input('order_id') ?? $request->query('id', 0));

    $order = DB::table('orders')->where('order_id', $order_id)->first();
    if ($order && ($role === 'admin' || (int) $order->user_id === $uid)) {
        if (!empty($order->document_file)) {
            $filePath = public_path('uploads/' . basename($order->document_file));
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
        DB::table('orders')->where('order_id', $order_id)->delete();
    }

    return redirect()->back();
})->name('order.delete.get');

/* =========================================================================
5. ORDER FILE SERVING ROUTE
   ========================================================================= */
Route::get('/order-file', function (Request $request) {
    if (!session()->has('user_id')) {
        return response('Forbidden', 403);
    }

    $uid     = (int) session('user_id');
    $role    = session('role', '');
    $orderId = (int) $request->query('order', 0);

    $o = DB::table('orders')->select('user_id', 'document_file')->where('order_id', $orderId)->first();

    if (!$o || ($role !== 'admin' && (int) $o->user_id !== $uid)) {
        return response('Not found', 404);
    }

    $name = basename(trim((string) $o->document_file));
    if ($name === '') {
        return response('No file', 404);
    }

    $dir  = public_path('uploads') . DIRECTORY_SEPARATOR;
    $path = $dir . $name;

    if (!is_file($path)) {
        $path = '';
        foreach ((array) @scandir($dir) as $f) {
            if (strcasecmp($f, $name) === 0) { $path = $dir . $f; break; }
        }
    }
    if ($path === '' || !is_file($path)) {
        return response('File missing: ' . htmlspecialchars($name), 404);
    }

    $ext   = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mimes = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'webp' => 'image/webp', 'gif' => 'image/gif', 'pdf' => 'application/pdf',
        'txt' => 'text/plain', 'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];
    if (!isset($mimes[$ext])) {
        return response('File type not allowed', 403);
    }

    $inline = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'txt'], true);

    return response()->file($path, [
        'Content-Type' => $mimes[$ext],
        'Content-Length' => filesize($path),
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control' => 'private, max-age=3600',
        'Content-Disposition' => ($inline ? 'inline' : 'attachment') . '; filename="' . $name . '"',
    ]);
})->name('order.file');

/* =========================================================================
6. CUSTOMER PORTAL HOME ROUTE
   ========================================================================= */
Route::get('/', function (Request $request) {
    checkCustomer();

    $message = "";
    $ordered_success = false;
    if ($request->query('ordered') === 'success') {
        $ordered_success = true;
    } else if ($request->query('payment') === 'cancelled') {
        $message = "Payment process was cancelled.";
    }

    $user_id = session('user_id') ?? session('id') ?? 0;
    $logged_user = DB::table('users')->select('profile_pic', 'full_name')->where('user_id', $user_id)->first();

    return view('index', compact('message', 'ordered_success', 'user_id', 'logged_user'));
})->name('home');

/* =========================================================================
7. NOTIFICATIONS POLLING ENDPOINT (customer side; admin gumagamit na ng /admin/notifications)
   ========================================================================= */
Route::get('/notifications', function (Request $request) {
    if (!session()->has('user_id')) {
        return response()->json(['error' => 'unauthorized'], 401)->header('Cache-Control', 'no-store');
    }

    $for = $request->query('for', 'customer');
    $sess_role = session('role', '');
    $sess_uid  = session('user_id') ?? session('id') ?? 0;

    if ($for === 'admin') {
        if ($sess_role !== 'admin') {
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
                    'user_id' => (int) $mm->user_id,
                    'full_name' => $mm->full_name,
                    'c' => (int) $mm->c,
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

    $user_id = $sess_uid;
    $orders = DB::table('orders')
        ->where('user_id', $user_id)
        ->orderByDesc('order_id')
        ->limit(30)
        ->select('order_id', 'order_code', 'product_name', 'status')
        ->get()
        ->map(function ($o) {
            $o->order_id = (int) $o->order_id;
            $o->status = trim((string) $o->status) === '' ? 'Pending' : trim((string) $o->status);
            return $o;
        });

    $admin_msgs = (int) DB::table('messages')
        ->where('user_id', $user_id)
        ->where('sender', 'admin')
        ->count();

    return response()->json([
        'orders' => $orders,
        'admin_msgs' => $admin_msgs,
    ])->header('Cache-Control', 'no-store');
})->name('notifications.fetch');

/* =========================================================================
8. LOGIN & LOGOUT ROUTES
   ========================================================================= */
Route::get('/login', function () {
    $error = "";
    return view('login', compact('error'));
})->name('login');

Route::post('/login', function (Request $request) {
    $error = "";

    if ($request->has('login')) {
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        $user = DB::table('users')->where('email', $email)->first();

        if ($user && password_verify($password, $user->password_hash)) {
            session([
                'user_id' => $user->user_id,
                'user_name' => $user->full_name,
                'role' => $user->role,
            ]);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('home');
            }
        } else {
            $error = "Maling email o password!";
        }
    }

    return view('login', compact('error'));
})->name('login.submit');

// GET (link) at POST (logout button ng bagong admin sidebar)
Route::match(['get', 'post'], '/logout', function (Request $request) {
    $request->session()->flush();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/* =========================================================================
9. REGISTER ROUTES
   ========================================================================= */
Route::get('/register', function () {
    $error = "";
    $success = "";
    return view('register', compact('error', 'success'));
})->name('register');

Route::post('/register', function (Request $request) {
    $error = "";
    $success = "";

    $full_name = trim((string) $request->input('full_name', ''));
    $email = trim((string) $request->input('email', ''));
    $password = (string) $request->input('password', '');
    $role = 'customer';

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Lahat ng fields ay kinakailangan punan!";
    } else {
        $exists = DB::table('users')->where('email', $email)->exists();
        if ($exists) {
            $error = "Ang email na ito ay rehistrado na!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $inserted = DB::table('users')->insert([
                'full_name' => $full_name,
                'email' => $email,
                'password_hash' => $hashed_password,
                'role' => $role,
            ]);

            if ($inserted) {
                $success = "Matagumpay ang pag-rehistro! Maaari ka nang mag-log in.";
            } else {
                $error = "Nagkaroon ng problema sa pag-rehistro. Subukan ulit.";
            }
        }
    }

    return view('register', compact('error', 'success'));
})->name('register.submit');

/* =========================================================================
10. SEND MESSAGE ROUTE
   ========================================================================= */
Route::post('/send-message', function (Request $request) {
    checkCustomer();

    $user_id = session('user_id');
    $message = trim((string) $request->input('message', ''));

    if (!empty($message)) {
        DB::table('messages')->insert([
            'user_id' => $user_id,
            'sender' => 'customer',
            'message' => $message,
        ]);
    }

    return redirect()->route('home', ['tab' => 'messages']);
})->name('message.send');

/* =========================================================================
11. TEST LOGIN DIAGNOSTIC ROUTE
   ========================================================================= */
Route::get('/test-login', function () {
    $email = 'admin@smartprint.com';
    $password = 'password';

    $user = DB::table('users')->where('email', $email)->first();

    $output = "";
    if (!$user) {
        $output .= "❌ HINDI NAKITA ANG EMAIL: Ang email na '$email' ay walang record sa database.";
    } else {
        $output .= "✅ NAKITA ANG EMAIL sa database!<br>";
        $output .= "Pangalan: " . htmlspecialchars($user->full_name) . "<br>";
        $output .= "Role sa Database: <b>" . htmlspecialchars($user->role) . "</b><br>";

        if ($user->role !== 'admin') {
            $output .= "⚠️ BABALA: Ang role ng account na ito ay hindi 'admin' kundi '" . htmlspecialchars($user->role) . "'.<br>";
        }

        if (password_verify($password, $user->password_hash)) {
            $output .= "✅ PASSWORD MATCH: Tama ang password!<br>";
        } else {
            $output .= "❌ PASSWORD MISMATCH: Mali ang password.<br>";
        }
    }

    return response($output);
})->name('test.login');

/* =========================================================================
12. MESSAGES / CUSTOMER SERVICE ROUTE
   ========================================================================= */
Route::get('/messages', function () {
    checkCustomer();

    $chat_messages = DB::table('messages')
        ->where('user_id', session('user_id'))
        ->orderBy('created_at', 'asc')
        ->get();

    $logged_user = DB::table('users')
        ->select('profile_pic')
        ->where('user_id', session('user_id'))
        ->first();

    return view('messages', compact('chat_messages', 'logged_user'));
})->name('messages');

/* =========================================================================
13. MY ORDERS ROUTE
   ========================================================================= */
Route::get('/my-orders', function () {
    checkCustomer();

    $all_orders = DB::table('orders')
        ->join('users', 'orders.user_id', '=', 'users.user_id')
        ->where('orders.user_id', session('user_id'))
        ->orderByDesc('orders.order_id')
        ->select('orders.*', 'users.full_name')
        ->get();

    $logged_user = DB::table('users')
        ->select('profile_pic')
        ->where('user_id', session('user_id'))
        ->first();

    $orderFile = function (array $o): string {
        foreach (['document_file', 'file_path', 'file_name', 'uploaded_file', 'file', 'document', 'attachment'] as $k) {
            if (!empty($o[$k])) return (string) $o[$k];
        }
        return '';
    };
    $isImageFile = function (string $f): bool {
        return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    };
    $statusMeta = function (string $s): array {
        $l = strtolower($s);
        if (strpos($l, 'cancel') !== false)                          return ['cancel', -1, 'text-rose-300 bg-rose-500/15 border-rose-500/40', '#f43f5e'];
        if (strpos($l, 'ready') !== false)                           return ['ready', 2, 'text-violet-300 bg-violet-500/15 border-violet-500/40', '#8b5cf6'];
        if (preg_match('/complete|done|claim|success|deliver/', $l)) return ['done', 3, 'text-emerald-300 bg-emerald-500/15 border-emerald-500/40', '#10b981'];
        if (preg_match('/process|print|progress/', $l))              return ['processing', 1, 'text-sky-300 bg-sky-500/15 border-sky-500/40', '#0ea5e9'];
        return ['pending', 0, 'text-amber-300 bg-amber-500/15 border-amber-500/40', '#f59e0b'];
    };

    return view('my_orders', compact('all_orders', 'logged_user', 'orderFile', 'isImageFile', 'statusMeta'));
})->name('my.orders');

/* =========================================================================
14. CUSTOMER PROFILE ROUTES
   ========================================================================= */
Route::get('/profile', function () {
    checkCustomer();

    $user_id = session('user_id');
    $user = DB::table('users')->where('user_id', $user_id)->first();
    $error = session('error', '');

    return view('profile', compact('user', 'error'));
})->name('profile');

Route::post('/profile', function (Request $request) {
    checkCustomer();

    $user_id = session('user_id');
    $phone = trim((string) $request->input('phone', ''));
    $address = trim((string) $request->input('address', ''));

    $old_data = DB::table('users')->where('user_id', $user_id)->first();
    $profile_pic = $old_data->profile_pic ?? '';
    $error = '';

    if ($request->hasFile('profile_pic') && $request->file('profile_pic')->isValid()) {
        $file = $request->file('profile_pic');
        $file_ext = strtolower($file->getClientOriginalExtension());

        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_ext;
            $upload_dir = public_path('uploads/profiles/');

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if ($file->move($upload_dir, $new_filename)) {
                if (!empty($old_data->profile_pic) && is_file($upload_dir . $old_data->profile_pic)) {
                    @unlink($upload_dir . $old_data->profile_pic);
                }
                $profile_pic = $new_filename;
            }
        } else {
            $error = "Invalid image format. Only JPG, JPEG, PNG, and WEBP are allowed.";
        }
    }

    if (empty($error)) {
        DB::table('users')->where('user_id', $user_id)->update([
            'phone' => $phone,
            'address' => $address,
            'profile_pic' => $profile_pic,
        ]);

        return redirect()->route('home');
    }

    return redirect()->route('profile')->with('error', $error);
})->name('profile.submit');


/* =========================================================================
15. MGA BAGONG DAGDAG NA MGA RUTA (EXTRA FEATURES)
   ========================================================================= */

// Dagdag: Pagkansela ng Order ng Customer (kung Pending pa)
Route::post('/order/cancel', function (Request $request) {
    checkCustomer();
    $order_id = (int) $request->input('order_id');
    $user_id = session('user_id');

    $order = DB::table('orders')->where('order_id', $order_id)->where('user_id', $user_id)->first();
    if ($order && strtolower(trim($order->status)) === 'pending') {
        DB::table('orders')->where('order_id', $order_id)->update(['status' => 'Cancelled']);
    }

    return redirect()->route('my.orders')->with('success', 'Ang order ay matagumpay na nakansela.');
})->name('order.cancel');

// Dagdag: Pag-update ng Password ng User o Admin
Route::post('/profile/password', function (Request $request) {
    if (!session()->has('user_id')) {
        return redirect()->route('login');
    }

    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6',
    ]);

    $user_id = session('user_id');
    $user = DB::table('users')->where('user_id', $user_id)->first();

    if ($user && password_verify($request->input('current_password'), $user->password_hash)) {
        DB::table('users')->where('user_id', $user_id)->update([
            'password_hash' => password_hash($request->input('new_password'), PASSWORD_DEFAULT)
        ]);
        return back()->with('success', 'Matagumpay na na-update ang iyong password!');
    }

    return back()->with('error', 'Mali ang iyong kasalukuyang password.');
})->name('profile.password');