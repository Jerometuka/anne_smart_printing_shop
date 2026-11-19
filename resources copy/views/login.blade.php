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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="min-h-screen w-full flex items-center justify-center p-4">

  
    <div class="relative w-full max-w-lg glass-card rounded-3xl shadow-2xl overflow-hidden">
        
      
        <div class="cmyk-bar w-full"></div>

        
        <div class="relative h-52 sm:h-60 w-full overflow-hidden border-b border-slate-700/60 banner-bg">
            <img src="{{ asset('product-jpeg-1000x1000.webp') }}" 
                 alt="SmartPrint Shop Machine" 
                 onerror="this.style.display='none'"
                 class="w-full h-full object-cover object-center transform hover:scale-105 transition duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/45 to-transparent"></div>
            
             
            <div class="absolute top-4 right-4 flex items-center gap-1.5 bg-slate-950/70 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
            </div>

            <div class="absolute bottom-5 left-5 right-5 sm:left-7 sm:right-7 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 via-blue-600 to-pink-500 flex items-center justify-center text-white shadow-xl shadow-blue-600/40 shrink-0 text-2xl">
                    <i class="fa-solid fa-print"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight leading-tight">SmartPrint Shop</h2>
                    <p class="text-xs text-cyan-300 font-bold uppercase tracking-widest mt-0.5">Management & Ordering Portal</p>
                </div>
            </div>
        </div>

       
        <div class="p-6 sm:p-8 space-y-5">

          
            @if (session('error'))
                <div class="flex items-center gap-3 bg-red-500/15 border border-red-500/40 text-red-300 p-3.5 rounded-2xl text-xs font-semibold animate-pulse">
                    <i class="fa-solid fa-circle-exclamation shrink-0 text-base text-red-400"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                @csrf
                
                <!-- Email Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-cyan-400 text-sm">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" 
                            class="w-full bg-slate-950/90 border border-slate-700 text-white text-sm rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/25 transition placeholder-slate-500 shadow-inner" 
                            placeholder="name@example.com" required>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                            Password
                        </label>
                        <a href="{{ url('/forgot-password') }}" class="text-xs text-pink-400 hover:text-pink-300 transition font-semibold">
                            Nakalimutan?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-pink-400 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="loginPassword" name="password" 
                            class="w-full bg-slate-950/90 border border-slate-700 text-white text-sm rounded-xl pl-11 pr-12 py-3 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-400/25 transition placeholder-slate-500 shadow-inner" 
                            placeholder="••••••••" required>
                        <!-- Eye button: ipakita/itago ang password -->
                        <button type="button" id="togglePassword" aria-label="Ipakita ang password"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-cyan-400 text-sm transition focus:outline-none">
                            <i id="togglePasswordIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="login" 
                    class="w-full bg-gradient-to-r from-cyan-500 via-blue-600 to-pink-600 hover:from-cyan-400 hover:via-blue-500 hover:to-pink-500 text-white font-extrabold py-3.5 rounded-xl text-xs shadow-xl shadow-indigo-600/35 transition transform active:scale-[0.98] flex items-center justify-center gap-2 mt-2 uppercase tracking-wider">
                    <span>Mag-Login</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Register Link para sa mga Customers -->
            <div class="text-center text-xs text-slate-400 pt-4 border-t border-slate-800/80">
                Wala pang account? 
                <a href="{{ url('/register') }}" class="text-cyan-400 font-bold hover:text-pink-400 transition ml-1 underline underline-offset-4">
                    Mag-Register dito
                </a>
            </div>
        </div>

    </div>

    <script>
        const pwInput = document.getElementById('loginPassword');
        const pwBtn = document.getElementById('togglePassword');
        const pwIcon = document.getElementById('togglePasswordIcon');

        pwBtn.addEventListener('click', function () {
            const show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            pwIcon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            pwBtn.setAttribute('aria-label', show ? 'Itago ang password' : 'Ipakita ang password');
        });
    </script>

</body>
</html>