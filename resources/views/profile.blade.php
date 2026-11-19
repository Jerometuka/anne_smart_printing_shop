<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.90), rgba(30, 41, 59, 0.95)), url('{{ asset("a634de15b82268db1566459c3ca3edc7.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
            font-size: 14px; /* Standard web base size */
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .field { transition: border-color .2s, box-shadow .2s; }
        .field:focus { box-shadow: 0 0 0 3px rgba(59,130,246,.18); }
        .avatar-ring { background: conic-gradient(from 0deg, #3b82f6, #8b5cf6, #ec4899, #f59e0b, #3b82f6); padding: 4px; }
        input[type=file]::file-selector-button { cursor: pointer; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased">

    <!-- Header -->
    <header class="bg-slate-900/95 border-b border-slate-700/80 sticky top-0 z-50 backdrop-blur-md shadow-lg">
        <div class="w-full px-6 h-16 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-print text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-base text-white leading-tight">Anne tarpulin & printing Shop</h1>
                    <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider">Customer Profile</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-sm font-semibold bg-slate-800 hover:bg-slate-700 text-white transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Dashboard
                </a>
                <a href="{{ route('logout') }}" class="px-4 py-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-semibold border border-red-500/20 transition">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-3xl mx-auto w-full px-4 py-8 flex-1">
        <div class="glass-card rounded-2xl shadow-xl overflow-hidden">

            <!-- Banner heading -->
            <div class="relative px-6 py-6 text-center bg-gradient-to-br from-blue-600/30 via-indigo-600/20 to-purple-600/10 border-b border-slate-700/80">
                <div class="relative space-y-1.5">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl shadow-md shadow-blue-600/30">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white uppercase tracking-wide">Customer Profile Settings</h2>
                    <p class="text-xs sm:text-sm text-slate-300">Update your personal details, contact number, address, and profile picture.</p>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                @if (!empty($error))
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-lg text-sm font-medium text-center">
                        {{ $error }}
                    </div>
                @endif

                <form action="{{ route('profile.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Profile Picture Preview Box -->
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="avatar-ring rounded-full shadow-lg shadow-blue-600/20">
                            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden bg-slate-950 flex items-center justify-center">
                                @if (!empty($user->profile_pic) && file_exists(public_path("uploads/profiles/" . $user->profile_pic)))
                                    <img id="avatarPreview" src="{{ asset('uploads/profiles/' . $user->profile_pic) }}" alt="Profile Picture" class="w-full h-full object-cover">
                                @else
                                    <img id="avatarPreview" src="" alt="Profile Picture" class="w-full h-full object-cover hidden">
                                    <i id="avatarIcon" class="fa-solid fa-user text-4xl text-slate-400"></i>
                                @endif
                            </div>
                        </div>
                        <div class="w-full max-w-sm">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5 text-center"><i class="fa-solid fa-camera mr-1 text-blue-400"></i> Change Profile Picture</label>
                            <input type="file" id="profilePicInput" name="profile_pic" accept="image/*" class="w-full text-xs text-slate-300 file:bg-blue-600 hover:file:bg-blue-500 file:text-white file:px-3 file:py-2 file:mr-3 file:rounded-lg file:border-0 file:font-semibold cursor-pointer font-medium bg-slate-950 border border-slate-700 rounded-lg p-1">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5"><i class="fa-solid fa-id-card mr-1 text-blue-400"></i> Full Name</label>
                            <input type="text" value="{{ $user->full_name ?? '' }}" disabled class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-400 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5"><i class="fa-solid fa-envelope mr-1 text-blue-400"></i> Email (Read-Only)</label>
                            <input type="email" value="{{ $user->email ?? '' }}" disabled class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-400 cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5"><i class="fa-solid fa-phone mr-1 text-blue-400"></i> Phone Number</label>
                        <input type="text" name="phone" value="{{ $user->phone ?? '' }}" placeholder="e.g., 09123456789" class="field w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5"><i class="fa-solid fa-location-dot mr-1 text-blue-400"></i> Home Address</label>
                        <textarea name="address" rows="3" placeholder="Enter your complete home address..." class="field w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">{{ $user->address ?? '' }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3 rounded-lg text-sm uppercase tracking-wider transition shadow-md shadow-blue-600/30">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Save Profile Changes
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900/90 border-t border-slate-700/60 py-5 text-center text-slate-400 mt-8 backdrop-blur space-y-1">
        <div class="text-xs sm:text-sm text-slate-300 font-medium px-4"><i class="fa-solid fa-location-dot text-rose-400 mr-1.5"></i> Anyatam, San Ildefonso, Bulacan &ndash; Dimla Street, Purok 6 (beside Far East Fuel Company)</div>
        <div class="text-xs">&copy; 2026 SmartPrint Shop. All rights reserved.</div>
    </footer>

    <script>
    // Live preview ng napiling profile picture bago i-save
    document.getElementById('profilePicInput').addEventListener('change', function () {
        const f = this.files && this.files[0];
        if (!f || !f.type.startsWith('image/')) return;
        const img = document.getElementById('avatarPreview');
        const icon = document.getElementById('avatarIcon');
        img.src = URL.createObjectURL(f);
        img.classList.remove('hidden');
        if (icon) icon.classList.add('hidden');
    });
    </script>
</body>
</html>