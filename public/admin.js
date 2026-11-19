(function () {
    const K_LAST = 'sp_a_last_order', K_LIST = 'sp_a_list';
    const ls = {
        get(k, d) { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
        set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
    };
    const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    let list = ls.get(K_LIST, []);

    function timeAgo(t) {
        const s = Math.floor((Date.now() - t) / 1000);
        if (s < 60) return 'Just now';
        if (s < 3600) return Math.floor(s / 60) + ' min ago';
        if (s < 86400) return Math.floor(s / 3600) + ' hr ago';
        return new Date(t).toLocaleDateString();
    }

    function beep() {
        try {
            const AC = window.AudioContext || window.webkitAudioContext;
            const ctx = new AC();
            [880, 1175].forEach((f, i) => {
                const o = ctx.createOscillator(), g = ctx.createGain();
                o.type = 'sine'; o.frequency.value = f;
                g.gain.setValueAtTime(0.0001, ctx.currentTime + i * 0.18);
                g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + i * 0.18 + 0.02);
                g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.18 + 0.16);
                o.connect(g); g.connect(ctx.destination);
                o.start(ctx.currentTime + i * 0.18); o.stop(ctx.currentTime + i * 0.18 + 0.17);
            });
        } catch (e) { /* browser blocked audio until user interacts */ }
    }

    function render() {
        const box = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        const unread = list.filter(n => !n.read).length;
        badge.textContent = unread > 9 ? '9+' : unread;
        badge.classList.toggle('hidden', unread === 0);
        if (!list.length) {
            box.innerHTML = '<div class="p-6 text-center text-xs text-slate-500"><i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>No new orders yet.</div>';
            return;
        }
        box.innerHTML = list.map(n => n.type === 'msg' ?
            '<a href="' + ADMIN.adminUrl + '?tab=messages&chat_user=' + encodeURIComponent(n.uid) + '" class="flex gap-3 px-4 py-3 border-b border-slate-800/70 hover:bg-slate-800/60 transition ' + (n.read ? '' : 'bg-blue-500/10') + '">' +
            '<i class="fa-solid fa-comment-dots text-blue-400 mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-bold text-white">New message</p>' +
            '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + (n.count > 1 ? ' sent ' + n.count + ' messages' : ' sent you a message') + '</p>' +
            '<p class="text-[10px] text-slate-500 mt-0.5">' + timeAgo(n.t) + '</p></div></a>' :
            '<a href="' + ADMIN.adminUrl + '?tab=orders" class="flex gap-3 px-4 py-3 border-b border-slate-800/70 hover:bg-slate-800/60 transition ' + (n.read ? '' : 'bg-amber-500/10') + '">' +
            '<i class="fa-solid fa-cart-shopping text-amber-400 mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-bold text-white">New order <span class="font-mono text-amber-400">' + esc(n.code) + '</span></p>' +
            '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + ' &bull; ' + esc(n.product) + '</p>' +
            '<p class="text-[10px] text-slate-500 mt-0.5">&#8369;' + esc(n.total) + ' &bull; ' + timeAgo(n.t) + '</p></div></a>'
        ).join('');
    }

    function toast(n) {
        const el = document.createElement('a');
        if (n.type === 'msg') {
            el.href = ADMIN.adminUrl + '?tab=messages&chat_user=' + encodeURIComponent(n.uid);
            el.className = 'glass-card flex gap-3 items-start p-4 rounded-2xl shadow-2xl border border-blue-500/50 transition-all duration-300 translate-x-4 opacity-0';
            el.innerHTML = '<i class="fa-solid fa-comment-dots text-blue-400 text-lg mt-0.5"></i>' +
                '<div class="min-w-0"><p class="text-xs font-extrabold text-white">New message!</p>' +
                '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + (n.count > 1 ? ' sent ' + n.count + ' messages' : ' sent you a message') + '</p>' +
                '<p class="text-[10px] text-blue-300 font-bold mt-1">Click to open the chat</p></div>';
            document.getElementById('toastBox').appendChild(el);
            requestAnimationFrame(() => el.classList.remove('translate-x-4', 'opacity-0'));
            setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 300); }, 8000);
            return;
        }
        el.href = ADMIN.adminUrl + '?tab=orders';
        el.className = 'glass-card flex gap-3 items-start p-4 rounded-2xl shadow-2xl border border-amber-500/50 transition-all duration-300 translate-x-4 opacity-0';
        el.innerHTML = '<i class="fa-solid fa-cart-shopping text-amber-400 text-lg mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-extrabold text-white">New order! <span class="font-mono text-amber-400">' + esc(n.code) + '</span></p>' +
            '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + ' &bull; ' + esc(n.product) + '</p>' +
            '<p class="text-[10px] text-amber-300 font-bold mt-1">Click to view Live Orders</p></div>';
        document.getElementById('toastBox').appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-x-4', 'opacity-0'));
        setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 300); }, 8000);
    }

    async function poll() {
        try {
            const r = await fetch(ADMIN.notificationsUrl + '?for=admin', { cache: 'no-store', credentials: 'same-origin' });
            if (r.status === 401 || r.status === 403) { console.warn('Notifications stopped: not logged in as admin (HTTP ' + r.status + ')'); clearInterval(pollTimer); return; }
            if (!r.ok) { console.warn('notifications.php returned HTTP ' + r.status + ' - make sure the file is in the same folder as admin.php'); return; }
            const data = await r.json();

            if (data.msgs_error) console.warn('Message notification query failed:', data.msgs_error);
            document.getElementById('notifPending').textContent = data.pending > 0 ? (data.pending + ' pending') : '';

            const stored = localStorage.getItem(K_LAST);
            const last = stored === null ? null : parseInt(stored, 10);
            if (last !== null) {
                const fresh = (data.orders || []).filter(o => o.order_id > last).sort((a, b) => a.order_id - b.order_id);
                fresh.forEach(o => {
                    const n = {
                        t: Date.now(), read: false,
                        code: o.order_code || ('#' + o.order_id),
                        customer: o.full_name, product: o.product_name,
                        total: Number(o.total).toFixed(2)
                    };
                    list.unshift(n);
                    toast(n);
                });
                if (fresh.length) {
                    list = list.slice(0, 30);
                    ls.set(K_LIST, list);
                    beep();
                    render();
                }
            }
            try { localStorage.setItem(K_LAST, String(data.last_id)); } catch (e) {}

            // ===== Bagong message galing sa customer =====
            const K_MSG = 'sp_a_msgs';
            const msgFirst = localStorage.getItem(K_MSG) === null;
            const msgMap = ls.get(K_MSG, {});
            let gotMsg = false;
            (data.msgs || []).forEach(m => {
                const prev = msgMap[m.user_id];
                if (!msgFirst && m.c > (prev === undefined ? 0 : prev)) {
                    const n = { type: 'msg', t: Date.now(), read: false, uid: m.user_id, customer: m.full_name, count: m.c - (prev || 0) };
                    list.unshift(n);
                    toast(n);
                    gotMsg = true;
                }
                msgMap[m.user_id] = m.c;
            });
            ls.set(K_MSG, msgMap);
            if (gotMsg) {
                list = list.slice(0, 30);
                ls.set(K_LIST, list);
                beep();
                render();
            }
        } catch (e) { /* ignore network errors */ }
    }

    window.toggleNotifPanel = function () {
        const p = document.getElementById('notifPanel');
        p.classList.toggle('hidden');
        if (!p.classList.contains('hidden')) {
            list.forEach(n => n.read = true);
            ls.set(K_LIST, list);
            document.getElementById('notifBadge').classList.add('hidden');
            setTimeout(render, 1500);
        }
    };
    window.clearNotifs = function () { list = []; ls.set(K_LIST, list); render(); };

    document.addEventListener('click', e => {
        const p = document.getElementById('notifPanel');
        if (!p.classList.contains('hidden') && !e.target.closest('#notifPanel') && !e.target.closest('#notifBell')) p.classList.add('hidden');
    });

    render();
    poll();
    const pollTimer = setInterval(poll, 5000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
})();

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'orders';
    switchTab(tab);

    const monthlyData = ADMIN.monthlySales;
    const ctx = document.getElementById('monthlySalesBarChart').getContext('2d');
    Chart.defaults.font.size = 15;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Buwanang Kita (₱)',
                data: monthlyData,
                backgroundColor: '#f59e0b',
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Kita: ₱' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#cbd5e1', font: { size: 15 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.08)' },
                    ticks: { 
                        color: '#cbd5e1', 
                        font: { size: 15 },
                        callback: function(value) {
                            return '₱' + value;
                        }
                    }
                }
            }
        }
    });
});

// Function para i-toggle o paliitin/palakihin ang sidebar para maging full-screen ang main content
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}

function switchTab(tabName) {
    ['analytics', 'orders', 'history', 'inventory', 'services', 'messages'].forEach(t => {
        const el = document.getElementById('tab-' + t);
        if (el) el.classList.add('hidden');
        const btn = document.getElementById('btn-' + t);
        if (btn) {
            btn.classList.remove('bg-amber-500', 'text-slate-950', 'shadow-md', 'shadow-amber-500/20');
            btn.classList.add('text-slate-300', 'hover:bg-slate-800');
        }
    });
    const target = document.getElementById('tab-' + tabName);
    if (target) target.classList.remove('hidden');
    if (tabName === 'messages') {
        const cs = document.getElementById('adminChatScroll');
        if (cs) cs.scrollTop = cs.scrollHeight;
    }
    const targetBtn = document.getElementById('btn-' + tabName);
    if (targetBtn) {
        targetBtn.classList.remove('text-slate-300', 'hover:bg-slate-800');
        targetBtn.classList.add('bg-amber-500', 'text-slate-950', 'shadow-md', 'shadow-amber-500/20');
    }
}

function toggleAddServiceView(show) {
    const formContainer = document.getElementById('serviceFormContainer');
    if (show) {
        formContainer.classList.remove('hidden');
        document.getElementById('formTitle').innerText = "Add New Service / Product Offer";
        document.getElementById('fs_service_id').value = "";
        document.getElementById('fs_service_name').value = "";
        document.getElementById('fs_description').value = "";
        document.getElementById('fs_price').value = "";
        document.getElementById('statusGroup').classList.add('hidden');
        document.getElementById('fsSubmitBtn').name = "add_service";
        document.getElementById('fsSubmitBtn').form.action = ADMIN.servicesAddUrl;
        document.getElementById('fsSubmitBtn').innerText = "Save Offer";
        formContainer.scrollIntoView({ behavior: 'smooth' });
    } else {
        formContainer.classList.add('hidden');
    }
}

function openEditServiceFullView(srv) {
    const formContainer = document.getElementById('serviceFormContainer');
    formContainer.classList.remove('hidden');
    document.getElementById('formTitle').innerText = "Edit Service / Product Offer (#" + srv.service_id + ")";
    document.getElementById('fs_service_id').value = srv.service_id;
    document.getElementById('fs_service_name').value = srv.service_name;
    document.getElementById('fs_description').value = srv.description;
    document.getElementById('fs_price').value = srv.price;
    document.getElementById('fs_status').value = srv.status;
    document.getElementById('statusGroup').classList.remove('hidden');
    document.getElementById('fsSubmitBtn').name = "update_service";
    document.getElementById('fsSubmitBtn').form.action = ADMIN.servicesUpdateUrl;
    document.getElementById('fsSubmitBtn').innerText = "Update Offer";
    formContainer.scrollIntoView({ behavior: 'smooth' });
}

// I-print ang inupload na file ng customer (nagpi-print agad, walang pop-up)
function printOrderFile(url, type) {
    const abs = new URL(url, location.href).href;
    if (type === 'other') { window.open(abs, '_blank'); return; }

    const old = document.getElementById('printFrame');
    if (old) old.remove();
    const fr = document.createElement('iframe');
    fr.id = 'printFrame';
    fr.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden';
    document.body.appendChild(fr);

    const doPrint = () => {
        try { fr.contentWindow.focus(); fr.contentWindow.print(); }
        catch (e) { window.open(abs, '_blank'); }
    };

    if (type === 'img') {
        fr.onload = () => setTimeout(doPrint, 300);
        fr.srcdoc = '<html><head><style>@page{margin:0}html,body{margin:0;height:100%}body{display:flex;align-items:center;justify-content:center}img{max-width:100%;max-height:100vh;object-fit:contain}</style></head><body><img src="' + abs + '"></body></html>';
    } else {
        fr.onload = () => setTimeout(doPrint, 700);
        fr.src = abs;
    }
}

function toggleProofInput(orderId) {
    const selectEl = document.getElementById('status_select_' + orderId);
    const proofBox = document.getElementById('proof_box_' + orderId);
    if (selectEl && selectEl.value === 'Successful Pickup') {
        if (proofBox) proofBox.classList.remove('hidden');
    } else {
        if (proofBox) proofBox.classList.add('hidden');
    }
}

function openProofModal(imgSrc, orderCode, orderDate, customerName, productName, orderTotal) {
    document.getElementById('modalProofImg').src = imgSrc;
    document.getElementById('modalOrderCode').innerText = orderCode;
    document.getElementById('proofModal').classList.remove('hidden');
}

// Mga value mula sa Laravel (galing sa data-attributes ng <body> sa admin.blade.php)
const ADMIN = {
    adminUrl: document.body.dataset.adminUrl,
    notificationsUrl: document.body.dataset.notificationsUrl,
    servicesAddUrl: document.body.dataset.servicesAddUrl,
    servicesUpdateUrl: document.body.dataset.servicesUpdateUrl,
    monthlySales: JSON.parse(document.body.dataset.monthlySales || '[]')
};

(function () {
    const K_LAST = 'sp_a_last_order', K_LIST = 'sp_a_list';
    const ls = {
        get(k, d) { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
        set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
    };
    const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    let list = ls.get(K_LIST, []);

    function timeAgo(t) {
        const s = Math.floor((Date.now() - t) / 1000);
        if (s < 60) return 'Just now';
        if (s < 3600) return Math.floor(s / 60) + ' min ago';
        if (s < 86400) return Math.floor(s / 3600) + ' hr ago';
        return new Date(t).toLocaleDateString();
    }

    function beep() {
        try {
            const AC = window.AudioContext || window.webkitAudioContext;
            const ctx = new AC();
            [880, 1175].forEach((f, i) => {
                const o = ctx.createOscillator(), g = ctx.createGain();
                o.type = 'sine'; o.frequency.value = f;
                g.gain.setValueAtTime(0.0001, ctx.currentTime + i * 0.18);
                g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + i * 0.18 + 0.02);
                g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.18 + 0.16);
                o.connect(g); g.connect(ctx.destination);
                o.start(ctx.currentTime + i * 0.18); o.stop(ctx.currentTime + i * 0.18 + 0.17);
            });
        } catch (e) { /* browser blocked audio until user interacts */ }
    }

    function render() {
        const box = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        const unread = list.filter(n => !n.read).length;
        badge.textContent = unread > 9 ? '9+' : unread;
        badge.classList.toggle('hidden', unread === 0);
        if (!list.length) {
            box.innerHTML = '<div class="p-6 text-center text-xs text-slate-500"><i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>No new orders yet.</div>';
            return;
        }
        box.innerHTML = list.map(n => n.type === 'msg' ?
            '<a href="' + ADMIN.adminUrl + '?tab=messages&chat_user=' + encodeURIComponent(n.uid) + '" class="flex gap-3 px-4 py-3 border-b border-slate-800/70 hover:bg-slate-800/60 transition ' + (n.read ? '' : 'bg-blue-500/10') + '">' +
            '<i class="fa-solid fa-comment-dots text-blue-400 mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-bold text-white">New message</p>' +
            '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + (n.count > 1 ? ' sent ' + n.count + ' messages' : ' sent you a message') + '</p>' +
            '<p class="text-[10px] text-slate-500 mt-0.5">' + timeAgo(n.t) + '</p></div></a>' :
            '<a href="' + ADMIN.adminUrl + '?tab=orders" class="flex gap-3 px-4 py-3 border-b border-slate-800/70 hover:bg-slate-800/60 transition ' + (n.read ? '' : 'bg-amber-500/10') + '">' +
            '<i class="fa-solid fa-cart-shopping text-amber-400 mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-bold text-white">New order <span class="font-mono text-amber-400">' + esc(n.code) + '</span></p>' +
            '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + ' &bull; ' + esc(n.product) + '</p>' +
            '<p class="text-[10px] text-slate-500 mt-0.5">&#8369;' + esc(n.total) + ' &bull; ' + timeAgo(n.t) + '</p></div></a>'
        ).join('');
    }

    function toast(n) {
        const el = document.createElement('a');
        if (n.type === 'msg') {
            el.href = ADMIN.adminUrl + '?tab=messages&chat_user=' + encodeURIComponent(n.uid);
            el.className = 'glass-card flex gap-3 items-start p-4 rounded-2xl shadow-2xl border border-blue-500/50 transition-all duration-300 translate-x-4 opacity-0';
            el.innerHTML = '<i class="fa-solid fa-comment-dots text-blue-400 text-lg mt-0.5"></i>' +
                '<div class="min-w-0"><p class="text-xs font-extrabold text-white">New message!</p>' +
                '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + (n.count > 1 ? ' sent ' + n.count + ' messages' : ' sent you a message') + '</p>' +
                '<p class="text-[10px] text-blue-300 font-bold mt-1">Click to open the chat</p></div>';
            document.getElementById('toastBox').appendChild(el);
            requestAnimationFrame(() => el.classList.remove('translate-x-4', 'opacity-0'));
            setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 300); }, 8000);
            return;
        }
        el.href = ADMIN.adminUrl + '?tab=orders';
        el.className = 'glass-card flex gap-3 items-start p-4 rounded-2xl shadow-2xl border border-amber-500/50 transition-all duration-300 translate-x-4 opacity-0';
        el.innerHTML = '<i class="fa-solid fa-cart-shopping text-amber-400 text-lg mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-extrabold text-white">New order! <span class="font-mono text-amber-400">' + esc(n.code) + '</span></p>' +
            '<p class="text-[11px] text-slate-300 truncate">' + esc(n.customer) + ' &bull; ' + esc(n.product) + '</p>' +
            '<p class="text-[10px] text-amber-300 font-bold mt-1">Click to view Live Orders</p></div>';
        document.getElementById('toastBox').appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-x-4', 'opacity-0'));
        setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 300); }, 8000);
    }

    async function poll() {
        try {
            const r = await fetch(ADMIN.notificationsUrl + '?for=admin', { cache: 'no-store', credentials: 'same-origin' });
            if (r.status === 401 || r.status === 403) { console.warn('Notifications stopped: not logged in as admin (HTTP ' + r.status + ')'); clearInterval(pollTimer); return; }
            if (!r.ok) { console.warn('notifications.php returned HTTP ' + r.status + ' - make sure the file is in the same folder as admin.php'); return; }
            const data = await r.json();

            if (data.msgs_error) console.warn('Message notification query failed:', data.msgs_error);
            document.getElementById('notifPending').textContent = data.pending > 0 ? (data.pending + ' pending') : '';

            const stored = localStorage.getItem(K_LAST);
            const last = stored === null ? null : parseInt(stored, 10);
            if (last !== null) {
                const fresh = (data.orders || []).filter(o => o.order_id > last).sort((a, b) => a.order_id - b.order_id);
                fresh.forEach(o => {
                    const n = {
                        t: Date.now(), read: false,
                        code: o.order_code || ('#' + o.order_id),
                        customer: o.full_name, product: o.product_name,
                        total: Number(o.total).toFixed(2)
                    };
                    list.unshift(n);
                    toast(n);
                });
                if (fresh.length) {
                    list = list.slice(0, 30);
                    ls.set(K_LIST, list);
                    beep();
                    render();
                }
            }
            try { localStorage.setItem(K_LAST, String(data.last_id)); } catch (e) {}

            // ===== Bagong message galing sa customer =====
            const K_MSG = 'sp_a_msgs';
            const msgFirst = localStorage.getItem(K_MSG) === null;
            const msgMap = ls.get(K_MSG, {});
            let gotMsg = false;
            (data.msgs || []).forEach(m => {
                const prev = msgMap[m.user_id];
                if (!msgFirst && m.c > (prev === undefined ? 0 : prev)) {
                    const n = { type: 'msg', t: Date.now(), read: false, uid: m.user_id, customer: m.full_name, count: m.c - (prev || 0) };
                    list.unshift(n);
                    toast(n);
                    gotMsg = true;
                }
                msgMap[m.user_id] = m.c;
            });
            ls.set(K_MSG, msgMap);
            if (gotMsg) {
                list = list.slice(0, 30);
                ls.set(K_LIST, list);
                beep();
                render();
            }
        } catch (e) { /* ignore network errors */ }
    }

    window.toggleNotifPanel = function () {
        const p = document.getElementById('notifPanel');
        p.classList.toggle('hidden');
        if (!p.classList.contains('hidden')) {
            list.forEach(n => n.read = true);
            ls.set(K_LIST, list);
            document.getElementById('notifBadge').classList.add('hidden');
            setTimeout(render, 1500);
        }
    };
    window.clearNotifs = function () { list = []; ls.set(K_LIST, list); render(); };

    document.addEventListener('click', e => {
        const p = document.getElementById('notifPanel');
        if (!p.classList.contains('hidden') && !e.target.closest('#notifPanel') && !e.target.closest('#notifBell')) p.classList.add('hidden');
    });

    render();
    poll();
    const pollTimer = setInterval(poll, 5000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
})();

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'orders';
    switchTab(tab);

    const monthlyData = ADMIN.monthlySales;
    const ctx = document.getElementById('monthlySalesBarChart').getContext('2d');
    Chart.defaults.font.size = 15;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Buwanang Kita (₱)',
                data: monthlyData,
                backgroundColor: '#f59e0b',
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Kita: ₱' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#cbd5e1', font: { size: 15 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.08)' },
                    ticks: { 
                        color: '#cbd5e1', 
                        font: { size: 15 },
                        callback: function(value) {
                            return '₱' + value;
                        }
                    }
                }
            }
        }
    });
});

// Function para i-toggle o paliitin/palakihin ang sidebar para maging full-screen ang main content
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}

function switchTab(tabName) {
    ['analytics', 'orders', 'history', 'inventory', 'services', 'messages'].forEach(t => {
        const el = document.getElementById('tab-' + t);
        if (el) el.classList.add('hidden');
        const btn = document.getElementById('btn-' + t);
        if (btn) {
            btn.classList.remove('bg-amber-500', 'text-slate-950', 'shadow-md', 'shadow-amber-500/20');
            btn.classList.add('text-slate-300', 'hover:bg-slate-800');
        }
    });
    const target = document.getElementById('tab-' + tabName);
    if (target) target.classList.remove('hidden');
    if (tabName === 'messages') {
        const cs = document.getElementById('adminChatScroll');
        if (cs) cs.scrollTop = cs.scrollHeight;
    }
    const targetBtn = document.getElementById('btn-' + tabName);
    if (targetBtn) {
        targetBtn.classList.remove('text-slate-300', 'hover:bg-slate-800');
        targetBtn.classList.add('bg-amber-500', 'text-slate-950', 'shadow-md', 'shadow-amber-500/20');
    }
}

function toggleAddServiceView(show) {
    const formContainer = document.getElementById('serviceFormContainer');
    if (show) {
        formContainer.classList.remove('hidden');
        document.getElementById('formTitle').innerText = "Add New Service / Product Offer";
        document.getElementById('fs_service_id').value = "";
        document.getElementById('fs_service_name').value = "";
        document.getElementById('fs_description').value = "";
        document.getElementById('fs_price').value = "";
        document.getElementById('statusGroup').classList.add('hidden');
        document.getElementById('fsSubmitBtn').name = "add_service";
        document.getElementById('fsSubmitBtn').form.action = ADMIN.servicesAddUrl;
        document.getElementById('fsSubmitBtn').innerText = "Save Offer";
        formContainer.scrollIntoView({ behavior: 'smooth' });
    } else {
        formContainer.classList.add('hidden');
    }
}

function openEditServiceFullView(srv) {
    const formContainer = document.getElementById('serviceFormContainer');
    formContainer.classList.remove('hidden');
    document.getElementById('formTitle').innerText = "Edit Service / Product Offer (#" + srv.service_id + ")";
    document.getElementById('fs_service_id').value = srv.service_id;
    document.getElementById('fs_service_name').value = srv.service_name;
    document.getElementById('fs_description').value = srv.description;
    document.getElementById('fs_price').value = srv.price;
    document.getElementById('fs_status').value = srv.status;
    document.getElementById('statusGroup').classList.remove('hidden');
    document.getElementById('fsSubmitBtn').name = "update_service";
    document.getElementById('fsSubmitBtn').form.action = ADMIN.servicesUpdateUrl;
    document.getElementById('fsSubmitBtn').innerText = "Update Offer";
    formContainer.scrollIntoView({ behavior: 'smooth' });
}

// I-print ang inupload na file ng customer (nagpi-print agad, walang pop-up)
function printOrderFile(url, type) {
    const abs = new URL(url, location.href).href;
    if (type === 'other') { window.open(abs, '_blank'); return; }

    const old = document.getElementById('printFrame');
    if (old) old.remove();
    const fr = document.createElement('iframe');
    fr.id = 'printFrame';
    fr.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden';
    document.body.appendChild(fr);

    const doPrint = () => {
        try { fr.contentWindow.focus(); fr.contentWindow.print(); }
        catch (e) { window.open(abs, '_blank'); }
    };

    if (type === 'img') {
        fr.onload = () => setTimeout(doPrint, 300);
        fr.srcdoc = '<html><head><style>@page{margin:0}html,body{margin:0;height:100%}body{display:flex;align-items:center;justify-content:center}img{max-width:100%;max-height:100vh;object-fit:contain}</style></head><body><img src="' + abs + '"></body></html>';
    } else {
        fr.onload = () => setTimeout(doPrint, 700);
        fr.src = abs;
    }
}

function toggleProofInput(orderId) {
    const selectEl = document.getElementById('status_select_' + orderId);
    const proofBox = document.getElementById('proof_box_' + orderId);
    if (selectEl && selectEl.value === 'Successful Pickup') {
        if (proofBox) proofBox.classList.remove('hidden');
    } else {
        if (proofBox) proofBox.classList.add('hidden');
    }
}

function openProofModal(imgSrc, orderCode, orderDate, customerName, productName, orderTotal) {
    document.getElementById('modalProofImg').src = imgSrc;
    document.getElementById('modalOrderCode').innerText = orderCode;
    document.getElementById('proofModal').classList.remove('hidden');
}