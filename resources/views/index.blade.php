@php
use Illuminate\Support\Facades\DB;

// Kunin ang user ID mula sa Laravel session
$user_id = session('user_id') ?? session('id') ?? 0;

// Kunin ang user details gamit ang Laravel DB facade
$logged_user = DB::table('users')->where('user_id', $user_id)->first();

$message = session('message', '');
$ordered_success = request()->get('ordered') === 'success';
if (request()->get('payment') === 'cancelled') {
    $message = "Payment process was cancelled.";
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrint Shop - Customer Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.96)), url('{{ asset("images/a634de15b82268db1566459c3ca3edc7.jpg") }}') no-repeat center center fixed;
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
<body class="min-h-screen flex flex-col justify-between antialiased text-sm" data-uid="{{ (int)$user_id }}">

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
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold bg-blue-600 text-white transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-cart-plus"></i> <span class="hidden md:inline">Place Order</span>
                </a>
                
                <a href="{{ route('my.orders') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left"></i> <span class="hidden md:inline">My Orders</span>
                </a>

                <a href="{{ route('messages') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-comments"></i> <span class="hidden md:inline">Customer Service</span>
                </a>

                <!-- Notification Bell -->
                <div class="relative">
                    <button type="button" id="notifBell" onclick="toggleNotifPanel()" class="relative w-9 h-9 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-200 text-sm flex items-center justify-center transition" title="Notifications">
                        <i class="fa-solid fa-bell"></i>
                        <span id="notifBadge" class="hidden absolute -top-1.5 -right-1.5 min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-extrabold flex items-center justify-center border-2 border-slate-900">0</span>
                    </button>
                    <div id="notifPanel" class="hidden absolute right-0 mt-2 w-72 glass-card rounded-xl shadow-2xl border border-slate-700 overflow-hidden z-50">
                        <div class="flex items-center justify-between px-3.5 py-2.5 border-b border-slate-800">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-white">Notifications</span>
                            <button type="button" onclick="clearNotifs()" class="text-[10px] font-bold text-slate-400 hover:text-white">Clear all</button>
                        </div>
                        <div id="notifList" class="max-h-72 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="h-5 w-px bg-slate-700 mx-0.5"></div>

                <div class="flex items-center gap-2 bg-slate-950/60 px-2.5 py-1 rounded-xl border border-slate-800">
                    <a href="{{ route('profile') }}" class="w-8 h-8 rounded-full overflow-hidden border border-blue-500 bg-slate-800 flex items-center justify-center hover:border-blue-400 transition shrink-0" title="Profile Settings">
                        @if (!empty($logged_user->profile_pic) && file_exists(public_path("uploads/profiles/" . $logged_user->profile_pic)))
                            <img src="{{ asset('uploads/profiles/' . $logged_user->profile_pic) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user text-xs text-slate-400"></i>
                        @endif
                    </a>
                    <span class="text-xs font-bold text-white tracking-wide hidden sm:inline-block">Welcome {{ $logged_user->full_name ?? 'Jerome' }}!</span>
                </div>

                <a href="{{ route('logout') }}" title="Logout" class="w-9 h-9 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-bold border border-red-500/20 transition flex items-center justify-center">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Banner Section -->
    <section class="relative bg-slate-950 border-b border-slate-800 py-10 px-4 text-center overflow-hidden">
        <div class="absolute inset-0 z-0 opacity-65">
            <img src="{{ asset('images/a634de15b82268db1566459c3ca3edc7.jpg') }}" alt="Hero Background" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 z-0 bg-gradient-to-b from-slate-950/40 via-transparent to-slate-950/50"></div>
        <div class="relative z-10 max-w-3xl mx-auto space-y-2">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-400 px-3 py-1 bg-blue-500/10 border border-blue-500/30 rounded-full inline-block">
                Professional Printing & Xerox Services
            </span>
            <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white drop-shadow">
                YOUR ONE-STOP PRINTING SOLUTION
            </h2>
            <p class="text-slate-300 text-xs max-w-md mx-auto font-medium">
                Printing shop photo, tarpaulin, o magpa-xerox sa abot-kayang presyo starting at (₱3.00/sheet).
            </p>
        </div>
    </section>

    <!-- Divider -->
    <div class="h-4 w-full border-y border-slate-700/60 bg-slate-900"></div>

    <!-- Body wrapper -->
    <div class="relative">
        <div class="absolute inset-0 z-0 opacity-100 pointer-events-none">
            <img src="{{ asset('images/color-splash.png') }}" alt="Body Background" class="w-full h-full object-cover" style="filter: saturate(1.2) contrast(1.05);">
        </div>

        <!-- Features Section -->
        <section class="relative z-10 py-10 px-4 border-b border-slate-800/80">
            <div class="relative z-10 max-w-5xl mx-auto text-center space-y-6">
                <h3 class="inline-block text-sm sm:text-base font-extrabold text-white tracking-wider uppercase bg-slate-900/85 px-4 py-1.5 rounded-lg border border-slate-700 backdrop-blur">
                    FEATURES FOR EVERYONE
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="glass-card p-4 rounded-xl space-y-2 text-center border border-slate-800 shadow">
                        <div class="w-10 h-10 mx-auto rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs">Quick Services</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Save time and improve efficiency of your business.</p>
                    </div>

                    <div class="glass-card p-4 rounded-xl space-y-2 text-center border border-slate-800 shadow">
                        <div class="w-10 h-10 mx-auto rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs">Improved security</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Additional level of security to keep your information safe.</p>
                    </div>

                    <div class="glass-card p-4 rounded-xl space-y-2 text-center border border-slate-800 shadow">
                        <div class="w-10 h-10 mx-auto rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-tint"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs">Close to you</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Each of our stores is locally owned and operated.</p>
                    </div>

                    <div class="glass-card p-4 rounded-xl space-y-2 text-center border border-slate-800 shadow">
                        <div class="w-10 h-10 mx-auto rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs">Flexible Prices</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Xerox is only ₱3.00 per sheet with flexible printing discounts.</p>
                    </div>

                    <div class="glass-card p-4 rounded-xl space-y-2 text-center border border-slate-800 shadow">
                        <div class="w-10 h-10 mx-auto rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs">Easy Payments</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">You can pay with GCash, Maya, or Cash on Pickup.</p>
                    </div>

                    <div class="glass-card p-4 rounded-xl space-y-2 text-center border border-slate-800 shadow">
                        <div class="w-10 h-10 mx-auto rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs">All-in-One</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">We offer scanning, copying, xerox, and printing services.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Container (Place Order Form) -->
        <main class="relative z-20 max-w-3xl mx-auto w-full px-4 py-8 flex-1">

            @if (!empty($message))
                <div class="bg-slate-900/90 backdrop-blur border border-blue-500/50 text-blue-300 p-3 rounded-xl mb-4 text-xs font-semibold flex items-center gap-2 shadow">
                    <i class="fa-solid fa-circle-info text-sm text-blue-400"></i>
                    <span>{{ $message }}</span>
                </div>
            @endif

            <!-- ORDER FORM -->
            <form method="POST" action="{{ route('checkout') }}" enctype="multipart/form-data" class="glass-card relative z-20 p-5 sm:p-8 rounded-2xl shadow-xl space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-2.5">1. Select Service Type</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                        <!-- Paper Print -->
                        <label id="card-paper" onclick="selectServiceCard('paper')" class="service-card flex flex-col items-center justify-center p-3.5 rounded-xl border-2 border-blue-500 bg-slate-950/90 cursor-pointer hover:border-blue-400 transition shadow-sm">
                            <input type="radio" id="radio-paper" name="product_type" value="paper" checked class="sr-only pointer-events-none">
                            <i class="fa-solid fa-file-lines text-xl text-blue-400 mb-1.5 pointer-events-none"></i>
                            <span class="text-xs font-bold text-white pointer-events-none">Paper Print</span>
                        </label>

                        <!-- Xerox -->
                        <label id="card-xerox" onclick="selectServiceCard('xerox')" class="service-card flex flex-col items-center justify-center p-3.5 rounded-xl border-2 border-slate-700 bg-slate-950/80 cursor-pointer hover:border-blue-400 transition shadow-sm">
                            <input type="radio" id="radio-xerox" name="product_type" value="xerox" class="sr-only pointer-events-none">
                            <i class="fa-solid fa-copy text-xl text-amber-400 mb-1.5 pointer-events-none"></i>
                            <span class="text-xs font-bold text-white pointer-events-none">Xerox (₱3)</span>
                        </label>

                        <!-- Photo Paper -->
                        <label id="card-photo" onclick="selectServiceCard('photo')" class="service-card flex flex-col items-center justify-center p-3.5 rounded-xl border-2 border-slate-700 bg-slate-950/80 cursor-pointer hover:border-blue-400 transition shadow-sm">
                            <input type="radio" id="radio-photo" name="product_type" value="photo" class="sr-only pointer-events-none">
                            <i class="fa-solid fa-image text-xl text-emerald-400 mb-1.5 pointer-events-none"></i>
                            <span class="text-xs font-bold text-white pointer-events-none">Photo Paper</span>
                        </label>

                        <!-- Tarpaulin -->
                        <label id="card-tarp" onclick="selectServiceCard('tarp')" class="service-card flex flex-col items-center justify-center p-3.5 rounded-xl border-2 border-slate-700 bg-slate-950/80 cursor-pointer hover:border-blue-400 transition shadow-sm">
                            <input type="radio" id="radio-tarp" name="product_type" value="tarp" class="sr-only pointer-events-none">
                            <i class="fa-solid fa-scroll text-xl text-indigo-400 mb-1.5 pointer-events-none"></i>
                            <span class="text-xs font-bold text-white pointer-events-none">Tarpaulin</span>
                        </label>

                        <!-- Vinyl Sticker -->
                        <label id="card-sticker" onclick="selectServiceCard('sticker')" class="service-card flex flex-col items-center justify-center p-3.5 rounded-xl border-2 border-slate-700 bg-slate-950/80 cursor-pointer hover:border-blue-400 transition shadow-sm">
                            <input type="radio" id="radio-sticker" name="product_type" value="sticker" class="sr-only pointer-events-none">
                            <i class="fa-solid fa-note-sticky text-xl text-purple-400 mb-1.5 pointer-events-none"></i>
                            <span class="text-xs font-bold text-white pointer-events-none">Vinyl Sticker</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-2">2. Upload File / Document</label>
                    <input type="file" name="document_file" required class="w-full text-xs text-slate-300 file:bg-blue-600 file:text-white file:px-3.5 file:py-2 file:mr-3 file:rounded-lg file:border-0 cursor-pointer font-semibold bg-slate-950 border border-slate-700 rounded-xl p-1" id="documentInput" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" onchange="previewUpload(this)">

                    <!-- Preview of the uploaded file -->
                    <div id="uploadPreview" class="hidden mt-3 bg-slate-950 border border-blue-500/40 rounded-xl p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-400"><i class="fa-solid fa-eye mr-1"></i> Print Preview</span>
                            <button type="button" onclick="clearUpload()" class="text-[10px] font-bold text-red-400 hover:text-red-300 bg-red-500/10 border border-red-500/30 px-2 py-0.5 rounded transition">
                                <i class="fa-solid fa-xmark mr-1"></i> Remove
                            </button>
                        </div>
                        <div class="rounded-lg overflow-hidden border border-slate-800 flex items-center justify-center min-h-[160px]" style="background-color:#060b16;background-image:linear-gradient(rgba(148,163,184,.08) 1px, transparent 1px),linear-gradient(90deg, rgba(148,163,184,.08) 1px, transparent 1px);background-size:20px 20px;">
                            <img id="uploadPreviewImg" src="" alt="Preview" class="hidden max-h-72 w-full object-contain">
                            <iframe id="uploadPreviewPdf" src="" class="hidden w-full h-72 bg-white"></iframe>
                            <div id="uploadPreviewIcon" class="hidden text-center p-4">
                                <i id="uploadPreviewIconI" class="fa-solid fa-file-lines text-4xl text-blue-400"></i>
                                <div id="uploadPreviewExt" class="text-xs font-extrabold text-slate-200 mt-1.5"></div>
                                <p class="text-[10px] text-slate-400 mt-1">No preview is available for this file type, but it will still be sent.</p>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-300 mt-2 truncate"><i class="fa-solid fa-paperclip text-blue-400 mr-1"></i><span id="uploadPreviewName" class="font-semibold"></span> <span id="uploadPreviewSize" class="text-slate-500"></span></p>
                    </div>
                </div>

                <div class="border-t border-b border-slate-800 py-5 space-y-4">
                    <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">3. Specifications & Quantity</label>

                    <div id="service-paper" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Number of Pages</label>
                            <input type="number" id="pages" name="pages_count" min="1" value="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Paper Size</label>
                            <select id="paper_size" name="paper_size" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" onchange="calculateTotal()">
                                <option value="A4">A4 Size</option>
                                <option value="Short">Short / Letter Size</option>
                                <option value="Long">Long / Folio (+₱0.25)</option>
                            </select>
                        </div>
                    </div>

                    <div id="service-xerox" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Number of Pages / Sheets (₱3.00/pc)</label>
                            <input type="number" id="xerox_pages" name="xerox_pages" min="1" value="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Copies</label>
                            <input type="number" id="xerox_copies" name="xerox_copies" min="1" value="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div id="service-photo" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Photo Size</label>
                            <select id="photo_size" name="photo_size" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" onchange="calculateTotal()">
                                <option value="4R">4R Size (₱10.00)</option>
                                <option value="A4_Photo">A4 Photo Size (₱50.00)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Quantity</label>
                            <input type="number" id="photo_q" name="photo_qty" min="1" value="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div id="service-tarp" class="hidden grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Width (Feet)</label>
                            <input type="number" step="0.5" id="tarp_w" name="tarp_width" value="2" min="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Height (Feet)</label>
                            <input type="number" step="0.5" id="tarp_h" name="tarp_height" value="3" min="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Quantity</label>
                            <input type="number" id="tarp_q" name="tarp_qty" value="1" min="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div id="service-sticker" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sheet Size</label>
                            <select id="sticker_size" name="sticker_size" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" onchange="calculateTotal()">
                                <option value="A4">A4 Sheet (₱35.00)</option>
                                <option value="A3">A3 Sheet (₱65.00)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Quantity</label>
                            <input type="number" id="sticker_q" name="sticker_qty" value="1" min="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div id="hq_option_box">
                        <label class="flex items-center p-3 rounded-lg border border-amber-500/40 bg-amber-500/10 cursor-pointer">
                            <input type="checkbox" id="is_high_quality" name="is_high_quality" class="w-3.5 h-3.5 text-amber-500 rounded" onchange="calculateTotal()">
                            <span class="ml-2 text-xs font-bold text-amber-300">High Quality Print Option</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-1">Notes / Instructions</label>
                        <textarea name="specifications" rows="2" placeholder="Other details or instructions..." class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                </div>

                <!-- REAL-TIME ESTIMATED TOTAL DISPLAY -->
                <div class="bg-slate-950 p-3.5 rounded-xl border border-blue-500/30 flex justify-between items-center shadow-inner">
                    <div>
                        <span class="text-xs text-slate-400 block font-semibold">Estimated Total Amount:</span>
                        <span class="text-[10px] text-slate-500 font-bold">Standard Computation Price</span>
                    </div>
                    <div class="text-xl font-black text-blue-400" id="displayTotal">₱0.00</div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-extrabold py-3.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md shadow-blue-600/30">
                    Proceed to Checkout <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </form>
        </main>
    </div>

    <!-- Company History Section -->
    <section class="py-12 px-4 bg-slate-950/60 border-t border-slate-800/80 backdrop-blur">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="space-y-3">
                <h3 class="text-lg sm:text-xl font-extrabold text-white uppercase tracking-wider">Smart Printing Shop</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    The SmartPrint concept was introduced as a modern alternative to traditional print shops and xerox centers. Throughout its evolution, it has continued to define and lead the business services category it created.
                </p>
                <p class="text-xs text-slate-300 leading-relaxed">
                    We offer a variety of printing, scanning, xerox (₱3.00/sheet), large-format tarpaulin layout, and document services with convenient locations and world-class customer service.
                </p>
            </div>
            <div class="rounded-xl overflow-hidden shadow-xl border border-slate-800">
                <img src="{{ asset('images/a634de15b82268db1566459c3ca3edc7.jpg') }}" alt="Company History" class="w-full h-52 object-cover">
            </div>
        </div>
    </section>

    <!-- Shop Location Section -->
    <section id="location" class="py-12 px-4 bg-slate-900/70 border-t border-slate-800/80 backdrop-blur">
        <div class="max-w-3xl mx-auto text-center space-y-4">
            <h3 class="text-lg sm:text-xl font-extrabold text-white uppercase tracking-wider">
                <i class="fa-solid fa-location-dot text-rose-400 mr-1.5"></i> Visit Our Shop
            </h3>
            <div class="glass-card rounded-2xl p-6 border border-slate-700 shadow-xl space-y-2.5">
                <div class="w-12 h-12 mx-auto rounded-xl bg-gradient-to-br from-rose-500 to-orange-500 flex items-center justify-center text-white text-xl shadow">
                    <i class="fa-solid fa-store"></i>
                </div>
                <p class="text-base sm:text-lg font-extrabold text-white">Anyatam, San Ildefonso, Bulacan</p>
                <p class="text-xs sm:text-sm text-slate-300 font-medium">Dimla Street, Purok 6</p>
                <p class="text-xs sm:text-sm text-amber-300 font-bold"><i class="fa-solid fa-gas-pump mr-1"></i> Beside Far East Fuel Company</p>
                <a href="https://www.google.com/maps/search/?api=1&query=Far+East+Fuel+Company+Anyatam+San+Ildefonso+Bulacan" target="_blank" rel="noopener" class="inline-block mt-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-5 py-2.5 rounded-lg transition shadow">
                    <i class="fa-solid fa-diamond-turn-right mr-1"></i> Get Directions
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900/90 border-t border-slate-700/60 py-5 text-center text-[11px] text-slate-400 backdrop-blur space-y-1">
        <div class="text-xs text-slate-300"><i class="fa-solid fa-location-dot text-rose-400 mr-1"></i> Anyatam, San Ildefonso, Bulacan &ndash; Dimla Street, Purok 6, beside Far East Fuel Company</div>
        <div>&copy; 2026 SmartPrint Shop. All rights reserved.</div>
    </footer>

@if ($ordered_success)
    <!-- THANK YOU OVERLAY (after checkout) -->
    <style>
        @keyframes ty-pop  { 0% { transform: scale(0); opacity: 0; } 60% { transform: scale(1.15); opacity: 1; } 100% { transform: scale(1); } }
        @keyframes ty-fill { from { width: 0%; } to { width: 100%; } }
        @keyframes ty-fade { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <div id="thankYouOverlay" class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4">
        <div class="glass-card w-full max-w-md p-8 rounded-2xl shadow-2xl border border-emerald-500/30 text-center space-y-4" style="animation: ty-fade .5s ease-out;">
            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-500 flex items-center justify-center text-white text-4xl shadow-lg shadow-emerald-500/40" style="animation: ty-pop .6s ease-out;">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="space-y-1.5">
                <h2 class="text-2xl font-black text-white tracking-tight">Thank you for purchasing!</h2>
                <p class="text-xs text-slate-300">Your order has been placed successfully. Taking you to <strong class="text-white">My Orders</strong>...</p>
            </div>
            <div class="h-1.5 rounded-full bg-slate-800 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="animation: ty-fill 3.5s linear forwards;"></div>
            </div>
            <a href="{{ route('my.orders') }}" class="inline-block bg-blue-600 hover:bg-blue-500 text-white text-xs font-extrabold uppercase tracking-wider px-6 py-2.5 rounded-lg transition shadow">
                Go to My Orders now <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <script>
        setTimeout(function () { window.location.href = "{{ route('my.orders') }}"; }, 3500);
    </script>
@endif

    <!-- Toast container -->
    <div id="toastBox" class="fixed top-20 right-4 z-[100] space-y-2 w-72 max-w-[calc(100vw-2rem)]"></div>

    <!-- JavaScript code for preview, calculation, and notifications -->
    <script>
        let uploadObjectUrl = null;
        function previewUpload(input) {
            const box = document.getElementById('uploadPreview');
            const img = document.getElementById('uploadPreviewImg');
            const pdf = document.getElementById('uploadPreviewPdf');
            const icon = document.getElementById('uploadPreviewIcon');
            if (uploadObjectUrl) { URL.revokeObjectURL(uploadObjectUrl); uploadObjectUrl = null; }
            [img, pdf, icon].forEach(el => el.classList.add('hidden'));
            img.src = ''; pdf.src = '';

            const file = input.files && input.files[0];
            if (!file) { box.classList.add('hidden'); return; }

            uploadObjectUrl = URL.createObjectURL(file);
            const ext = (file.name.split('.').pop() || '').toLowerCase();

            if (file.type.startsWith('image/')) {
                img.src = uploadObjectUrl; img.classList.remove('hidden');
            } else if (ext === 'pdf' || file.type === 'application/pdf') {
                pdf.src = uploadObjectUrl; pdf.classList.remove('hidden');
            } else {
                const iconClass = ['doc', 'docx'].includes(ext) ? 'fa-file-word' : ['xls', 'xlsx'].includes(ext) ? 'fa-file-excel' : ['ppt', 'pptx'].includes(ext) ? 'fa-file-powerpoint' : 'fa-file-lines';
                document.getElementById('uploadPreviewIconI').className = 'fa-solid ' + iconClass + ' text-4xl text-blue-400';
                document.getElementById('uploadPreviewExt').innerText = ext.toUpperCase() + ' FILE';
                icon.classList.remove('hidden');
            }

            document.getElementById('uploadPreviewName').innerText = file.name;
            const kb = file.size / 1024;
            document.getElementById('uploadPreviewSize').innerText = '(' + (kb >= 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb.toFixed(0) + ' KB') + ')';
            box.classList.remove('hidden');
        }

        function clearUpload() {
            const input = document.getElementById('documentInput');
            input.value = '';
            previewUpload(input);
        }

        function selectServiceCard(type) {
            const radio = document.getElementById('radio-' + type);
            if (radio) radio.checked = true;

            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('border-blue-500', 'bg-slate-950/90');
                card.classList.add('border-slate-700', 'bg-slate-950/80');
            });
            const activeCard = document.getElementById('card-' + type);
            if (activeCard) {
                activeCard.classList.remove('border-slate-700', 'bg-slate-950/80');
                activeCard.classList.add('border-blue-500', 'bg-slate-950/90');
            }

            ['paper', 'xerox', 'photo', 'tarp', 'sticker'].forEach(s => {
                const el = document.getElementById('service-' + s);
                if (el) el.classList.add('hidden');
            });
            const target = document.getElementById('service-' + type);
            if (target) target.classList.remove('hidden');

            const hqBox = document.getElementById('hq_option_box');
            if (hqBox) {
                hqBox.style.display = (type === 'xerox') ? 'none' : 'block';
            }

            calculateTotal();
        }

        function calculateTotal() {
            const checkedRadio = document.querySelector('input[name="product_type"]:checked');
            if (!checkedRadio) return;
            const productType = checkedRadio.value;
            const isHQCheckbox = document.getElementById('is_high_quality');
            const isHQ = isHQCheckbox ? isHQCheckbox.checked : false;
            let total = 0;

            if (productType === 'paper') {
                const pages = parseInt(document.getElementById('pages').value) || 1;
                const size = document.getElementById('paper_size').value;
                let rate = pages >= 21 ? 2.50 : (pages >= 11 ? 3.00 : 4.00);
                let stdAmount = (size === 'Long') ? 0.25 : 0.00;
                let hqExtra = isHQ ? 1.00 : 0.00;
                total = pages * (rate + stdAmount + hqExtra);
            } else if (productType === 'xerox') {
                const pages = parseInt(document.getElementById('xerox_pages').value) || 1;
                const copies = parseInt(document.getElementById('xerox_copies').value) || 1;
                total = pages * copies * 3.00;
            } else if (productType === 'photo') {
                const size = document.getElementById('photo_size').value;
                const qty = parseInt(document.getElementById('photo_q').value) || 1;
                let rate = (size === '4R') ? 10.00 : 50.00;
                let hqExtra = isHQ ? 5.00 : 0.00;
                total = qty * (rate + hqExtra);
            } else if (productType === 'tarp') {
                const w = parseFloat(document.getElementById('tarp_w').value) || 2;
                const h = parseFloat(document.getElementById('tarp_h').value) || 3;
                const qty = parseInt(document.getElementById('tarp_q').value) || 1;
                let rate = isHQ ? 18.00 : 15.00;
                total = (w * h) * rate * qty;
            } else if (productType === 'sticker') {
                const size = document.getElementById('sticker_size').value;
                const qty = parseInt(document.getElementById('sticker_q').value) || 1;
                let rate = (size === 'A4') ? 35.00 : 65.00;
                let hqExtra = isHQ ? 10.00 : 0.00;
                total = qty * (rate + hqExtra);
            }

            const displayEl = document.getElementById('displayTotal');
            if (displayEl) {
                displayEl.innerHTML = "₱" + total.toFixed(2);
            }
        }

        // Real-time Notification & Chat Polling Script
        (function () {
            const bodyEl = document.body;
            const UID = bodyEl.getAttribute('data-uid') || 0;
            const K_MAP = 'sp_c_status_' + UID, K_MAX = 'sp_c_max_' + UID, K_LIST = 'sp_c_list_' + UID;
            
            const META = {
                'Pending':           { icon: 'fa-hourglass-half', color: 'text-sky-400',     msg: 'Order placed! Waiting in line.' },
                'Processing':        { icon: 'fa-print',          color: 'text-amber-400',   msg: 'Your order is now being printed.' },
                'Ready for Pickup':  { icon: 'fa-bag-shopping',   color: 'text-violet-400',  msg: 'Your order is ready for pickup!' },
                'Successful Pickup': { icon: 'fa-circle-check',   color: 'text-emerald-400', msg: 'Order completed. Thank you for buying!' },
                'New Message':       { icon: 'fa-comment-dots',   color: 'text-blue-400',    msg: 'May bago kang mensahe mula sa Admin. I-tap para basahin.' }
            };

            const hrefFor = n => n.status === 'New Message' ? "{{ route('messages') }}" : "{{ route('my.orders') }}";
            
            const ls = {
                get(k, d) { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
                set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
            };
            const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

            let list = ls.get(K_LIST, []);

            function timeAgo(t) {
                const s = Math.floor((Date.now() - t) / 1000);
                if (s < 60) return 'Just now';
                if (s < 3600) return Math.floor(s / 60) + ' min ago';
                if (s < 86400) return Math.floor(s / 3600) + ' hr ago';
                return new Date(t).toLocaleDateString();
            }

            function render() {
                const box = document.getElementById('notifList');
                const badge = document.getElementById('notifBadge');
                if (!box || !badge) return;
                
                const unread = list.filter(n => !n.read).length;
                badge.textContent = unread > 9 ? '9+' : unread;
                badge.classList.toggle('hidden', unread === 0);
                
                if (!list.length) {
                    box.innerHTML = '<div class="p-6 text-center text-xs text-slate-500"><i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>Wala pang notifications.</div>';
                    return;
                }
                
                box.innerHTML = list.map(n => {
                    const m = META[n.status] || META['Pending'];
                    return '<a href="' + hrefFor(n) + '" class="flex gap-3 px-4 py-3 border-b border-slate-800/70 hover:bg-slate-800/60 transition ' + (n.read ? '' : 'bg-blue-500/10') + '">' +
                        '<i class="fa-solid ' + m.icon + ' ' + m.color + ' mt-0.5"></i>' +
                        '<div class="min-w-0"><p class="text-xs font-bold text-white">' + esc(n.status) + ' <span class="font-mono text-slate-400">' + esc(n.code) + '</span></p>' +
                        '<p class="text-[11px] text-slate-300">' + esc(n.msg) + '</p>' +
                        '<p class="text-[10px] text-slate-500 mt-0.5">' + timeAgo(n.t) + '</p></div></a>';
                }).join('');
            }

            function toast(n) {
                const m = META[n.status] || META['Pending'];
                const boxContainer = document.getElementById('toastBox');
                if (!boxContainer) return;

                const el = document.createElement('a');
                el.href = hrefFor(n);
                el.className = 'glass-card flex gap-3 items-start p-4 rounded-xl shadow-2xl border border-blue-500/40 transition-all duration-300 translate-x-4 opacity-0';
                el.innerHTML = '<i class="fa-solid ' + m.icon + ' ' + m.color + ' text-lg mt-0.5"></i>' +
                    '<div class="min-w-0"><p class="text-xs font-extrabold text-white">' + esc(n.status) + '</p>' +
                    '<p class="text-[11px] text-slate-300">' + esc(n.msg) + '</p></div>';
                
                boxContainer.appendChild(el);
                requestAnimationFrame(() => el.classList.remove('translate-x-4', 'opacity-0'));
                setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 300); }, 6000);
            }

            function push(order) {
                const m = META[order.status] || META['Pending'];
                const n = { 
                    t: Date.now(), 
                    status: order.status, 
                    code: order.status === 'New Message' ? '' : (order.order_code || ('#' + order.order_id)), 
                    msg: m.msg, 
                    read: false 
                };
                list.unshift(n);
                list = list.slice(0, 30);
                ls.set(K_LIST, list);
                toast(n);
                render();
            }

            async function poll() {
                try {
                    const r = await fetch("{{ route('notifications.fetch') }}", { cache: 'no-store', credentials: 'same-origin' });
                    if (!r.ok) return;
                    const data = await r.json();
                    
                    const K_ADM = 'sp_c_admmsgs_' + UID;
                    const admPrev = localStorage.getItem(K_ADM);
                    const admNow = parseInt(data.admin_msgs, 10) || 0;
                    
                    if (admPrev !== null && admNow > parseInt(admPrev, 10)) {
                        push({ status: 'New Message', order_code: '', order_id: '' });
                    }
                    try { localStorage.setItem(K_ADM, String(admNow)); } catch (e) {}

                } catch (e) { /* ignore network errors */ }
            }

            window.toggleNotifPanel = function () {
                const p = document.getElementById('notifPanel');
                if (!p) return;
                p.classList.toggle('hidden');
                if (!p.classList.contains('hidden')) {
                    list.forEach(n => n.read = true);
                    ls.set(K_LIST, list);
                    setTimeout(render, 1500);
                    const badge = document.getElementById('notifBadge');
                    if (badge) badge.classList.add('hidden');
                }
            };
            
            window.clearNotifs = function () { 
                list = []; 
                ls.set(K_LIST, list); 
                render(); 
            };

            document.addEventListener('click', e => {
                const p = document.getElementById('notifPanel');
                if (p && !p.classList.contains('hidden') && !e.target.closest('#notifPanel') && !e.target.closest('#notifBell')) {
                    p.classList.add('hidden');
                }
            });

            render();
            poll();
            setInterval(poll, 5000);
        })();

        window.onload = function() {
            calculateTotal();
        };
    </script>
</body>
</html>