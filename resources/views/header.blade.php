<header class="adm-header sticky top-0 z-20 px-6 py-3 flex items-center justify-between gap-4">
    <label class="adm-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input id="admSearch" type="text" placeholder="Type Search Here..." autocomplete="off">
    </label>

    <div class="flex items-center gap-5">
        <a href="#orders" class="adm-bell" title="Pending orders">
            <i class="fa-regular fa-bell"></i>
            @if(($pendingCount ?? 0) > 0)
                <span class="adm-bell-dot">{{ $pendingCount }}</span>
            @endif
        </a>

        <label class="adm-open">
            <span>Open For Order</span>
            <input type="checkbox" id="openForOrder" checked>
        </label>

        <div class="adm-user">
            <div class="adm-avatar">AD</div>
            <div class="leading-tight hidden sm:block">
                <div class="text-xs font-bold text-white">System Admin</div>
                <div class="text-[10px] text-amber-400">Super Admin</div>
            </div>
        </div>
    </div>
</header>