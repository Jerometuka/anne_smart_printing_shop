<!-- MANAGE SERVICES / OFFERS TAB -->
<div id="tab-services" class="hidden space-y-6">
    <div class="flex justify-between items-center glass-card p-8 rounded-3xl shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-list-check text-amber-400 text-2xl"></i> Products & Service Offers Management
            </h2>
            <p class="text-base text-slate-400 mt-2">View and manage the services/products shown on the customer dashboard.</p>
        </div>
        <button type="button" data-action="service-add" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-base font-bold px-7 py-4 rounded-xl transition shadow flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Product / Offer
        </button>
    </div>

    <div id="serviceFormContainer" class="hidden glass-card p-8 rounded-3xl shadow-2xl border border-amber-500/40 space-y-6">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
            <h3 id="formTitle" class="text-base font-extrabold text-white uppercase tracking-wider">Add New Service Offer</h3>
            <button type="button" data-action="service-close" class="text-slate-400 hover:text-white text-sm bg-slate-800 px-3 py-1.5 rounded-xl">
                <i class="fa-solid fa-xmark mr-1"></i> Close Form
            </button>
        </div>

        {{-- Ang action ay pinapalitan ng JS: store (Add) o update (Edit) --}}
        <form method="POST" id="serviceForm" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="space-y-6"
              data-store-url="{{ route('admin.services.store') }}"
              data-update-url="{{ route('admin.services.update', ['service' => '__ID__']) }}">
            @csrf
            <input type="hidden" name="_method" id="fs_method" value="PUT" disabled>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Service / Product Name</label>
                        <input type="text" name="service_name" id="fs_service_name" required placeholder="e.g. Colored Printing / Tarpaulin" class="w-full bg-slate-950 border border-slate-700 text-sm rounded-xl p-3.5 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Description</label>
                        <textarea name="description" id="fs_description" rows="4" required placeholder="Describe the product..." class="w-full bg-slate-950 border border-slate-700 text-sm rounded-xl p-3.5 text-white focus:outline-none focus:border-amber-500"></textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Price (₱)</label>
                            <input type="number" step="0.01" min="0" name="price" id="fs_price" required placeholder="0.00" class="w-full bg-slate-950 border border-slate-700 text-sm rounded-xl p-3.5 text-white focus:outline-none focus:border-amber-500">
                        </div>
                        <div id="statusGroup" class="hidden">
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Status</label>
                            <select name="status" id="fs_status" class="w-full bg-slate-950 border border-slate-700 text-sm rounded-xl p-3.5 text-white focus:outline-none focus:border-amber-500">
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Upload Product Picture (Shown to Customers on the Dashboard):</label>
                        <input type="file" name="product_image" accept="image/*" class="w-full text-xs text-slate-300 file:bg-amber-500 file:text-slate-950 file:px-4 file:py-2.5 file:rounded-xl file:border-0 cursor-pointer font-semibold bg-slate-950 border border-slate-700 rounded-xl p-2">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" data-action="service-close" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-6 py-3.5 rounded-xl transition">Cancel</button>
                <button type="submit" id="fsSubmitBtn" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold px-8 py-3.5 rounded-xl transition shadow-lg">Save Offer</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($services as $srv)
            <div class="glass-card p-8 rounded-3xl flex justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-white text-2xl">{{ $srv->service_name }}</h4>
                            <span class="bg-emerald-500/10 text-emerald-400 text-sm px-3.5 py-1 rounded-full font-bold border border-emerald-500/20">{{ $srv->status }}</span>
                        </div>
                        <p class="text-base text-slate-400 mt-2 line-clamp-1">{{ $srv->description }}</p>
                        <span class="text-amber-400 font-black text-2xl mt-2 block">₱{{ number_format($srv->price, 2) }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" data-service="{{ json_encode($srv) }}" class="bg-blue-600 hover:bg-blue-500 text-white text-lg font-bold px-5 py-4 rounded-xl transition shadow" title="Edit Offer">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.services.destroy', $srv) }}" data-confirm="Delete this service/product?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-lg px-5 py-4 rounded-xl transition" title="Delete Offer">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>