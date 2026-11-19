<header class="bg-slate-900/90 border-b border-slate-700/80 h-28 px-10 flex justify-between items-center sticky top-0 z-10 backdrop-blur-md">
    <!-- Sidebar Toggle Button para maging Full-Screen -->
    <div class="flex items-center gap-4">
        <button type="button" data-action="toggle-sidebar" class="text-slate-300 hover:text-amber-400 bg-slate-800/80 p-4 rounded-xl border border-slate-700 transition" title="Toggle Fullscreen Sidebar">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
        <div class="relative w-[28rem] hidden sm:block">
            <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-slate-400 text-lg">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" placeholder="Type Search Here..." class="w-full bg-slate-950 border border-slate-700 text-base rounded-xl pl-14 pr-5 py-4 text-white focus:outline-none focus:border-amber-500 transition">
        </div>
    </div>

    <div class="flex items-center gap-6 ml-auto">
        <!-- Notification Bell (New Orders) -->
        <div class="relative">
            <button type="button" id="notifBell" data-action="toggle-notif" class="relative w-14 h-14 text-xl rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-amber-400 flex items-center justify-center transition" title="New order notifications">
                <i class="fa-solid fa-bell"></i>
                <span id="notifBadge" class="hidden absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-extrabold flex items-center justify-center border-2 border-slate-900">0</span>
            </button>
            <div id="notifPanel" class="hidden absolute right-0 mt-3 w-80 glass-card rounded-2xl shadow-2xl border border-slate-700 overflow-hidden z-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-white">New Orders</span>
                    <span id="notifPending" class="text-[10px] font-bold text-amber-400"></span>
                </div>
                <div id="notifList" class="max-h-80 overflow-y-auto"></div>
                <button type="button" data-action="clear-notifs" class="w-full text-[10px] font-bold text-slate-400 hover:text-white py-2.5 border-t border-slate-800">Clear all</button>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-base font-semibold text-slate-300">Open For Order</span>
            <input type="checkbox" checked class="toggle accent-amber-500 w-7 h-7 cursor-pointer">
        </div>
        <div class="flex items-center gap-3 border-l border-slate-700 pl-6">
            <div class="w-14 h-14 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center font-bold text-amber-400 text-lg">
                AD
            </div>
            <div class="hidden sm:block">
                <h4 class="text-base font-bold text-white">System Admin</h4>
                <p class="text-sm text-amber-400">Super Administrator</p>
            </div>
        </div>
    </div>
</header>