<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrint Shop - Customer Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        html { font-size: max(16px, 0.8vw); }
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.90), rgba(30, 41, 59, 0.95)), url('{{ asset('a634de15b82268db1566459c3ca3edc7.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .chat-bg {
            background-color: rgba(2, 6, 23, 0.55);
            background-image: radial-gradient(rgba(96,165,250,.10) 1.5px, transparent 1.5px);
            background-size: 26px 26px;
        }
        .bubble-me { background: linear-gradient(135deg, #3b82f6, #4f46e5); box-shadow: 0 8px 22px -8px rgba(59,130,246,.65); }
        .bubble-admin { background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid #334155; box-shadow: 0 8px 22px -10px rgba(0,0,0,.7); }
        @keyframes msgIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
        .msg-in { animation: msgIn .3s ease-out both; }
        @keyframes pulseDot { 0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,.6); } 50% { box-shadow: 0 0 0 7px rgba(34,197,94,0); } }
        .online-dot { animation: pulseDot 1.8s ease-in-out infinite; }
        .chat-scroll::-webkit-scrollbar { width: 8px; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased">

    <!-- Header Navigation Bar -->
    <header class="bg-slate-900/95 border-b border-slate-700/80 sticky top-0 z-50 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-6 h-24 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-print text-2xl"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-2xl text-white leading-tight">Anne tarpulin & printing Shop</h1>
                    <p class="text-sm text-blue-400 font-bold uppercase tracking-wider">Customer Print</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ url('/') }}" class="px-5 py-3 rounded-xl text-base font-bold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-cart-plus"></i> Place Order
                </a>
                <a href="{{ route('customer.orders') }}" class="px-5 py-3 rounded-xl text-base font-bold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left"></i> My Orders
                </a>
                <a href="{{ route('customer.messages') }}" class="px-5 py-3 rounded-xl text-base font-bold bg-blue-600 text-white transition flex items-center gap-1.5 shadow-md shadow-blue-600/25">
                    <i class="fa-solid fa-comments"></i> Customer Service
                </a>

                <div class="h-6 w-px bg-slate-700 mx-1"></div>

                <a href="{{ url('/profile') }}" class="w-14 h-14 rounded-full overflow-hidden border-2 border-blue-500 bg-slate-800 flex items-center justify-center hover:border-blue-400 transition shrink-0 shadow" title="Profile Settings">
                    @if (!empty($logged_user['profile_pic']) && file_exists(public_path("uploads/profiles/" . $logged_user['profile_pic'])))
                        <img src="{{ asset('uploads/profiles/' . $logged_user['profile_pic']) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-sm text-slate-400"></i>
                    @endif
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Logout" class="px-5 py-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-base font-bold border border-red-500/20 transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-8 flex-1 flex flex-col">
        <div class="glass-card border border-slate-700 rounded-3xl flex flex-col h-[85vh] min-h-[700px] shadow-2xl relative overflow-hidden flex-1">

            <!-- Chat Heading -->
            <div class="px-6 sm:px-8 py-5 flex items-center justify-between gap-4 bg-gradient-to-r from-blue-600/30 via-indigo-600/20 to-transparent border-b border-slate-700">
                <div class="flex items-center gap-5 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-lg shadow-blue-600/30">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <span class="online-dot absolute bottom-0 right-0 w-4 h-4 rounded-full bg-green-500 border-2 border-slate-900"></span>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white truncate">Anne Trinidad/admin</h2>
                        <p class="text-base text-slate-300 font-medium flex items-center gap-2 mt-0.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Online &middot; Anne Tarpulin &amp; Printing Shop
                        </p>
                    </div>
                </div>
                <div class="hidden sm:flex w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 items-center justify-center text-blue-400 text-2xl shrink-0">
                    <i class="fa-solid fa-comments"></i>
                </div>
            </div>

            <!-- Chat Box Area -->
            <div class="flex flex-col chat-bg overflow-hidden flex-1 p-4 sm:p-6">
                <div id="chatScroll" class="chat-scroll flex-1 overflow-y-auto space-y-5 pr-2 my-2">
                    @if (count($chat_messages) > 0)
                        @php $lastDay = ''; @endphp
                        @foreach ($chat_messages as $msg)
                            @php
                                $isMe = ($msg['sender'] === 'customer');
                                $day  = date('Y-m-d', strtotime($msg['created_at']));
                            @endphp
                            @if ($day !== $lastDay)
                                @php $lastDay = $day; @endphp
                                <div class="flex items-center gap-4 py-1">
                                    <div class="flex-1 h-px bg-slate-700/70"></div>
                                    <span class="text-sm font-bold text-slate-400 bg-slate-900/80 border border-slate-700 px-4 py-1 rounded-full">
                                        {{ $day === date('Y-m-d') ? 'Today' : date('F j, Y', strtotime($msg['created_at'])) }}
                                    </span>
                                    <div class="flex-1 h-px bg-slate-700/70"></div>
                                </div>
                            @endif

                            <div class="msg-in flex items-end gap-3 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                @if (!$isMe)
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-lg shrink-0 shadow">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                @endif

                                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} max-w-[80%]">
                                    <div class="px-6 py-3.5 rounded-3xl text-lg leading-relaxed {{ $isMe ? 'bubble-me text-white rounded-br-md' : 'bubble-admin text-slate-100 rounded-bl-md' }}">
                                        @if(!empty($msg['message']))
                                            <p class="break-words">{!! nl2br(e($msg['message'])) !!}</p>
                                        @endif

                                        @if(!empty($msg['attachment']) && file_exists(public_path("uploads/" . $msg['attachment'])))
                                            @php $ext = strtolower(pathinfo($msg['attachment'], PATHINFO_EXTENSION)); @endphp
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                                <div class="mt-3">
                                                    <img src="{{ asset('uploads/' . $msg['attachment']) }}" alt="Attachment" class="max-h-72 rounded-2xl object-contain cursor-zoom-in border border-white/10" onclick="openProofModal('{{ asset('uploads/' . $msg['attachment']) }}')">
                                                </div>
                                            @else
                                                <div class="mt-3">
                                                    <a href="{{ asset('uploads/' . $msg['attachment']) }}" download class="inline-flex items-center gap-2 underline font-bold text-blue-200 text-base">
                                                        <i class="fa-solid fa-file-arrow-down"></i> Download Attached File ({{ strtoupper($ext) }})
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <span class="text-sm text-slate-400 mt-1.5 px-2">{{ date('M d, g:i a', strtotime($msg['created_at'])) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-slate-500">
                            <div class="w-24 h-24 rounded-full bg-slate-800/70 border border-slate-700 flex items-center justify-center mb-4">
                                <i class="fa-regular fa-comment-dots text-5xl text-blue-400"></i>
                            </div>
                            <p class="font-extrabold text-slate-200 text-2xl">No messages yet. Start chatting!</p>
                            <p class="text-lg text-slate-400 mt-1">Send a message or file to the shop owner.</p>
                        </div>
                    @endif
                </div>

                <!-- Send Message Form -->
                <form id="chatForm" action="{{ route('customer.message.send') }}" method="POST" enctype="multipart/form-data" class="pt-4 mt-2 border-t border-slate-700/80 flex items-center gap-3">
                    @csrf
                    <input type="text" name="message" placeholder="Type a message..." autocomplete="off" class="flex-1 bg-slate-900/90 border-2 border-slate-700 rounded-2xl px-6 py-4 text-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">

                    <label class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-blue-400 w-16 h-16 rounded-2xl cursor-pointer text-2xl transition flex items-center justify-center shrink-0" title="Attach Picture or File">
                        <i class="fa-solid fa-paperclip"></i>
                        <input type="file" name="chat_file" class="hidden">
                    </label>

                    <button type="submit" class="bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white w-16 h-16 rounded-2xl text-2xl font-bold transition shadow-lg shadow-blue-600/30 flex items-center justify-center shrink-0" title="Send">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- PROOF / ATTACHMENT VIEWER MODAL -->
    <div id="proofModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
        <div class="glass-card w-full max-w-4xl p-8 rounded-3xl shadow-2xl border border-slate-700 space-y-6">
            <div class="flex justify-between items-center border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-xl font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-blue-400"></i> Attachment Viewer
                    </h3>
                </div>
                <button onclick="document.getElementById('proofModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-xl p-2"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="space-y-2">
                <div class="bg-slate-950 p-3 rounded-2xl border border-slate-800 flex items-center justify-center overflow-hidden">
                    <img id="modalProofImg" src="" alt="Attachment View" class="max-h-[70vh] w-auto object-contain rounded-xl shadow-lg">
                </div>
            </div>

            <button onclick="document.getElementById('proofModal').classList.add('hidden')" class="w-full bg-slate-800 hover:bg-slate-700 text-white text-base font-bold py-4 rounded-xl transition shadow">Close Viewer</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900/90 border-t border-slate-700/60 py-8 text-center text-slate-400 mt-12 backdrop-blur space-y-2">
        <div class="text-base sm:text-lg text-slate-200 font-semibold px-4"><i class="fa-solid fa-location-dot text-rose-400 mr-2"></i> Anyatam, San Ildefonso, Bulacan &ndash; Dimla Street, Purok 6 (beside Far East Fuel Company)</div>
        <div class="text-sm">&copy; 2026 SmartPrint Shop. All rights reserved.</div>
    </footer>

    <script>
    (function () { const c = document.getElementById('chatScroll'); if (c) c.scrollTop = c.scrollHeight; })();
    document.addEventListener('keydown', e => { if (e.key === 'Escape') document.getElementById('proofModal').classList.add('hidden'); });
    function openProofModal(imgSrc) {
        document.getElementById('modalProofImg').src = imgSrc;
        document.getElementById('proofModal').classList.remove('hidden');
    }

    (function () {
        const form = document.getElementById('chatForm');
        if (!form) return;
        const input = form.querySelector('input[name="message"]');
        const fileInput = form.querySelector('input[name="chat_file"]');
        const btn = form.querySelector('button[type="submit"]');
        let sending = false;

        async function refreshChat() {
            const r = await fetch('{{ route('customer.messages') }}', { cache: 'no-store', credentials: 'same-origin' });
            if (!r.ok) throw new Error('refresh failed');
            const doc = new DOMParser().parseFromString(await r.text(), 'text/html');
            const fresh = doc.getElementById('chatScroll');
            const box = document.getElementById('chatScroll');
            if (!fresh || !box) throw new Error('chat box missing');
            const oldCount = box.querySelectorAll('.msg-in').length;
            box.innerHTML = fresh.innerHTML;
            box.querySelectorAll('.msg-in').forEach((el, i) => { if (i < oldCount) el.style.animation = 'none'; });
            box.scrollTop = box.scrollHeight;
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (sending) return;
            const hasText = input.value.trim() !== '';
            const hasFile = fileInput.files && fileInput.files.length > 0;
            if (!hasText && !hasFile) return;

            sending = true;
            btn.disabled = true;
            btn.classList.add('opacity-60');

            let posted = false;
            try {
                const res = await fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin' });
                if (!res.ok) throw new Error('send failed');
                posted = true;
                form.reset();
                await refreshChat();
            } catch (err) {
                if (!posted) { sending = false; form.submit(); return; }
            }

            sending = false;
            btn.disabled = false;
            btn.classList.remove('opacity-60');
            input.focus();
        });
    })();
    </script>
</body>
</html>