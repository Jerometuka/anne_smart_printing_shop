
(function () { const c = document.getElementById('chatScroll'); if (c) c.scrollTop = c.scrollHeight; })();
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.getElementById('proofModal').classList.add('hidden'); });
function openProofModal(imgSrc) {
    document.getElementById('modalProofImg').src = imgSrc;
    document.getElementById('proofModal').classList.remove('hidden');
}


(function () {
    const form = document.getElementById('chatForm');
    if (!form) return;
    const input = form.querySelector('input[name="message"]');
    const fileInput = form.querySelector('input[name="chat_file"]');
    const btn = form.querySelector('button[type="submit"]');
    let sending = false;


    async function refreshChat() {
        const r = await fetch('/messages', { cache: 'no-store', credentials: 'same-origin' });
        if (!r.ok) throw new Error('refresh failed');
        const doc = new DOMParser().parseFromString(await r.text(), 'text/html');
        const fresh = doc.getElementById('chatScroll');
        const box = document.getElementById('chatScroll');
        if (!fresh || !box) throw new Error('chat box missing');
        const oldCount = box.querySelectorAll('.msg-in').length;
        box.innerHTML = fresh.innerHTML;

        box.querySelectorAll('.msg-in').forEach((el, i) => { if (i < oldCount) el.style.animation = 'none'; });
        box.scrollTop = box.scrollHeight;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (sending) return;
        const hasText = input.value.trim() !== '';
        const hasFile = fileInput.files && fileInput.files.length > 0;
        if (!hasText && !hasFile) return;

        sending = true;
        btn.disabled = true;
        btn.classList.add('opacity-60');

        let posted = false;
        try {
            const res = await fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin' });
            if (!res.ok) throw new Error('send failed');
            posted = true;
            form.reset();
            await refreshChat();
        } catch (err) {
            
            if (!posted) { sending = false; form.submit(); return; }
        }

        sending = false;
        btn.disabled = false;
        btn.classList.remove('opacity-60');
        input.focus();
    });
})();