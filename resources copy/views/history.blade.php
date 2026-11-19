<!-- HISTORY TAB -->
<div id="tab-history" class="hidden space-y-6">
    <div class="flex justify-between items-center glass-card p-6 rounded-3xl shadow-sm">
        <h2 class="text-lg font-bold text-white flex items-center gap-3">
            <i class="fa-solid fa-clock-rotate-left text-emerald-400 text-xl"></i> Completed Orders History
        </h2>
        <span class="text-xs font-bold bg-emerald-500/10 text-emerald-400 px-4 py-2 rounded-xl border border-emerald-500/20">
            History Items: {{ $orderHistory->count() }}
        </span>
    </div>

    <div class="space-y-4">
        @forelse ($orderHistory as $hist)
            <div class="glass-card p-6 rounded-3xl space-y-4 shadow-xl border-l-8 border-emerald-500">
                <div class="flex flex-wrap justify-between items-center border-b border-slate-800 pb-3 gap-3">
                    <div class="flex items-center gap-3">
                        <span class="bg-emerald-500 text-slate-950 font-black px-3 py-1 rounded-xl text-xs">ID: #{{ $hist->order_id }}</span>
                        <span class="font-mono font-bold text-emerald-400 text-sm">{{ $hist->order_code }}</span>
                        <span class="bg-slate-800 text-slate-300 text-xs px-3 py-1 rounded-full uppercase font-bold">{{ $hist->payment_method }}</span>
                    </div>
                    <div class="text-xs text-slate-400">
                        <i class="fa-regular fa-calendar mr-1.5 text-emerald-400"></i> Ordered on: <strong class="text-white">{{ $hist->created_at?->format('F j, Y, g:i A') }}</strong>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-white">{{ $hist->product_name }}</h3>
                        <p class="text-xs text-slate-300"><i class="fa-regular fa-user mr-1.5 text-blue-400"></i> Customer: <span class="font-bold text-white">{{ $hist->customer->full_name }}</span></p>

                        @if (\App\Support\Uploads::exists($hist->proof_image))
                            <div class="pt-2">
                                <button type="button"
                                        data-proof-src="{{ \App\Support\Uploads::url($hist->proof_image) }}"
                                        data-proof-label="ID: #{{ $hist->order_id }} - {{ $hist->order_code }}"
                                        class="inline-flex items-center gap-2 text-xs text-amber-400 hover:underline font-bold bg-amber-500/10 px-4 py-2 rounded-xl border border-amber-500/30 cursor-pointer shadow">
                                    <i class="fa-solid fa-image text-sm"></i> View Proof of Delivery Picture
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-6 ml-auto">
                        <div class="text-right">
                            <span class="text-[10px] uppercase text-slate-400 block font-bold">Total Paid</span>
                            <span class="text-xl font-black text-white">₱{{ number_format($hist->total, 2) }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.orders.destroy', $hist) }}" data-confirm="Delete this order from history?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-bold px-4 py-3 rounded-xl transition flex items-center gap-2">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-3xl p-16 text-center text-slate-400 text-sm shadow-sm">
                <i class="fa-solid fa-folder-open text-5xl mb-4 text-slate-500"></i>
                <p class="font-semibold text-slate-300">No completed orders in history.</p>
            </div>
        @endforelse
    </div>
</div>