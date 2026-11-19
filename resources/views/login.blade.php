<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartPrint Shop</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Hiwalay na CSS File -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="min-h-screen w-full flex items-center justify-center p-3 sm:p-4">

    <div class="relative w-full max-w-xl glass-card rounded-3xl shadow-2xl overflow-hidden">
        <!-- Image Banner -->
        <div class="relative h-56 sm:h-72 w-full overflow-hidden border-b border-slate-700/60">
            <img src="{{ asset('product-jpeg-1000x1000.webp') }}" alt="SmartPrint Shop Machine" class="w-full h-full object-cover object-center transform hover:scale-105 transition duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-6 left-5 right-5 sm:left-8 sm:right-8 flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-xl shadow-blue-600/40 shrink-0 text-2xl">
                    <i class="fa-solid fa-print"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight leading-tight">SmartPrint Shop</h2>
                    <p class="text-xs text-blue-400 font-bold uppercase tracking-widest mt-1">Management & Ordering Portal</p>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-10 pt-6 space-y-6">
            @if (!empty($error))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-2xl text-xs font-semibold animate-pulse">
                    <i class="fa-solid fa-circle-exclamation shrink-0 text-base"></i>
                    <span>{{ $error }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full bg-slate-950/90 border border-slate-700 text-white text-base sm:text-sm rounded-xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition placeholder-slate-500 shadow-inner" 
                            placeholder="name@example.com" required>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Password</label>
                        <a href="#" class="text-xs text-blue-400 hover:text-blue-300 transition font-medium">Nakalimutan?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="loginPassword" name="password" 
                            class="w-full bg-slate-950/90 border border-slate-700 text-white text-base sm:text-sm rounded-xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition placeholder-slate-500 shadow-inner" 
                            placeholder="••••••••" required style="padding-right: 3rem;">
                        
                        <button type="button" id="togglePassword" aria-label="Ipakita ang password"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-blue-400 text-sm transition focus:outline-none">
                            <i id="togglePasswordIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" value="1"
                    class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold py-4 rounded-xl text-xs shadow-xl shadow-blue-600/30 transition transform active:scale-[0.98] flex items-center justify-center gap-2 mt-4 uppercase tracking-wider">
                    <span>Mag-Login</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <div class="text-center text-xs text-slate-400 pt-5 border-t border-slate-800/80">
                Wala pang account? 
                <a href="{{ route('register') }}" class="text-blue-400 font-bold hover:text-blue-300 transition ml-1 underline underline-offset-4">Mag-Register dito</a>
            </div>
        </div>
    </div>

    <!-- Hiwalay na JS File -->
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>