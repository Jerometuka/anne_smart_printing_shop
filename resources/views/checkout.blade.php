<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrint Shop - Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.90), rgba(30, 41, 59, 0.95)), url('{{ asset("a634de15b82268db1566459c3ca3edc7.jpg") }}') no-repeat center center fixed;
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
<body class="min-h-screen flex flex-col justify-between antialiased">

    <header class="bg-slate-900/95 border-b border-slate-700/85 sticky top-0 z-50 backdrop-blur-md shadow-lg">
        <div class="w-full px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-print text-lg"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-lg text-white leading-tight">Anne tarpulin &amp; printing Shop</h1>
                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">Secure Checkout</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to Order
            </a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto w-full px-4 py-12 flex-1">

        @if ($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/40 text-rose-300 p-3 rounded-xl mb-4 text-xs font-semibold space-y-1">
                @foreach ($errors->all() as $error)
                    <p><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('payment.process') }}" method="POST" enctype="multipart/form-data" class="glass-card p-8 rounded-3xl shadow-2xl space-y-6">
            @csrf
            <input type="hidden" name="uploaded_doc" value="{{ $uploaded_file_name ?? '' }}">

            <h2 class="text-xl font-black text-white uppercase tracking-wider border-b border-slate-800 pb-3">
                <i class="fa-solid fa-bag-shopping text-blue-400 mr-2"></i> Order Checkout Summary
            </h2>

            <!-- NEW USER VOUCHER -->
            <div class="bg-gradient-to-r from-blue-900/50 to-indigo-950/60 border border-blue-500/40 p-4 rounded-2xl flex items-center justify-between shadow-inner">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow">
                        <i class="fa-solid fa-ticket text-lg"></i>
                    </div>
                    <div>
                        <span class="text-[10px] bg-blue-500 text-white px-2 py-0.5 rounded-full font-extrabold uppercase">New User Voucher</span>
                        <h4 class="text-xs font-bold text-white mt-1">Free 2 printed plain paper</h4>
                        <p class="text-[10px] text-slate-300">Applied automatically to your first account transaction!</p>
                    </div>
                </div>
                <span class="text-xs font-black text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/20">FREE</span>
            </div>

            <!-- Order Summary Box -->
            <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-2 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Service Item:</span>
                    <strong class="text-white">{{ $details_summary ?? 'Standard Print' }}</strong>
                </div>
                <div class="flex justify-between items-center gap-3 text-slate-400">
                    <span>File to Print:</span>
                    @if (!empty($uploaded_file_name))
                        <span class="flex items-center gap-2 text-emerald-400 font-bold">
                            @if (in_array(strtolower(pathinfo($uploaded_file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','gif']))
                                <img src="{{ asset('uploads/' . $uploaded_file_name) }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-slate-700">
                            @endif
                            <i class="fa-solid fa-circle-check"></i> Natanggap
                        </span>
                    @else
                        <span class="text-rose-400 font-bold text-right"><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $upload_error ?? 'Walang file' }}</span>
                    @endif
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Notes:</span>
                    <span class="text-slate-300 italic">{{ !empty($specifications) ? $specifications : 'None' }}</span>
                </div>
                <div class="border-t border-slate-800 pt-2 flex justify-between items-center">
                    <span class="text-sm font-bold text-white">Total Amount to Pay:</span>
                    <span class="text-2xl font-black {{ (float) ($total_amount ?? 0) == 0 ? 'text-emerald-400' : 'text-blue-400' }}">
                        @if ((float) ($total_amount ?? 0) == 0)
                            FREE (₱0.00)
                        @else
                            ₱{{ number_format($total_amount, 2) }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div>
                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-2.5">Select Payment Method</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center p-3 rounded-xl border-2 border-slate-700 bg-slate-950 cursor-pointer hover:border-blue-500">
                        <input type="radio" name="payment_method" value="gcash" checked onclick="toggleOnlinePayment(true)">
                        <span class="ml-2 text-xs font-bold text-blue-400">GCash</span>
                    </label>
                    <label class="flex items-center p-3 rounded-xl border-2 border-slate-700 bg-slate-950 cursor-pointer hover:border-blue-500">
                        <input type="radio" name="payment_method" value="maya" onclick="toggleOnlinePayment(true)">
                        <span class="ml-2 text-xs font-bold text-emerald-400">Maya</span>
                    </label>
                    <label class="flex items-center p-3 rounded-xl border-2 border-slate-700 bg-slate-950 cursor-pointer hover:border-blue-500">
                        <input type="radio" name="payment_method" value="cash_on_pickup" onclick="toggleOnlinePayment(false)">
                        <span class="ml-2 text-xs font-bold text-slate-300">Cash on Pickup</span>
                    </label>
                </div>
            </div>

            <!-- OWNER QR CODE & DETAILS & PROOF PREVIEW -->
            <div id="qr_container" class="space-y-4 bg-slate-950 border border-blue-500/40 p-5 rounded-2xl text-center">
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-wider">Scan QR Code to Pay</h3>
                <div class="w-36 h-36 mx-auto bg-white p-2 rounded-xl border border-slate-700 flex items-center justify-center shadow-md">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=09123456789" alt="Owner QR Code" class="w-full h-full object-contain">
                </div>
                <div>
                    <p class="text-xs text-slate-300 font-semibold">Owner Name: <strong class="text-white">Anne Trinidad</strong></p>
                    <p class="text-[11px] text-slate-400">GCash / Maya Number: <strong class="text-blue-400">0912-345-6789</strong></p>
                </div>

                <!-- PROOF OF PAYMENT UPLOAD WITH LIVE PREVIEW -->
                <div class="pt-3 border-t border-slate-800 text-left space-y-2">
                    <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">Upload Proof of Payment (Screenshot)</label>
                    <input type="file" name="proof_payment" id="proofInput" accept="image/*" onchange="previewImage(event)" class="w-full text-xs text-slate-300 file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-xl file:border-0 cursor-pointer font-semibold bg-slate-900 border border-slate-700 rounded-xl">
                    <p class="text-[10px] text-slate-400 italic">I-upload ang larawan ng resibo o screenshot ng inyong GCash/Maya payment.</p>

                    <div id="previewContainer" class="hidden mt-3 text-center">
                        <p class="text-[11px] font-bold text-blue-400 mb-1">Preview ng Inupload na Resibo:</p>
                        <img id="imagePreview" src="#" alt="Proof Preview" class="w-32 h-32 mx-auto object-cover rounded-xl border border-blue-500 shadow-md">
                    </div>
                </div>
            </div>

            <!-- PAYER CONTACT DETAILS -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-wider">Payer Contact Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <input type="text" name="payer_name" required value="{{ old('payer_name') }}" placeholder="Full Name" class="bg-slate-900 border border-slate-700 text-white text-xs rounded-xl p-3 focus:outline-none focus:border-blue-500">
                    <input type="email" name="payer_email" required value="{{ old('payer_email') }}" placeholder="Email Address" class="bg-slate-900 border border-slate-700 text-white text-xs rounded-xl p-3 focus:outline-none focus:border-blue-500">
                    <input type="text" name="payer_phone" required value="{{ old('payer_phone') }}" placeholder="Phone Number" class="bg-slate-900 border border-slate-700 text-white text-xs rounded-xl p-3 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-extrabold py-4 rounded-xl text-xs uppercase tracking-wider transition shadow-lg shadow-blue-600/30">
                Confirm &amp; Pay Order
            </button>
        </form>
    </main>

    <footer class="bg-slate-900/90 border-t border-slate-700/60 py-6 text-center text-xs text-slate-400 mt-12 backdrop-blur">
        &copy; 2026 SmartPrint Shop. All rights reserved.
    </footer>

    <script>
    function toggleOnlinePayment(isOnline) {
        const qrBox = document.getElementById('qr_container');
        qrBox.style.display = isOnline ? 'block' : 'none';
    }

    // Preview ng inupload na screenshot
    function previewImage(event) {
        const reader = new FileReader();
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');

        reader.onload = function () {
            imagePreview.src = reader.result;
            previewContainer.classList.remove('hidden');
        };

        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
    </script>
</body>
</html>