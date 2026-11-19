<!-- SIDEBAR MENU -->
<aside id="sidebar" class="w-64 bg-slate-900/90 border-r border-slate-700/80 hidden md:flex flex-col justify-between p-6 sticky top-0 h-screen z-20 backdrop-blur-md">
    <div class="space-y-8">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center text-slate-950 font-bold shadow-lg shadow-amber-500/20 shrink-0">
                <i class="fa-solid fa-crown text-lg"></i>
            </div>
            <div class="sidebar-title">
                <h1 class="font-extrabold text-base text-white leading-tight">Anne SmartPrint</h1>
                <p class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">Admin Web</p>
            </div>
        </div>

        <nav class="space-y-1.5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2 menu-label">Menu</p>
            <button data-tab="analytics" id="btn-analytics" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800 transition" title="Analytics">
                <i class="fa-solid fa-chart-pie w-5 text-amber-400 shrink-0 text-center"></i> <span class="sidebar-text">Analytics</span>
            </button>
            <button data-tab="orders" id="btn-orders" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 transition shadow-md shadow-amber-500/20" title="Live Orders">
                <i class="fa-solid fa-box-archive w-5 shrink-0 text-center"></i> <span class="sidebar-text">Live Orders</span>
            </button>
            <button data-tab="history" id="btn-history" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800 transition" title="Order History">
                <i class="fa-solid fa-clock-rotate-left w-5 text-slate-400 shrink-0 text-center"></i> <span class="sidebar-text">Order History</span>
            </button>
            <button data-tab="inventory" id="btn-inventory" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800 transition" title="Stock / Inventory">
                <i class="fa-solid fa-warehouse w-5 text-slate-400 shrink-0 text-center"></i> <span class="sidebar-text">Stock / Inventory</span>
            </button>
            <button data-tab="services" id="btn-services" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800 transition" title="Products / Offers">
                <i class="fa-solid fa-list-check w-5 text-slate-400 shrink-0 text-center"></i> <span class="sidebar-text">Products / Offers</span>
            </button>
            <button data-tab="messages" id="btn-messages" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800 transition" title="Messages">
                <i class="fa-solid fa-comments w-5 text-slate-400 shrink-0 text-center"></i> <span class="sidebar-text">Messages</span>
            </button>
        </nav>
    </div>

    <div>
        {{-- Palitan ang URL kung iba ang logout route mo --}}
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-red-400 hover:bg-red-500/20 transition border border-red-500/20" title="Logout">
                <i class="fa-solid fa-right-from-bracket w-5 shrink-0 text-center"></i> <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</aside>