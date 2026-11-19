<!-- ORDERS TAB -->
<div id="tab-orders" class="space-y-6">
    <div class="flex justify-between items-center glass-card p-6 rounded-3xl shadow-sm">
        <h2 class="text-lg font-bold text-white flex items-center gap-3">
            <i class="fa-solid fa-box-archive text-amber-400 text-xl"></i> Active Customer Orders (First-Come, First-Served)
        </h2>
        <span class="text-xs font-bold bg-amber-500/10 text-amber-400 px-4 py-2 rounded-xl border border-amber-500/20">
            Active Orders: {{ $activeOrders->count() }}
        </span>
    </div>

    <div class="space-y-6">
        @forelse ($activeOrders as $ord)
            @php
                $st            = trim((string) $ord->status);
                $is_pending    = ($st === 'Pending' || $st === '');
                $is_processing = ($st === 'Processing');
                $is_ready      = ($st === 'Ready for Pickup');

                $f      = $ord->documentFilename();
                $furl   = $f !== '' ? route('admin.orders.file', $ord) : '';
                $fext   = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                $f_is_img = in_array($fext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $f_is_pdf = ($fext === 'pdf');
            @endphp
            <div class="glass-card p-8 rounded-3xl space-y-6 shadow-2xl border border-slate-700/80">
                <div class="flex flex-wrap justify-between items-center border-b border-slate-800 pb-4 gap-4">
                    <div class="flex items-center gap-3">
                        <span class="bg-amber-500 text-slate-950 font-black px-3 py-1 rounded-xl text-xs">ORDER ID: #{{ $ord->order_id }}</span>
                        <span class="font-mono font-bold text-amber-400 text-sm">{{ $ord->order_code }}</span>
                        <span class="bg-slate-800 text-slate-300 text-xs px-3 py-1 rounded-full uppercase font-bold tracking-wide">{{ $ord->payment_method }}</span>
                    </div>
                    <div class="text-xs text-slate-400">
                        <i class="fa-regular fa-clock mr-1.5 text-amber-400"></i> Ordered on: <strong class="text-white">{{ $ord->created_at?->format('F j, Y, g:i A') }}</strong>
                    </div>
                </div>

                @if ($is_processing)
                <div class="pp-anim flex items-center gap-4 rounded-2xl border p-4" style="background: rgba(245,158,11,.08); border-color: rgba(245,158,11,.35);">
                    <div class="pp-printer"><div class="pp-paper"></div><div class="pp-body"><i class="fa-solid fa-print"></i></div></div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="text-sm font-extrabold" style="color: var(--pp2);">Printing in progress<span class="pp-dots"><b></b><b></b><b></b></span></div>
                        <div class="pp-bar"><span></span></div>
                        <p class="text-[11px] text-slate-400">This order is being processed. The customer can see this status too.</p>
                    </div>
                </div>
                @endif

                @if ($is_pending)
                <div class="pp-anim pp-wait flex items-center gap-4 rounded-2xl border p-4">
                    <div class="pp-icon"><span class="pp-ring"></span><span class="pp-ring r2"></span><div class="pp-core"><i class="fa-solid fa-hourglass-half pp-flip"></i></div></div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="text-sm font-extrabold" style="color: var(--pp2);">Waiting in line<span class="pp-dots"><b></b><b></b><b></b></span></div>
                        <div class="pp-queue"><span></span><span></span><span></span><span></span><span></span></div>
                        <p class="text-[11px] text-slate-400">Not started yet. Change the status to Processing to begin printing.</p>
                    </div>
                </div>
                @endif

                @if ($is_ready)
                <div class="pp-anim pp-ready flex items-center gap-4 rounded-2xl border p-4">
                    <div class="pp-icon"><i class="fa-solid fa-star pp-spark s1"></i><i class="fa-solid fa-star pp-spark s2"></i><i class="fa-solid fa-star pp-spark s3"></i><div class="pp-core"><i class="fa-solid fa-bag-shopping pp-hop"></i></div><span class="pp-check"><i class="fa-solid fa-check"></i></span></div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="text-sm font-extrabold" style="color: var(--pp2);">Ready for Pickup!</div>
                        <p class="text-[11px] text-slate-300">Waiting for the customer to pick it up. Update to Successful Pickup once collected.</p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <div class="md:col-span-2 space-y-3">
                        <h3 class="text-xl font-extrabold text-white">{{ $ord->product_name }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Pages Count</span>
                                <strong class="text-white text-sm">{{ $ord->pages_count ?? 'N/A' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Paper Size</span>
                                <strong class="text-white text-sm">{{ $ord->paper_size ?? 'N/A' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Quantity</span>
                                <strong class="text-amber-400 text-sm">{{ $ord->quantity ?? '1' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">High Quality</span>
                                <strong class="text-emerald-400 text-sm">{{ !empty($ord->is_high_quality) ? 'Yes' : 'No' }}</strong>
                            </div>
                        </div>
                        <p class="text-xs text-slate-300"><i class="fa-regular fa-user mr-1.5 text-blue-400"></i> Customer Name: <span class="font-bold text-white text-sm">{{ $ord->customer->full_name }}</span></p>

                        @if (!empty($ord->specifications))
                            <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3 text-xs">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Customer Notes / Instructions</span>
                                <p class="text-slate-200 italic">{!! nl2br(e($ord->specifications)) !!}</p>
                            </div>
                        @endif

                        <!-- FILE TO PRINT (UPLOADED BY CUSTOMER) -->
                        <div class="bg-slate-950/60 border border-amber-500/30 rounded-2xl p-4 space-y-3">
                            <span class="text-amber-400 block text-[10px] uppercase font-extrabold tracking-widest"><i class="fa-solid fa-print mr-1"></i> File to Print (Uploaded by Customer)</span>
                            @if ($f !== '')
                                <div class="flex flex-col sm:flex-row gap-4 items-start">
                                    @if ($f_is_img)
                                        <a href="{{ $furl }}" target="_blank" class="block w-full sm:w-44 h-44 rounded-xl overflow-hidden border border-slate-700 bg-slate-900 shrink-0">
                                            <img src="{{ $furl }}" alt="Uploaded file" data-fallback-img class="w-full h-full object-contain hover:scale-105 transition">
                                            <span class="hidden w-full h-full flex items-center justify-center text-center text-xs text-rose-300 p-2">Hindi mahanap ang file sa server</span>
                                        </a>
                                    @elseif ($f_is_pdf)
                                        <div class="block w-full sm:w-44 h-44 rounded-xl overflow-hidden border border-slate-700 bg-slate-900 shrink-0">
                                            <iframe src="{{ $furl }}#toolbar=0&navpanes=0&view=FitH" title="PDF preview" class="w-full h-full bg-white"></iframe>
                                        </div>
                                    @else
                                        <div class="w-full sm:w-44 h-44 rounded-xl border border-slate-700 bg-slate-900 flex flex-col items-center justify-center shrink-0 text-center">
                                            <i class="fa-solid fa-file-lines text-blue-400 text-5xl"></i>
                                            <span class="text-xs font-extrabold text-slate-200 mt-2">{{ strtoupper($fext ?: 'FILE') }}</span>
                                        </div>
                                    @endif

                                    <div class="space-y-2 w-full">
                                        <p class="text-[11px] text-slate-300 break-all"><i class="fa-solid fa-paperclip text-amber-400 mr-1"></i>{{ basename($f) }}</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            <button type="button" data-print-url="{{ $furl }}" data-print-type="{{ $f_is_img ? 'img' : ($f_is_pdf ? 'pdf' : 'other') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold py-2.5 rounded-xl transition shadow">
                                                <i class="fa-solid fa-print mr-1"></i> Print
                                            </button>
                                            <a href="{{ $furl }}" target="_blank" class="text-center bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-2.5 rounded-xl transition">
                                                <i class="fa-solid fa-up-right-from-square mr-1"></i> Open
                                            </a>
                                            <a href="{{ $furl }}" download class="text-center bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-2.5 rounded-xl transition">
                                                <i class="fa-solid fa-file-arrow-down mr-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-500"><i class="fa-regular fa-image mr-1"></i> The customer did not upload a file for this order.</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-end justify-center bg-slate-950/40 p-6 rounded-2xl border border-slate-800/80 space-y-4">
                        <div class="text-right">
                            <span class="text-[10px] uppercase text-slate-400 block font-bold">Total Amount</span>
                            <span class="text-2xl font-black text-amber-400">₱{{ number_format($ord->total, 2) }}</span>
                        </div>

                        <form method="POST" action="{{ route('admin.orders.status', $ord) }}" enctype="multipart/form-data" class="w-full space-y-3">
                            @csrf
                            <div class="flex gap-2">
                                <select name="status" id="status_select_{{ $ord->order_id }}" data-proof-toggle="{{ $ord->order_id }}" class="flex-1 bg-slate-950 border border-slate-700 text-white text-xs rounded-xl p-3 focus:outline-none focus:border-amber-500 font-semibold">
                                    <option value="Pending" @selected($is_pending)>Pending</option>
                                    <option value="Processing" @selected($is_processing)>Processing</option>
                                    <option value="Ready for Pickup" @selected($is_ready)>Ready for Pickup</option>
                                    <option value="Successful Pickup">Successful Pickup (Complete)</option>
                                </select>
                                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold px-5 py-3 rounded-xl transition shadow-lg">Update</button>
                            </div>

                            <div id="proof_box_{{ $ord->order_id }}" class="hidden bg-amber-500/10 p-3 rounded-xl border border-amber-500/30 space-y-1">
                                <label class="block text-[10px] font-bold text-amber-400 uppercase">Upload Proof of Delivery Picture:</label>
                                <input type="file" name="proof_image" accept="image/*" class="w-full text-xs text-slate-300 file:bg-amber-500 file:text-slate-950 file:px-3 file:py-1.5 file:rounded-lg file:border-0 cursor-pointer font-semibold bg-slate-950 border border-slate-700 rounded-xl p-1">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-3xl p-16 text-center text-slate-400 text-sm shadow-sm">
                <i class="fa-solid fa-box-open text-5xl mb-4 text-slate-500"></i>
                <p class="font-semibold text-slate-300">No active customer orders found.</p>
            </div>
        @endforelse
    </div>
</div>