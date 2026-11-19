<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrint - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased" data-redirect-login="{{ !empty($success) ? '1' : '0' }}">

    <div class="glass-card w-full max-w-md p-8 rounded-3xl shadow-2xl space-y-6 border border-slate-700/80">
        
        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 items-center justify-center text-slate-950 font-bold shadow-lg shadow-amber-500/20 text-2xl mb-1">
                <i class="fa-solid fa-print"></i>
            </div>
            <h1 class="text-xl font-extrabold text-white tracking-tight">Create an Account</h1>
            <p class="text-[11px] text-amber-400 font-bold uppercase tracking-wider">SmartPrint Customer Portal</p>
        </div>

        @if (!empty($error))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
                <span>{{ $error }}</span>
            </div>
        @endif

        @if (!empty($success))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>{{ $success }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Full Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-xs">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input type="text" name="full_name" required placeholder="Juan Dela Cruz" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl pl-10 pr-4 py-3 text-white focus:outline-none focus:border-amber-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-xs">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required placeholder="example@gmail.com" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl pl-10 pr-4 py-3 text-white focus:outline-none focus:border-amber-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl pl-10 pr-4 py-3 text-white focus:outline-none focus:border-amber-500 transition">
                </div>
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold py-3.5 rounded-xl transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 mt-2">
                <span>Register Account</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

    </div>

    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>