<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


{
    function showLogin()
    {
        return view('admin_login');
    }

   function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = DB::table('users')
            ->where('email', trim($request->email))
            ->where('role', 'admin')
            ->first();

        if ($admin && Hash::check($request->password, $admin->password_hash)) {
            session([
                'user_id' => $admin->user_id,
                'user_name' => $admin->full_name,
                'role' => 'admin',
            ]);

            return redirect()->route('admin.index'); 
        }

        return back()->with('error', 'wrong email or password!');
    }

     function logout()
    {
        session()->flush();
        return redirect()->route('admin.login');
    }
}