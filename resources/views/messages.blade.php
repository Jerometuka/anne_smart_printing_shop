<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrint Shop - Customer Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/messages.css') }}">
    <style>
        body { 
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.96)), url('{{ asset("a634de15b82268db1566459c3ca3edc7.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased text-sm">

    <!-- Header Navigation Bar -->
    <header class="bg-slate-900/95 border-b border-slate-700/80 sticky top-0 z-50 backdrop-blur-md shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow">
                    <i class="fa-solid fa-print text-lg"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-base sm:text-lg text-white leading-tight">Anne tarpulin & printing Shop</h1>
                    <p class="text-[11px] text-blue-400 font-bold uppercase tracking-wider">Customer Print</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-cart-plus"></i> <span class="hidden md:inline">Place Order</span>
                </a>
                <a href="{{ route('my.orders') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left"></i> <span class="hidden md:inline">My Orders</span>
                </a>
                <a href="{{ route('messages') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold bg-blue-600 text-white transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-comments"></i> <span class="hidden md:inline">Customer Service</span>
                </a>

                <div class="h-5 w-px bg-slate-700 mx-0.5"></div>

                <a href="{{ url('/profile') }}" class="w-8 h-8 rounded-full overflow-hidden border border-blue-500 bg-slate-800 flex items-center justify-center hover:border-blue-400 transition shrink-0" title="Profile Settings">
                    @if (!empty($logged_user->profile_pic) && file_exists(public_path("uploads/profiles/" . $logged_user->profile_pic)))
                        <img src="{{ asset('uploads/profiles/' . $logged_user->profile_pic) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-xs text-slate-400"></i>
                    @endif
                </a>

                <a href="{{ route('logout') }}" title="Logout" class="w-9 h-9 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-bold border border-red-500/20 transition flex items-center justify-center">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container (Full Screen Chat View) -->
    <main class="max-w-5xl mx-auto w-full px-4 sm:px-6 py-6 flex-1 flex flex-col">
        <div class="glass-card border border-slate-700 rounded-2xl flex flex-col h-[78vh] min-h-[550px] shadow-2xl relative overflow-hidden flex-1">

            <!-- Chat Heading -->
            <div class="px-5 py-3.5 flex items-center justify-between gap-3 bg-gradient-to-r from-blue-600/25 via-indigo-600/15 to-transparent border-b border-slate-700">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg shadow-md">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <span class="online-dot absolute bottom-0 right-0 w-3 h-3 rounded-full bg-green-500 border-2 border-slate-900"></span>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg font-extrabold text-white truncate">Anne Trinidad / Admin</h2>
                        <p class="text-xs text-slate-300 font-medium flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Online &middot; Anne Tarpulin &amp; Printing Shop
                        </p>
                    </div>
                </div>
                <div class="hidden sm:flex w-10 h-10 rounded-xl bg-slate-800/80 border border-slate-700 items-center justify-center text-blue-400 text-base shrink-0">
                    <i class="fa-solid fa-comments"></i>
                </div>
            </div>

            <!-- Chat Box Area -->
            <div class="flex flex-col chat-bg overflow-hidden flex-1 p-4 sm:p-5">
                <!-- Messages History -->
                <div id="chatScroll" class="chat-scroll flex-1 overflow-y-auto space-y-4 pr-2 my-1">
                    @if (count($chat_messages) > 0)
                        @php $lastDay = ''; @endphp
                        @foreach ($chat_messages as $msg)
                            @php
                                $isMe = ($msg->sender === 'customer');
                                $day  = date('Y-m-d', strtotime($msg->created_at));
                            @endphp
                            @if ($day !== $lastDay)
                                @php $lastDay = $day; @endphp
                                <div class="flex items-center gap-3 py-1">
                                    <div class="flex-1 h-px bg-slate-700/60"></div>
                                    <span class="text-[11px] font-bold text-slate-400 bg-slate-900/80 border border-slate-700 px-3 py-0.5 rounded-full">
                                        {{ $day === date('Y-m-d') ? 'Today' : date('F j, Y', strtotime($msg->created_at)) }}
                                    </span>
                                    <div class="flex-1 h-px bg-slate-700/60"></div>
                                </div>
                            @endif

                            <div class="msg-in flex items-end gap-2.5 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                @if (!$isMe)
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xs shrink-0 shadow">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                @endif

                                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} max-w-[78%]">
                                    <div class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm leading-relaxed {{ $isMe ? 'bubble-me text-white rounded-br-sm' : 'bubble-admin text-slate-100 rounded-bl-sm' }}">
                                        @if(!empty($msg->message))
                                            <p class="break-words">{!! nl2br(e($msg->message)) !!}</p>
                                        @endif

                                        @if(!empty($msg->attachment) && file_exists(public_path("uploads/" . $msg->attachment)))
                                            @php $ext = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION)); @endphp
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                                <div class="mt-2">
                                                    <img src="{{ asset('uploads/' . $msg->attachment) }}" data-src="{{ asset('uploads/' . $msg->attachment) }}" alt="Attachment" class="max-h-48 rounded-xl object-contain cursor-zoom-in border border-white/10" onclick="openProofModal(this.dataset.src)">
                                                </div>
                                            @else
                                                <div class="mt-2">
                                                    <a href="{{ asset('uploads/' . $msg->attachment) }}" download class="inline-flex items-center gap-1.5 underline font-bold text-blue-200 text-xs">
                                                        <i class="fa-solid fa-file-arrow-down"></i> Download Attached File ({{ strtoupper($ext) }})
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-1 px-1">{{ date('M d, g:i a', strtotime($msg->created_at)) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-slate-500">
                            <div class="w-16 h-16 rounded-full bg-slate-800/70 border border-slate-700 flex items-center justify-center mb-3">
                                <i class="fa-regular fa-comment-dots text-3xl text-blue-400"></i>
                            </div>
                            <p class="font-extrabold text-slate-200 text-base">No messages yet. Start chatting!</p>
                            <p class="text-xs text-slate-400 mt-0.5">Send a message or file to the shop owner.</p>
                        </div>
                    @endif
                </div>

                <!-- Send Message Form na may Attachment -->
                <form id="chatForm" action="{{ route('message.send') }}" method="POST" enctype="multipart/form-data" class="pt-3 mt-1.5 border-t border-slate-700/80 flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="redirect_to" value="/messages">
                    <input type="text" name="message" placeholder="Type a message..." autocomplete="off" class="flex-1 bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">

                    <label class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-blue-400 w-11 h-11 rounded-xl cursor-pointer text-sm transition flex items-center justify-center shrink-0" title="Attach Picture or File">
                        <i class="fa-solid fa-paperclip"></i>
                        <input type="file" name="chat_file" class="hidden">
                    </label>

                    <button type="submit" class="bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white w-11 h-11 rounded-xl text-sm font-bold transition shadow-md shadow-blue-600/30 flex items-center justify-center shrink-0" title="Send">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- PROOF / ATTACHMENT VIEWER MODAL -->
    <div id="proofModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
        <div class="glass-card w-full max-w-2xl p-6 rounded-2xl shadow-2xl border border-slate-700 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-blue-400"></i> Attachment Viewer
                    </h3>
                </div>
                <button type="button" onclick="document.getElementById('proofModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-base p-1.5"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="space-y-2">
                <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800 flex items-center justify-center overflow-hidden">
                    <img id="modalProofImg" src="" alt="Attachment View" class="max-h-[60vh] w-auto object-contain rounded-lg shadow">
                </div>
            </div>

            <button type="button" onclick="document.getElementById('proofModal').classList.add('hidden')" class="w-full bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-3 rounded-xl transition shadow">Close Viewer</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900/90 border-t border-slate-700/60 py-5 text-center text-xs text-slate-400 backdrop-blur space-y-2">
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm text-slate-200 font-semibold px-4">
            <i class="fa-solid fa-location-dot text-rose-400"></i>
            <span>Anyatam, San Ildefonso, Bulacan &ndash; Dimla Street, Purok 6 (beside Far East Fuel Company)</span>
            <a href="https://www.google.com/maps/search/?api=1&query=Far+East+Fuel+Company+Anyatam+San+Ildefonso+Bulacan" target="_blank" rel="noopener" class="bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition shadow"><i class="fa-solid fa-diamond-turn-right mr-1"></i> Get Directions</a>
        </div>
        <div>&copy; 2026 SmartPrint Shop. All rights reserved.</div>
    </footer>

    <script src="{{ asset('js/messages.js') }}"></script>
</body>
</html>