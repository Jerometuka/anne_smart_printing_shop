\<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrint Shop - My Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.72), rgba(30, 41, 59, 0.85)), url('{{ asset('a634de15b82268db1566459c3ca3edc7.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.10);
        }
        .ticket {
            position: relative;
            background: linear-gradient(180deg, rgba(15,23,42,0.90), rgba(15,23,42,0.82));
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.10);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .ticket:hover { transform: translateY(-2px); box-shadow: 0 14px 30px -10px rgba(0,0,0,.6); border-color: rgba(96,165,250,.45); }
        .perforation { border-left: 2px dashed rgba(148,163,184,.25); }
        @media (max-width: 767px) { .perforation { border-left: 0; border-top: 2px dashed rgba(148,163,184,.25); } }
        .grid-bg {
            background-color: #060b16;
            background-image: linear-gradient(rgba(148,163,184,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(148,163,184,.08) 1px, transparent 1px);
            background-size: 18px 18px;
        }
        @keyframes popIn { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
        .pop-in { animation: popIn .2s ease-out; }

        .pp-anim { --pp: #f59e0b; --pp2: #fbbf24; }
        .pp-printer { position: relative; width: 46px; height: 46px; flex-shrink: 0; overflow: hidden; }
        .pp-body {
            position: absolute; left: 0; right: 0; bottom: 0; height: 28px; border-radius: 9px; z-index: 2;
            background: linear-gradient(var(--pp2), var(--pp)); color: #fff; font-size: 15px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,.35);
            animation: pp-bob .9s ease-in-out infinite;
        }
        .pp-paper {
            position: absolute; left: 11px; right: 11px; top: 0; height: 30px; border-radius: 2px; z-index: 1;
            background: #fff;
            background-image: repeating-linear-gradient(to bottom, transparent 0 4px, #cbd5e1 4px 6px);
            animation: pp-feed 1.8s ease-in-out infinite;
        }
        .pp-bar { position: relative; height: 6px; border-radius: 9999px; background: rgba(148,163,184,.25); overflow: hidden; }
        .pp-bar span {
            position: absolute; top: 0; bottom: 0; left: -40%; width: 40%; border-radius: inherit;
            background: linear-gradient(90deg, var(--pp), var(--pp2));
            animation: pp-slide 1.4s ease-in-out infinite;
        }
        .pp-dots b {
            display: inline-block; width: 4px; height: 4px; margin-left: 3px; border-radius: 9999px; background: currentColor;
            animation: pp-jump 1s ease-in-out infinite;
        }
        .pp-dots b:nth-child(2) { animation-delay: .15s; }
        .pp-dots b:nth-child(3) { animation-delay: .30s; }
        @keyframes pp-bob  { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-2px); } }
        @keyframes pp-feed { 0% { transform: translateY(20px); opacity: 1; } 55%,85% { transform: translateY(0); opacity: 1; } 100% { transform: translateY(0); opacity: 0; } }
        @keyframes pp-slide { 0% { left: -40%; } 100% { left: 100%; } }
        @keyframes pp-jump { 0%,60%,100% { transform: translateY(0); opacity: .5; } 30% { transform: translateY(-4px); opacity: 1; } }

        .pp-anim.pp-wait  { --pp: #0ea5e9; --pp2: #38bdf8; background: rgba(14,165,233,.08) !important; border-color: rgba(14,165,233,.35) !important; }
        .pp-anim.pp-ready { --pp: #8b5cf6; --pp2: #c4b5fd; background: rgba(139,92,246,.10) !important; border-color: rgba(139,92,246,.40) !important; }
        .pp-icon { position: relative; width: 46px; height: 46px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .pp-core {
            position: relative; z-index: 2; width: 34px; height: 34px; border-radius: 9999px; color: #fff; font-size: 15px;
            background: linear-gradient(var(--pp2), var(--pp)); display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,.35);
        }
        .pp-ring { position: absolute; inset: 4px; border-radius: 9999px; border: 2px solid var(--pp); animation: pp-ripple 1.8s ease-out infinite; }
        .pp-ring.r2 { animation-delay: .9s; }
        .pp-flip { display: inline-block; animation: pp-flip 2.4s ease-in-out infinite; }
        .pp-queue { display: flex; gap: 5px; }
        .pp-queue span { width: 7px; height: 7px; border-radius: 9999px; background: var(--pp); opacity: .25; animation: pp-seq 1.6s ease-in-out infinite; }
        .pp-queue span:nth-child(2) { animation-delay: .25s; }
        .pp-queue span:nth-child(3) { animation-delay: .50s; }
        .pp-queue span:nth-child(4) { animation-delay: .75s; }
        .pp-queue span:nth-child(5) { animation-delay: 1.0s; }
        .pp-hop { display: inline-block; animation: pp-hop 1.1s ease-in-out infinite; }
        .pp-check {
            position: absolute; z-index: 3; right: 1px; top: 1px; width: 16px; height: 16px; border-radius: 9999px;
            background: #10b981; color: #fff; font-size: 8px; border: 2px solid #0f172a;
            display: flex; align-items: center; justify-content: center; animation: pp-pop 1.2s ease-in-out infinite;
        }
        .pp-spark { position: absolute; z-index: 1; color: var(--pp2); font-size: 9px; opacity: 0; animation: pp-twinkle 1.5s ease-in-out infinite; }
        .pp-spark.s1 { left: 0; top: 5px; }
        .pp-spark.s2 { right: 0; bottom: 4px; animation-delay: .5s; }
        .pp-spark.s3 { left: 4px; bottom: 0; animation-delay: 1s; }
        @keyframes pp-ripple  { 0% { transform: scale(.7); opacity: .9; } 100% { transform: scale(1.35); opacity: 0; } }
        @keyframes pp-flip    { 0%, 55% { transform: rotate(0deg); } 85%, 100% { transform: rotate(180deg); } }
        @keyframes pp-seq     { 0%, 100% { opacity: .25; transform: scale(1); } 20% { opacity: 1; transform: scale(1.35); } }
        @keyframes pp-hop     { 0%, 100% { transform: translateY(0) scaleY(1); } 40% { transform: translateY(-4px) scaleY(1.06); } 60% { transform: translateY(0) scaleY(.92); } }
        @keyframes pp-pop     { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.25); } }
        @keyframes pp-twinkle { 0%, 100% { opacity: 0; transform: scale(.4) rotate(0deg); } 50% { opacity: 1; transform: scale(1.1) rotate(25deg); } }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased text-sm">

    <!-- Header Navigation Bar -->
    <header class="bg-slate-900/95 border-b border-slate-700/80 sticky top-0 z-50 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex justify-between items-center gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md shrink-0">
                    <i class="fa-solid fa-print text-lg"></i>
                </div>
                <div class="truncate">
                    <h1 class="font-extrabold text-base sm:text-lg text-white leading-tight truncate">Anne Tarpulin & Printing Shop</h1>
                    <p class="text-[11px] text-blue-400 font-bold uppercase tracking-wider">Customer Print</p>
                </div>
            </a>

            <nav class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-cart-plus"></i> <span class="hidden md:inline">Place Order</span>
                </a>
                <a href="{{ route('my.orders') }}" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-blue-600 text-white transition flex items-center gap-1.5 shadow-md shadow-blue-600/25">
                    <i class="fa-solid fa-clock-rotate-left"></i> <span>My Orders</span>
                </a>
                <a href="{{ route('messages') }}" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-comments"></i> <span class="hidden md:inline">Customer Service</span>
                </a>

                <div class="h-6 w-px bg-slate-700 mx-1"></div>

                <a href="{{ route('profile') }}" class="w-9 h-9 rounded-full overflow-hidden border-2 border-blue-500 bg-slate-800 flex items-center justify-center hover:border-blue-400 transition shrink-0 shadow" title="Profile Settings">
                    @if (!empty($logged_user->profile_pic) && file_exists(public_path("uploads/profiles/" . $logged_user->profile_pic)))
                        <img src="{{ asset('uploads/profiles/' . $logged_user->profile_pic) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-sm text-slate-400"></i>
                    @endif
                </a>

                <a href="{{ route('logout') }}" title="Logout" class="px-3.5 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs sm:text-sm font-semibold border border-red-500/20 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-right-from-bracket"></i> <span class="hidden sm:inline">Logout</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-8 flex-1 space-y-6">

        <!-- Title bar -->
        <div class="glass-card p-5 sm:p-6 rounded-2xl shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-36 h-36 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>
            <div class="flex items-center gap-3.5 relative">
                <a href="{{ route('home') }}" class="shrink-0 bg-slate-800 hover:bg-slate-700 border border-slate-600/80 text-slate-200 text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg shadow-lg shadow-blue-600/30 shrink-0">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">My Order History</h2>
                    <p class="text-xs sm:text-sm text-slate-400">{{ count($all_orders) }} order{{ count($all_orders) == 1 ? '' : 's' }} in total</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="relative bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-lg shadow-blue-600/30 text-center shrink-0">
                <i class="fa-solid fa-plus mr-1"></i> Place New Order
            </a>
        </div>

        <!-- Orders List -->
        <div class="space-y-4">
            @if (count($all_orders) > 0)
                @foreach ($all_orders as $ord)
                    @php
                        $ordArr   = (array) $ord;
                        $f        = $orderFile($ordArr);
                        $furl     = $f !== '' ? route('order.file', ['order' => (int)$ord->order_id]) : '';
                        $isImg    = $f !== '' && $isImageFile($f);
                        $ext      = strtoupper(pathinfo($f, PATHINFO_EXTENSION));
                        [$skey, $step, $sclass, $scolor] = $statusMeta((string)$ord->status);
                        $steps    = ['Pending', 'Printing', 'Ready', 'Done'];
                        $proofF   = !empty($ord->proof_image) ? (string)$ord->proof_image : '';
                        $proofUrl = ($proofF !== '' && file_exists(public_path('uploads/' . $proofF))) ? asset('uploads/' . $proofF) : '';
                    @endphp
                    <div class="ticket rounded-2xl shadow-lg flex flex-col md:flex-row overflow-hidden">
                        <div class="w-full h-1.5 md:w-1.5 md:h-auto shrink-0" style="background: {{ $scolor }};"></div>

                        <!-- Uploaded file preview -->
                        <div class="p-4 md:pr-0 flex md:items-center shrink-0">
                            <button type="button" onclick='openDetailsModal(@json($ord))'
                                    class="w-full md:w-36 h-36 rounded-xl overflow-hidden border border-slate-700/80 grid-bg flex items-center justify-center group relative">
                                @if ($isImg)
                                    <img src="{{ $furl }}" alt="Uploaded file" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @elseif ($f !== '')
                                    <div class="text-center">
                                        <i class="fa-solid {{ $ext === 'PDF' ? 'fa-file-pdf text-rose-400' : 'fa-file-lines text-blue-400' }} text-3xl"></i>
                                        <div class="text-xs font-bold text-slate-300 mt-1">{{ $ext ?: 'FILE' }}</div>
                                    </div>
                                @else
                                    <div class="text-center text-slate-500">
                                        <i class="fa-regular fa-image text-2xl"></i>
                                        <div class="text-[11px] font-semibold mt-1 uppercase">No file</div>
                                    </div>
                                @endif
                            </button>
                        </div>

                        <!-- Order Details -->
                        <div class="flex-1 p-4 sm:p-5 space-y-3 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono font-bold text-blue-400 text-sm">{{ $ord->order_code }}</span>
                                <span class="bg-slate-800 text-slate-300 text-xs px-2.5 py-0.5 rounded-full uppercase font-semibold">{{ $ord->payment_method }}</span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full uppercase font-bold border {{ $sclass }}">{{ $ord->status }}</span>
                            </div>

                            <div>
                                <h4 class="font-bold text-white text-lg sm:text-xl truncate">{{ $ord->product_name }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5"><i class="fa-regular fa-calendar-days mr-1"></i> {{ date("M d, Y - h:i A", strtotime($ord->created_at)) }}</p>
                            </div>

                            <!-- Progress tracker -->
                            @if ($step >= 0)
                                <div class="flex items-center pt-1 max-w-md">
                                    @foreach ($steps as $i => $label)
                                        <div class="flex flex-col items-center">
                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center {{ $i <= $step ? 'border-transparent' : 'border-slate-600 bg-slate-800' }}"
                                                 style="{{ $i <= $step ? "background: $scolor;" : '' }}">
                                                @if ($i < $step)<i class="fa-solid fa-check text-[10px] text-white"></i>@endif
                                            </div>
                                            <span class="text-[11px] mt-1 font-semibold {{ $i <= $step ? 'text-slate-200' : 'text-slate-500' }}">{{ $label }}</span>
                                        </div>
                                        @if ($i < count($steps) - 1)
                                            <div class="flex-1 h-0.5 mx-1.5 -mt-4 rounded" style="{{ $i < $step ? "background: $scolor;" : 'background: #334155;' }}"></div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs font-semibold text-rose-300"><i class="fa-solid fa-ban mr-1"></i> This order was cancelled.</p>
                            @endif

                            @if ($skey === 'pending')
                                <div class="pp-anim pp-wait flex items-center gap-3 rounded-xl border p-3">
                                    <div class="pp-icon"><span class="pp-ring"></span><span class="pp-ring r2"></span><div class="pp-core"><i class="fa-solid fa-hourglass-half pp-flip"></i></div></div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="text-sm font-bold" style="color: var(--pp2);">Waiting in line<span class="pp-dots"><b></b><b></b><b></b></span></div>
                                        <div class="pp-queue"><span></span><span></span><span></span><span></span><span></span></div>
                                        <p class="text-xs text-slate-400">Your order is in the queue. The shop will start on it shortly.</p>
                                    </div>
                                </div>
                            @endif

                            @if ($skey === 'ready')
                                <div class="pp-anim pp-ready flex items-center gap-3 rounded-xl border p-3">
                                    <div class="pp-icon"><i class="fa-solid fa-star pp-spark s1"></i><i class="fa-solid fa-star pp-spark s2"></i><i class="fa-solid fa-star pp-spark s3"></i><div class="pp-core"><i class="fa-solid fa-bag-shopping pp-hop"></i></div><span class="pp-check"><i class="fa-solid fa-check"></i></span></div>
                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <div class="text-sm font-bold" style="color: var(--pp2);">Ready for Pickup!</div>
                                        <p class="text-xs text-slate-300">Your order is ready. Please show your order code at the shop.</p>
                                    </div>
                                </div>
                            @endif

                            @if ($skey === 'processing')
                                <div class="pp-anim flex items-center gap-3 rounded-xl border p-3" style="background: rgba(245,158,11,.08); border-color: rgba(245,158,11,.35);">
                                    <div class="pp-printer"><div class="pp-paper"></div><div class="pp-body"><i class="fa-solid fa-print"></i></div></div>
                                    <div class="flex-1 min-w-0 space-y-1.5">
                                        <div class="text-sm font-bold" style="color: var(--pp2);">Printing your order<span class="pp-dots"><b></b><b></b><b></b></span></div>
                                        <div class="pp-bar"><span></span></div>
                                        <p class="text-xs text-slate-400">Your order is being printed. Please wait a moment!</p>
                                    </div>
                                </div>
                            @endif

                            @if ($proofUrl !== '')
                                <div class="rounded-xl border border-amber-500/40 bg-amber-500/10 p-3 space-y-2 max-w-md">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-300"><i class="fa-solid fa-camera mr-1.5"></i>Proof of Delivery</span>
                                        <button type="button" onclick="openProofLightbox('{{ $proofUrl }}')" class="text-xs font-semibold text-amber-300 hover:text-amber-200 hover:underline"><i class="fa-solid fa-expand mr-1"></i> Enlarge</button>
                                    </div>
                                    <button type="button" onclick="openProofLightbox('{{ $proofUrl }}')" class="block w-full rounded-lg overflow-hidden border border-amber-500/40 bg-slate-950/70 cursor-zoom-in">
                                        <img src="{{ $proofUrl }}" alt="Proof of delivery" class="w-full h-40 object-contain hover:scale-[1.02] transition duration-300">
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Total + buttons -->
                        <div class="perforation md:w-52 shrink-0 p-4 sm:p-5 flex md:flex-col items-center md:justify-center justify-between gap-3 bg-slate-950/30">
                            <div class="text-left md:text-center">
                                <div class="text-[11px] uppercase font-bold text-slate-500 tracking-widest">Total</div>
                                <div class="text-2xl sm:text-3xl font-black text-white">₱{{ number_format($ord->total, 2) }}</div>
                            </div>
                            <div class="flex md:flex-col gap-2 w-auto md:w-full">
                                <button onclick='openDetailsModal(@json($ord))' class="bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl transition shadow text-center">
                                    <i class="fa-solid fa-eye mr-1"></i> Details
                                </button>
                                <a href="{{ route('order.delete.get', ['id' => $ord->order_id]) }}" onclick="return confirm('Delete this order?');" class="text-center bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl transition">
                                    <i class="fa-solid fa-trash"></i><span class="ml-1">Delete</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="glass-card rounded-2xl p-12 text-center text-slate-400 shadow-sm">
                    <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-500"></i>
                    <p class="font-semibold text-slate-300 text-lg">You have no orders yet.</p>
                    <a href="{{ route('home') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">Place your first order</a>
                </div>
            @endif
        </div>
    </main>

    <!-- DETAILS MODAL WINDOW -->
    <div id="detailsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[60] hidden items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this) closeDetailsModal()">
        <div class="pop-in bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-4xl shadow-2xl relative my-auto max-h-[90vh] overflow-hidden text-white">
            <button onclick="closeDetailsModal()" class="absolute top-3 right-3 z-20 text-slate-300 hover:text-white text-lg bg-slate-800/90 hover:bg-slate-700 w-9 h-9 rounded-full flex items-center justify-center transition shadow">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-5 max-h-[90vh] overflow-y-auto md:overflow-hidden">
                <!-- LEFT: Uploaded file preview -->
                <div class="md:col-span-3 grid-bg p-4 flex flex-col gap-3 min-h-[240px] md:max-h-[90vh]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400"><i class="fa-solid fa-paperclip mr-1"></i> Uploaded File to Print</span>
                        <span id="modal_file_name" class="text-xs text-slate-500 font-mono truncate max-w-[50%]"></span>
                    </div>

                    <div class="flex-1 rounded-xl border border-slate-700/70 bg-slate-950/70 overflow-hidden flex items-center justify-center min-h-[200px]">
                        <img id="modal_img_tag" src="" alt="Preview" class="hidden max-h-[58vh] w-full object-contain">
                        <iframe id="modal_pdf_frame" src="" class="hidden w-full h-[58vh] bg-white"></iframe>
                        <div id="modal_file_icon" class="hidden text-center p-6">
                            <i id="modal_file_icon_i" class="fa-solid fa-file-lines text-5xl text-blue-400"></i>
                            <div id="modal_file_ext" class="text-xs font-bold text-slate-200 mt-2"></div>
                            <p class="text-xs text-slate-400 mt-1">No preview for this file type. Download it to view.</p>
                        </div>
                        <div id="modal_no_file" class="hidden text-center p-6 text-slate-500">
                            <i class="fa-regular fa-image text-4xl"></i>
                            <p class="text-xs font-bold mt-2">No file uploaded for this order.</p>
                        </div>
                    </div>

                    <div id="modal_file_actions" class="grid grid-cols-2 gap-2">
                        <a id="modal_file_link" href="#" target="_blank" class="bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold py-2.5 rounded-xl text-center transition shadow">
                            <i class="fa-solid fa-up-right-from-square mr-1"></i> Open Full Size
                        </a>
                        <a id="modal_file_dl" href="#" download class="bg-slate-800 hover:bg-slate-700 text-white text-xs sm:text-sm font-bold py-2.5 rounded-xl text-center transition">
                            <i class="fa-solid fa-file-arrow-down mr-1"></i> Download
                        </a>
                    </div>
                </div>

                <!-- RIGHT: Receipt / details -->
                <div class="md:col-span-2 p-5 flex flex-col gap-3.5 md:max-h-[90vh] md:overflow-y-auto">
                    <div class="pr-8">
                        <span id="modal_code" class="font-mono font-bold text-blue-400 text-sm"></span>
                        <h3 id="modal_product" class="text-xl font-extrabold text-white mt-0.5 leading-snug"></h3>
                        <span id="modal_status_badge" class="inline-block mt-2 text-xs px-3 py-1 rounded-full uppercase font-bold border"></span>
                    </div>

                    <div id="modal_tracker" class="flex items-center"></div>

                    <div id="modal_anim_wait" class="hidden">
                        <div class="pp-anim pp-wait flex items-center gap-3 rounded-xl border p-3">
                            <div class="pp-icon"><span class="pp-ring"></span><span class="pp-ring r2"></span><div class="pp-core"><i class="fa-solid fa-hourglass-half pp-flip"></i></div></div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="text-sm font-bold" style="color: var(--pp2);">Waiting in line<span class="pp-dots"><b></b><b></b><b></b></span></div>
                                <div class="pp-queue"><span></span><span></span><span></span><span></span><span></span></div>
                                <p class="text-xs text-slate-400">Your order is in the queue. The shop will start on it shortly.</p>
                            </div>
                        </div>
                    </div>

                    <div id="modal_anim_ready" class="hidden">
                        <div class="pp-anim pp-ready flex items-center gap-3 rounded-xl border p-3">
                            <div class="pp-icon"><i class="fa-solid fa-star pp-spark s1"></i><i class="fa-solid fa-star pp-spark s2"></i><i class="fa-solid fa-star pp-spark s3"></i><div class="pp-core"><i class="fa-solid fa-bag-shopping pp-hop"></i></div><span class="pp-check"><i class="fa-solid fa-check"></i></span></div>
                            <div class="flex-1 min-w-0 space-y-0.5">
                                <div class="text-sm font-bold" style="color: var(--pp2);">Ready for Pickup!</div>
                                <p class="text-xs text-slate-300">Your order is ready. Please show your order code at the shop.</p>
                            </div>
                        </div>
                    </div>

                    <div id="modal_anim" class="hidden">
                        <div class="pp-anim flex items-center gap-3 rounded-xl border p-3" style="background: rgba(245,158,11,.08); border-color: rgba(245,158,11,.35);">
                            <div class="pp-printer"><div class="pp-paper"></div><div class="pp-body"><i class="fa-solid fa-print"></i></div></div>
                            <div class="flex-1 min-w-0 space-y-1.5">
                                <div class="text-sm font-bold" style="color: var(--pp2);">Printing your order<span class="pp-dots"><b></b><b></b><b></b></span></div>
                                <div class="pp-bar"><span></span></div>
                                <p class="text-xs text-slate-400">Your order is being printed. Please wait a moment!</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-slate-700"></div>

                    <dl class="space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-slate-400 font-semibold uppercase text-xs">Ordered By</dt><dd id="modal_name" class="text-blue-300 font-bold text-right"></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400 font-semibold uppercase text-xs">Date & Time</dt><dd id="modal_date" class="text-slate-200 font-medium text-right"></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-400 font-semibold uppercase text-xs">Payment</dt><dd id="modal_payment" class="text-emerald-400 font-bold text-right"></dd></div>
                    </dl>

                    <div id="modal_proof_box" class="hidden rounded-xl border border-amber-500/40 bg-amber-500/10 p-3 space-y-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-amber-300"><i class="fa-solid fa-camera mr-1.5"></i>Proof of Delivery</span>
                        <button type="button" id="modal_proof_btn" class="block w-full rounded-lg overflow-hidden border border-amber-500/40 bg-slate-950/70 cursor-zoom-in">
                            <img id="modal_proof_img" src="" alt="Proof of delivery" class="w-full max-h-48 object-contain">
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <a id="modal_proof_link" href="#" target="_blank" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold py-2 rounded-lg text-center transition"><i class="fa-solid fa-up-right-from-square mr-1"></i> Full Size</a>
                            <a id="modal_proof_dl" href="#" download class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-2 rounded-lg text-center transition"><i class="fa-solid fa-file-arrow-down mr-1"></i> Download</a>
                        </div>
                    </div>

                    <div class="bg-slate-950/70 p-3.5 rounded-xl border border-slate-800 text-xs sm:text-sm">
                        <span class="text-slate-400 block mb-1 font-bold uppercase text-[11px]">Specifications</span>
                        <p id="modal_specs" class="text-slate-300 italic leading-relaxed"></p>
                    </div>

                    <div class="border-t border-dashed border-slate-700"></div>

                    <div class="flex items-end justify-between">
                        <span class="text-xs uppercase font-bold text-slate-400 tracking-widest">Total</span>
                        <span id="modal_total" class="text-3xl font-black text-white"></span>
                    </div>

                    <button onclick="closeDetailsModal()" class="mt-auto w-full bg-slate-800 hover:bg-slate-700 text-white text-xs sm:text-sm font-bold py-2.5 rounded-xl transition shadow">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PROOF LIGHTBOX -->
    <div id="proofLightbox" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/90 backdrop-blur-sm p-4" onclick="if(event.target===this) closeProofLightbox()">
        <button onclick="closeProofLightbox()" class="absolute top-4 right-4 text-white text-xl bg-slate-800/90 hover:bg-slate-700 w-10 h-10 rounded-full flex items-center justify-center shadow"><i class="fa-solid fa-xmark"></i></button>
        <img id="proofLightboxImg" src="" alt="Proof of delivery" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl">
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900/90 border-t border-slate-700/60 py-6 text-center text-slate-400 mt-10 backdrop-blur space-y-2">
        <div class="flex flex-wrap items-center justify-center gap-2.5 text-xs sm:text-sm text-slate-200 font-medium px-4">
            <i class="fa-solid fa-location-dot text-rose-400 text-base"></i>
            <span>Anyatam, San Ildefonso, Bulacan &ndash; Dimla Street, Purok 6 (beside Far East Fuel Company)</span>
            <a href="https://www.google.com/maps/search/?api=1&query=Far+East+Fuel+Company+Anyatam+San+Ildefonso+Bulacan" target="_blank" rel="noopener" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3.5 py-1.5 rounded-lg transition shadow"><i class="fa-solid fa-diamond-turn-right mr-1"></i> Get Directions</a>
        </div>
        <div class="text-xs text-slate-500">&copy; 2026 SmartPrint Shop. All rights reserved.</div>
    </footer>

    <script>
    const STEP_LABELS = ['Pending', 'Printing', 'Ready', 'Done'];
    const FILE_ENDPOINT = '{{ route('order.file') }}';
    const UPLOADS_BASE = '{{ asset('uploads') }}/';

    function statusMeta(s) {
        const l = String(s || '').toLowerCase();
        if (l.includes('cancel')) return { step: -1, color: '#f43f5e', cls: 'text-rose-300 bg-rose-500/15 border-rose-500/40' };
        if (l.includes('ready')) return { step: 2, color: '#8b5cf6', cls: 'text-violet-300 bg-violet-500/15 border-violet-500/40' };
        if (/complete|done|claim|success|deliver/.test(l)) return { step: 3, color: '#10b981', cls: 'text-emerald-300 bg-emerald-500/15 border-emerald-500/40' };
        if (/process|print|progress/.test(l)) return { step: 1, color: '#0ea5e9', cls: 'text-sky-300 bg-sky-500/15 border-sky-500/40' };
        return { step: 0, color: '#f59e0b', cls: 'text-amber-300 bg-amber-500/15 border-amber-500/40' };
    }

    function getOrderFile(o) {
        const keys = ['document_file', 'file_path', 'file_name', 'uploaded_file', 'file', 'document', 'attachment'];
        for (const k of keys) { if (o[k]) return String(o[k]); }
        return '';
    }

    function openDetailsModal(order) {
        document.getElementById('modal_code').innerText = order.order_code;
        document.getElementById('modal_product').innerText = order.product_name;
        document.getElementById('modal_name').innerText = order.full_name;

        const orderDate = new Date(String(order.created_at).replace(' ', 'T'));
        document.getElementById('modal_date').innerText = isNaN(orderDate) ? order.created_at
            : orderDate.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

        document.getElementById('modal_payment').innerText = String(order.payment_method).toUpperCase() + " (" + order.payment_status + ")";
        document.getElementById('modal_specs').innerText = order.specifications ? order.specifications : "No additional specifications.";
        document.getElementById('modal_total').innerText = '₱' + Number(order.total || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const meta = statusMeta(order.status);
        const badge = document.getElementById('modal_status_badge');
        badge.innerText = order.status;
        badge.className = 'inline-block mt-2 text-xs px-3 py-1 rounded-full uppercase font-bold border ' + meta.cls;

        const tracker = document.getElementById('modal_tracker');
        if (meta.step < 0) {
            tracker.innerHTML = '<p class="text-xs font-bold text-rose-300"><i class="fa-solid fa-ban mr-1"></i> This order was cancelled.</p>';
        } else {
            tracker.innerHTML = STEP_LABELS.map((label, i) => {
                const on = i <= meta.step;
                const dot = `<div class="flex flex-col items-center">
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center ${on ? 'border-transparent' : 'border-slate-600 bg-slate-800'}" style="${on ? 'background:' + meta.color : ''}">
                        ${i < meta.step ? '<i class="fa-solid fa-check text-[10px] text-white"></i>' : ''}
                    </div>
                    <span class="text-[11px] mt-1 font-semibold ${on ? 'text-slate-200' : 'text-slate-500'}">${label}</span></div>`;
                const line = i < STEP_LABELS.length - 1
                    ? `<div class="flex-1 h-0.5 mx-1.5 -mt-4 rounded" style="background:${i < meta.step ? meta.color : '#334155'}"></div>` : '';
                return dot + line;
            }).join('');
        }

        document.getElementById('modal_anim').classList.toggle('hidden', meta.step !== 1);
        document.getElementById('modal_anim_wait').classList.toggle('hidden', meta.step !== 0);
        document.getElementById('modal_anim_ready').classList.toggle('hidden', meta.step !== 2);

        const proofBox = document.getElementById('modal_proof_box');
        if (order.proof_image) {
            const purl = UPLOADS_BASE + order.proof_image;
            document.getElementById('modal_proof_img').src = purl;
            document.getElementById('modal_proof_link').href = purl;
            document.getElementById('modal_proof_dl').href = purl;
            document.getElementById('modal_proof_btn').onclick = () => openProofLightbox(purl);
            proofBox.classList.remove('hidden');
        } else {
            document.getElementById('modal_proof_img').src = '';
            proofBox.classList.add('hidden');
        }

        const img = document.getElementById('modal_img_tag');
        const pdf = document.getElementById('modal_pdf_frame');
        const icon = document.getElementById('modal_file_icon');
        const noFile = document.getElementById('modal_no_file');
        const actions = document.getElementById('modal_file_actions');
        [img, pdf, icon, noFile].forEach(el => el.classList.add('hidden'));
        img.src = ''; pdf.src = '';

        const file = getOrderFile(order);
        if (file) {
            const url = FILE_ENDPOINT + '?order=' + encodeURIComponent(order.order_id);
            const ext = file.split('.').pop().toLowerCase();
            document.getElementById('modal_file_name').innerText = file.split('/').pop();
            document.getElementById('modal_file_link').href = url;
            document.getElementById('modal_file_dl').href = url;
            actions.classList.remove('hidden');

            if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                img.src = url; img.classList.remove('hidden');
            } else if (ext === 'pdf') {
                pdf.src = url; pdf.classList.remove('hidden');
            } else {
                document.getElementById('modal_file_ext').innerText = ext.toUpperCase() + ' FILE';
                document.getElementById('modal_file_icon_i').className = 'fa-solid ' + (['doc', 'docx'].includes(ext) ? 'fa-file-word' : ['xls', 'xlsx'].includes(ext) ? 'fa-file-excel' : 'fa-file-lines') + ' text-5xl text-blue-400';
                icon.classList.remove('hidden');
            }
        } else {
            document.getElementById('modal_file_name').innerText = '';
            noFile.classList.remove('hidden');
            actions.classList.add('hidden');
        }

        const modal = document.getElementById('detailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetailsModal() {
        const modal = document.getElementById('detailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('modal_pdf_frame').src = '';
    }
    function openProofLightbox(url) {
        document.getElementById('proofLightboxImg').src = url;
        const lb = document.getElementById('proofLightbox');
        lb.classList.remove('hidden'); lb.classList.add('flex');
    }
    function closeProofLightbox() {
        const lb = document.getElementById('proofLightbox');
        lb.classList.add('hidden'); lb.classList.remove('flex');
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            if (!document.getElementById('proofLightbox').classList.contains('hidden')) closeProofLightbox();
            else closeDetailsModal();
        }
    });
    setInterval(() => { if (document.getElementById('detailsModal').classList.contains('hidden') && document.getElementById('proofLightbox').classList.contains('hidden')) location.reload(); }, 20000);
    </script>
</body>
</html>