<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $for = $request->query('for', 'customer');
        $user = Auth::user();

        if ($for === 'admin') {
            if ($user->role !== 'admin') {
                return response()->json(['error' => 'forbidden'], 403);
            }

            $pending = (int) DB::table('orders')
                ->where(function ($q) {
                    $q->where('status', 'Pending')->orWhereNull('status')->orWhere('status', '');
                })->count();

            $lastId = (int) (DB::table('orders')->max('order_id') ?? 0);

            $latest = DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.user_id')
                ->orderByDesc('orders.order_id')
                ->limit(15)
                ->select('orders.order_id', 'orders.order_code', 'orders.product_name', 'orders.total', 'users.full_name')
                ->get();

            $msgs = [];
            $msgs_error = null;
            try {
                $msgs = DB::table('messages as m')
                    ->join('users as u', 'm.user_id', '=', 'u.user_id')
                    ->whereRaw("LOWER(TRIM(m.sender)) <> 'admin'")
                    ->groupBy('m.user_id', 'u.full_name')
                    ->selectRaw('m.user_id, u.full_name, COUNT(*) as c')
                    ->get()
                    ->map(function ($mm) {
                        $mm->user_id = (int) $mm->user_id;
                        $mm->c = (int) $mm->c;
                        return $mm;
                    });
            } catch (Throwable $e) {
                $msgs_error = $e->getMessage();
            }

            return response()->json([
                'last_id'    => $lastId,
                'pending'    => $pending,
                'orders'     => $latest,
                'msgs'       => $msgs,
                'msgs_error' => $msgs_error,
            ])->header('Cache-Control', 'no-store');
        }

        // Customer
        $userId = Auth::id();
        $orders = DB::table('orders')
            ->where('user_id', $userId)
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
            ->where('user_id', $userId)
            ->where('sender', 'admin')
            ->count();

        return response()->json([
            'orders'     => $orders,
            'admin_msgs' => $admin_msgs,
        ])->header('Cache-Control', 'no-store');
    }
}