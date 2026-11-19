<!-- PROOF / ATTACHMENT VIEWER MODAL -->
<div id="proofModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
    <div class="glass-card w-full max-w-2xl p-8 rounded-3xl shadow-2xl border border-slate-700 space-y-6">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-amber-400"></i> Attachment / Details Viewer
                </h3>
                <p class="text-xs text-amber-400 font-mono mt-1" id="modalOrderCode"></p>
            </div>
            <button type="button" data-close-modal="proofModal" class="text-slate-400 hover:text-white text-xl p-2"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="space-y-2">
            <div class="bg-slate-950 p-3 rounded-2xl border border-slate-800 flex items-center justify-center overflow-hidden">
                <img id="modalProofImg" src="" alt="Attachment View" class="max-h-[400px] w-auto object-contain rounded-xl shadow-lg">
            </div>
        </div>

        <button type="button" data-close-modal="proofModal" class="w-full bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-3.5 rounded-xl transition shadow">Close Viewer</button>
    </div>
</div>

<!-- ADD STOCK MODAL -->
<div id="addStockModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
    <div class="glass-card w-full max-w-md p-6 rounded-3xl shadow-2xl border border-slate-700 space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Add New Inventory Item</h3>
            <button type="button" data-close-modal="addStockModal" class="text-slate-400 hover:text-white text-sm"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Item Name</label>
                <input type="text" name="item_name" required placeholder="e.g. A4 Paper Ream" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl p-3 text-white focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Category</label>
                <input type="text" name="category" required placeholder="e.g. Paper" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl p-3 text-white focus:outline-none focus:border-amber-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Quantity</label>
                    <input type="number" min="0" name="stock_quantity" required placeholder="100" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl p-3 text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Unit</label>
                    <input type="text" name="unit" required placeholder="reams / pcs" class="w-full bg-slate-950 border border-slate-700 text-xs rounded-xl p-3 text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold py-3 rounded-xl transition shadow mt-2">Save Item</button>
        </form>
    </div>
</div>