<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $chat_messages = DB::table('messages')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => (array) $m)
            ->toArray();

        $logged_user = (array) DB::table('users')
            ->where('user_id', $userId)
            ->select('profile_pic')
            ->first();

        return view('customer.messages', compact('chat_messages', 'logged_user'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $message = trim((string) $request->input('message', ''));
        $attachment = null;

        if ($request->hasFile('chat_file') && $request->file('chat_file')->isValid()) {
            $file = $request->file('chat_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $attachment = 'chat_' . time() . '_' . uniqid() . '.' . $ext;
            $dir = public_path('uploads');
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $file->move($dir, $attachment);
        }

        if ($message !== '' || $attachment !== null) {
            DB::table('messages')->insert([
                'user_id'    => $userId,
                'sender'     => 'customer',
                'message'    => $message,
                'attachment' => $attachment,
            ]);
        }

        return redirect()->route('customer.messages');
    }
}