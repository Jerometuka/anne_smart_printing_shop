<!-- INVENTORY TAB -->
<div id="tab-inventory" class="hidden space-y-6">
    <div class="flex justify-between items-center glass-card p-8 rounded-3xl shadow-sm">
        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <i class="fa-solid fa-warehouse text-amber-400"></i> Paper & Supplies Inventory Stocks
        </h2>
        <button type="button" data-open-modal="addStockModal" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-base font-bold px-7 py-4 rounded-xl transition shadow">
            + Add New Item
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($inventoryItems as $inv)
            <div class="glass-card p-8 rounded-3xl flex justify-between items-center border border-slate-800 shadow-md">
                <div class="space-y-1">
                    <span class="text-sm uppercase font-bold text-amber-400 bg-amber-500/10 px-4 py-1 rounded-full border border-amber-500/20">{{ $inv->category }}</span>
                    <h4 class="font-bold text-white text-2xl mt-2">{{ $inv->item_name }}</h4>
                    <p class="text-base text-slate-400 mt-1">Available Stock: <strong class="text-emerald-400 text-xl">{{ $inv->stock_quantity }}</strong> {{ $inv->unit }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.inventory.update', $inv) }}" class="flex items-center gap-1.5">
                        @csrf
                        @method('PUT')
                        <input type="number" min="0" name="stock_quantity" value="{{ $inv->stock_quantity }}" class="w-24 bg-slate-950 border border-slate-700 text-white text-lg rounded-xl p-3 text-center focus:outline-none focus:border-amber-500">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-lg px-5 py-3.5 rounded-xl transition shadow" title="Update Stock">
                            <i class="fa-solid fa-floppy-disk"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.inventory.destroy', $inv) }}" data-confirm="Delete this inventory item?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-lg px-5 py-3.5 rounded-xl transition" title="Delete Item">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>