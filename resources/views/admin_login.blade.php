<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SmartPrint System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        html {
            font-size: max(16px, min(1.3vw, 2vh));
        }

        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.90), rgba(30, 41, 59, 0.95)), url('{{ asset("a634de15b82268db1566459c3ca3edc7.jpg") }}') no-repeat center center fixed;
            background-size: cover;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen w-full flex items-center justify-center p-3 sm:p-4">

    <!-- Admin Login Card Container -->
    <div class="relative w-full max-w-xl glass-card rounded-3xl shadow-2xl border border-amber-500/30 overflow-hidden">
        
        <!-- Printer Image Banner na may Gold/Amber Accent -->
        <div class="relative h-48 sm:h-64 w-full overflow-hidden">
            <img src="{{ asset('360_F_1013825572_SYT88juzJQhO7rVXjlGrJ1PN6ejMDhTD.jpg') }}" alt="SmartPrint Management" class="w-full h-full object-cover object-center transform hover:scale-105 transition duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-5 left-5 right-5 sm:left-8 sm:right-8 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center text-slate-950 shadow-xl shadow-amber-500/20 shrink-0 font-bold text-xl">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-yellow-500 leading-tight">SmartPrint Admin</h2>
                    <p class="text-xs text-amber-500/80 font-semibold uppercase tracking-wider">Executive Management Portal</p>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-10 pt-6">

            <!-- Alert Message (Error) -->
            @if (!empty($error))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-2xl mb-6 text-sm">
                    <i class="fa-solid fa-circle-exclamation shrink-0 text-base"></i>
                    <span>{{ $error }}</span>
                </div>
            @endif

            <!-- Admin Login Form -->
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf
                
                <!-- Email Input -->
                <div>
                    <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-2">
                        Admin Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-amber-500/70 text-sm">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full bg-slate-950 border border-amber-500/30 text-white text-base sm:text-sm rounded-xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition placeholder-slate-500 shadow-inner" 
                            placeholder="admin@smartprint.com" required>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-amber-300 uppercase tracking-wider">
                            Admin Password
                        </label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-amber-500/70 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="adminPassword" name="password" 
                            class="w-full bg-slate-950 border border-amber-500/30 text-white text-base sm:text-sm rounded-xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition placeholder-slate-500 shadow-inner" 
                            placeholder="••••••••" required style="padding-right: 3rem;">
                        <!-- Eye button: ipakita/itago ang password -->
                        <button type="button" id="togglePassword" aria-label="Ipakita ang password"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-amber-500/70 hover:text-amber-400 text-sm transition focus:outline-none">
                            <i id="togglePasswordIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="admin_login" 
                    class="w-full bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 font-extrabold py-4 rounded-xl text-sm sm:text-xs shadow-xl shadow-amber-500/20 transition transform active:scale-[0.98] flex items-center justify-center gap-2 mt-4 uppercase tracking-wider">
                    <span>Access Admin Dashboard</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

        </div>

    </div>

    <script>
        const pwInput = document.getElementById('adminPassword');
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