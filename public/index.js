if (document.getElementById('thankYouOverlay')) {
    setTimeout(function () { window.location.href = '/my-orders'; }, 3500);
}

(function () {
    const UID = parseInt(document.body.dataset.uid, 10) || 0;
    const K_MAP = 'sp_c_status_' + UID, K_MAX = 'sp_c_max_' + UID, K_LIST = 'sp_c_list_' + UID;
    const META = {
        'Pending':           { icon: 'fa-hourglass-half', color: 'text-sky-400',     msg: 'Order placed! Waiting in line.' },
        'Processing':        { icon: 'fa-print',          color: 'text-amber-400',   msg: 'Your order is now being printed.' },
        'Ready for Pickup':  { icon: 'fa-bag-shopping',   color: 'text-violet-400',  msg: 'Your order is ready for pickup!' },
        'Successful Pickup': { icon: 'fa-circle-check',   color: 'text-emerald-400', msg: 'Order completed. Thank you for buying!' },
        'New Message':       { icon: 'fa-comment-dots',   color: 'text-blue-400',    msg: 'The shop replied to your chat. Tap to read.' }
    };
    const hrefFor = n => n.status === 'New Message' ? '/messages' : '/my-orders';
    const ls = {
        get(k, d) { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
        set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
    };
    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    let list = ls.get(K_LIST, []);

    function timeAgo(t) {
        const s = Math.floor((Date.now() - t) / 1000);
        if (s < 60) return 'Just now';
        if (s < 3600) return Math.floor(s / 60) + ' min ago';
        if (s < 86400) return Math.floor(s / 3600) + ' hr ago';
        return new Date(t).toLocaleDateString();
    }

    function render() {
        const box = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        const unread = list.filter(n => !n.read).length;
        badge.textContent = unread > 9 ? '9+' : unread;
        badge.classList.toggle('hidden', unread === 0);
        if (!list.length) {
            box.innerHTML = '<div class="p-6 text-center text-xs text-slate-500"><i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>No notifications yet.</div>';
            return;
        }
        box.innerHTML = list.map(n => {
            const m = META[n.status] || META['Pending'];
            return '<a href="' + hrefFor(n) + '" class="flex gap-3 px-4 py-3 border-b border-slate-800/70 hover:bg-slate-800/60 transition ' + (n.read ? '' : 'bg-blue-500/10') + '">' +
                '<i class="fa-solid ' + m.icon + ' ' + m.color + ' mt-0.5"></i>' +
                '<div class="min-w-0"><p class="text-xs font-bold text-white">' + esc(n.status) + ' <span class="font-mono text-slate-400">' + esc(n.code) + '</span></p>' +
                '<p class="text-[11px] text-slate-300">' + esc(n.msg) + '</p>' +
                '<p class="text-[10px] text-slate-500 mt-0.5">' + timeAgo(n.t) + '</p></div></a>';
        }).join('');
    }

    function toast(n) {
        const m = META[n.status] || META['Pending'];
        const el = document.createElement('a');
        el.href = hrefFor(n);
        el.className = 'glass-card flex gap-3 items-start p-4 rounded-2xl shadow-2xl border border-blue-500/40 transition-all duration-300 translate-x-4 opacity-0';
        el.innerHTML = '<i class="fa-solid ' + m.icon + ' ' + m.color + ' text-lg mt-0.5"></i>' +
            '<div class="min-w-0"><p class="text-xs font-extrabold text-white">' + esc(n.status) + ' <span class="font-mono text-slate-400">' + esc(n.code) + '</span></p>' +
            '<p class="text-[11px] text-slate-300">' + esc(n.msg) + '</p></div>';
        document.getElementById('toastBox').appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-x-4', 'opacity-0'));
        setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 300); }, 6000);
    }

    function push(order) {
        const m = META[order.status] || META['Pending'];
        const n = { t: Date.now(), status: order.status, code: order.status === 'New Message' ? '' : (order.order_code || ('#' + order.order_id)), msg: m.msg, read: false };
        list.unshift(n);
        list = list.slice(0, 30);
        ls.set(K_LIST, list);
        toast(n);
        render();
    }

    async function poll() {
        try {
            const r = await fetch('/notifications?for=customer', { cache: 'no-store', credentials: 'same-origin' });
            if (r.status === 401 || r.status === 403) { clearInterval(pollTimer); return; }
            if (!r.ok) return;
            const data = await r.json();
            const orders = data.orders || [];
            const firstRun = localStorage.getItem(K_MAP) === null;
            const map = ls.get(K_MAP, {});
            const prevMax = ls.get(K_MAX, 0);
            let max = prevMax;

            orders.slice().reverse().forEach(o => {
                if (o.order_id > max) max = o.order_id;
                const prev = map[o.order_id];
                if (!firstRun) {
                    if (prev === undefined && o.order_id > prevMax) push(o);
                    else if (prev !== undefined && prev !== o.status) push(o);
                }
                map[o.order_id] = o.status;
            });

            ls.set(K_MAP, map);
            ls.set(K_MAX, max);

            // ===== Bagong reply galing sa admin =====
            const K_ADM = 'sp_c_admmsgs_' + UID;
            const admPrev = localStorage.getItem(K_ADM);
            const admNow = parseInt(data.admin_msgs, 10) || 0;
            if (admPrev !== null && admNow > parseInt(admPrev, 10)) {
                push({ status: 'New Message', order_code: '', order_id: '' });
            }
            try { localStorage.setItem(K_ADM, String(admNow)); } catch (e) {}
        } catch (e) { /* ignore network errors */ }
    }

    window.toggleNotifPanel = function () {
        const p = document.getElementById('notifPanel');
        p.classList.toggle('hidden');
        if (!p.classList.contains('hidden')) {
            list.forEach(n => n.read = true);
            ls.set(K_LIST, list);
            // iwan muna ang highlight ng unread bago i-render ulit
            setTimeout(render, 1500);
            const badge = document.getElementById('notifBadge');
            badge.classList.add('hidden');
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

// ===== Preview ng inupload na file =====
let uploadObjectUrl = null;
function previewUpload(input) {
    const box = document.getElementById('uploadPreview');
    const img = document.getElementById('uploadPreviewImg');
    const pdf = document.getElementById('uploadPreviewPdf');
    const icon = document.getElementById('uploadPreviewIcon');
    if (uploadObjectUrl) { URL.revokeObjectURL(uploadObjectUrl); uploadObjectUrl = null; }
    [img, pdf, icon].forEach(el => el.classList.add('hidden'));
    img.src = ''; pdf.src = '';

    const file = input.files && input.files[0];
    if (!file) { box.classList.add('hidden'); return; }

    uploadObjectUrl = URL.createObjectURL(file);
    const ext = (file.name.split('.').pop() || '').toLowerCase();

    if (file.type.startsWith('image/')) {
        img.src = uploadObjectUrl; img.classList.remove('hidden');
    } else if (ext === 'pdf' || file.type === 'application/pdf') {
        pdf.src = uploadObjectUrl; pdf.classList.remove('hidden');
    } else {
        const iconClass = ['doc', 'docx'].includes(ext) ? 'fa-file-word' : ['xls', 'xlsx'].includes(ext) ? 'fa-file-excel' : ['ppt', 'pptx'].includes(ext) ? 'fa-file-powerpoint' : 'fa-file-lines';
        document.getElementById('uploadPreviewIconI').className = 'fa-solid ' + iconClass + ' text-5xl text-blue-400';
        document.getElementById('uploadPreviewExt').innerText = ext.toUpperCase() + ' FILE';
        icon.classList.remove('hidden');
    }

    document.getElementById('uploadPreviewName').innerText = file.name;
    const kb = file.size / 1024;
    document.getElementById('uploadPreviewSize').innerText = '(' + (kb >= 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb.toFixed(0) + ' KB') + ')';
    box.classList.remove('hidden');
}
function clearUpload() {
    const input = document.getElementById('documentInput');
    input.value = '';
    previewUpload(input);
}

function toggleService(service) {
    ['paper', 'xerox', 'photo', 'tarp', 'sticker'].forEach(s => {
        const el = document.getElementById('service-' + s);
        if (el) el.classList.add('hidden');
    });
    const target = document.getElementById('service-' + service);
    if (target) target.classList.remove('hidden');

    const hqBox = document.getElementById('hq_option_box');
    if (hqBox) {
        hqBox.style.display = (service === 'xerox') ? 'none' : 'block';
    }
    calculateTotal();
}

function calculateTotal() {
    const productType = document.querySelector('input[name="product_type"]:checked').value;
    const isHQ = document.getElementById('is_high_quality').checked;
    let total = 0;

    if (productType === 'paper') {
        const pages = parseInt(document.getElementById('pages').value) || 1;
        const size = document.getElementById('paper_size').value;
        let rate = pages >= 21 ? 2.50 : (pages >= 11 ? 3.00 : 4.00);
        let stdAmount = (size === 'Long') ? 0.25 : 0.00;
        let hqExtra = isHQ ? 1.00 : 0.00;
        total = pages * (rate + stdAmount + hqExtra);
    } else if (productType === 'xerox') {
        const pages = parseInt(document.getElementById('xerox_pages').value) || 1;
        const copies = parseInt(document.getElementById('xerox_copies').value) || 1;
        total = pages * copies * 3.00;
    } else if (productType === 'photo') {
        const size = document.getElementById('photo_size').value;
        const qty = parseInt(document.getElementById('photo_q').value) || 1;
        let rate = (size === '4R') ? 10.00 : 50.00;
        let hqExtra = isHQ ? 5.00 : 0.00;
        total = qty * (rate + hqExtra);
    } else if (productType === 'tarp') {
        const w = parseFloat(document.getElementById('tarp_w').value) || 2;
        const h = parseFloat(document.getElementById('tarp_h').value) || 3;
        const qty = parseInt(document.getElementById('tarp_q').value) || 1;
        let rate = isHQ ? 18.00 : 15.00;
        total = (w * h) * rate * qty;
    } else if (productType === 'sticker') {
        const size = document.getElementById('sticker_size').value;
        const qty = parseInt(document.getElementById('sticker_q').value) || 1;
        let rate = (size === 'A4') ? 35.00 : 65.00;
        let hqExtra = isHQ ? 10.00 : 0.00;
        total = qty * (rate + hqExtra);
    }

    const displayEl = document.getElementById('displayTotal');
    displayEl.innerHTML = "₱" + total.toFixed(2);
}

// Run on page load
window.onload = calculateTotal;