const STEP_LABELS = ['Pending', 'Printing', 'Ready', 'Done'];

function statusMeta(s) {
    const l = String(s || '').toLowerCase();
    if (l.includes('cancel')) return { step: -1, color: '#f43f5e', cls: 'text-rose-300 bg-rose-500/15 border-rose-500/40' };
    if (l.includes('ready')) return { step: 2, color: '#8b5cf6', cls: 'text-violet-300 bg-violet-500/15 border-violet-500/40' };
    if (/complete|done|claim|success|deliver/.test(l)) return { step: 3, color: '#10b981', cls: 'text-emerald-300 bg-emerald-500/15 border-emerald-500/40' };
    if (/process|print|progress/.test(l)) return { step: 1, color: '#0ea5e9', cls: 'text-sky-300 bg-sky-500/15 border-sky-500/40' };
    return { step: 0, color: '#f59e0b', cls: 'text-amber-300 bg-amber-500/15 border-amber-500/40' };
}

// Hanapin ang inupload na file (kahit iba ang pangalan ng column)
function getOrderFile(o) {
    const keys = ['document_file', 'file_path', 'file_name', 'uploaded_file', 'file', 'document', 'attachment'];
    for (const k of keys) { if (o[k]) return String(o[k]); }
    return '';
}
function toFileUrl(f) {
    if (/^(https?:)?\/\//.test(f) || f.startsWith('uploads/') || f.startsWith('/')) return f;
    return '/uploads/' + f;
}

function openDetailsModal(order) {
    document.getElementById('modal_code').innerText = order.order_code;
    document.getElementById('modal_product').innerText = order.product_name;
    document.getElementById('modal_name').innerText = order.full_name;

    const orderDate = new Date(String(order.created_at).replace(' ', 'T'));
    document.getElementById('modal_date').innerText = isNaN(orderDate) ? order.created_at
        : orderDate.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    document.getElementById('modal_payment').innerText = String(order.payment_method).toUpperCase() + " (" + order.payment_status + ")";
    document.getElementById('modal_specs').innerText = order.specifications ? order.specifications : "No additional specifications.";
    document.getElementById('modal_total').innerText = '₱' + Number(order.total || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Status badge + tracker
    const meta = statusMeta(order.status);
    const badge = document.getElementById('modal_status_badge');
    badge.innerText = order.status;
    badge.className = 'inline-block mt-2 text-base px-5 py-2 rounded-full uppercase font-extrabold border ' + meta.cls;

    const tracker = document.getElementById('modal_tracker');
    if (meta.step < 0) {
        tracker.innerHTML = '<p class="text-base font-bold text-rose-300"><i class="fa-solid fa-ban mr-1"></i> This order was cancelled.</p>';
    } else {
        tracker.innerHTML = STEP_LABELS.map((label, i) => {
            const on = i <= meta.step;
            const dot = `<div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full border-2 flex items-center justify-center ${on ? 'border-transparent' : 'border-slate-600 bg-slate-800'}" style="${on ? 'background:' + meta.color : ''}">
                    ${i < meta.step ? '<i class="fa-solid fa-check text-xs text-white"></i>' : ''}
                </div>
                <span class="text-base mt-2 font-bold ${on ? 'text-slate-200' : 'text-slate-500'}">${label}</span></div>`;
            const line = i < STEP_LABELS.length - 1
                ? `<div class="flex-1 h-1 mx-2 -mt-9 rounded" style="background:${i < meta.step ? meta.color : '#334155'}"></div>` : '';
            return dot + line;
        }).join('');
    }

    document.getElementById('modal_anim').classList.toggle('hidden', meta.step !== 1);
    document.getElementById('modal_anim_wait').classList.toggle('hidden', meta.step !== 0);
    document.getElementById('modal_anim_ready').classList.toggle('hidden', meta.step !== 2);

    // Proof of Delivery (galing sa admin)
    const proofBox = document.getElementById('modal_proof_box');
    if (order.proof_image) {
        const purl = '/uploads/' + order.proof_image;
        document.getElementById('modal_proof_img').src = purl;
        document.getElementById('modal_proof_link').href = purl;
        document.getElementById('modal_proof_dl').href = purl;
        document.getElementById('modal_proof_btn').onclick = () => openProofLightbox(purl);
        proofBox.classList.remove('hidden');
    } else {
        document.getElementById('modal_proof_img').src = '';
        proofBox.classList.add('hidden');
    }

    // Inupload na file
    const img = document.getElementById('modal_img_tag');
    const pdf = document.getElementById('modal_pdf_frame');
    const icon = document.getElementById('modal_file_icon');
    const noFile = document.getElementById('modal_no_file');
    const actions = document.getElementById('modal_file_actions');
    [img, pdf, icon, noFile].forEach(el => el.classList.add('hidden'));
    img.src = ''; pdf.src = '';

    const file = getOrderFile(order);
    if (file) {
        const url = '/order-file?order=' + encodeURIComponent(order.order_id);
        const ext = file.split('.').pop().toLowerCase();
        document.getElementById('modal_file_name').innerText = file.split('/').pop();
        document.getElementById('modal_file_link').href = url;
        document.getElementById('modal_file_dl').href = url;
        actions.classList.remove('hidden');

        if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
            img.src = url; img.classList.remove('hidden');
        } else if (ext === 'pdf') {
            pdf.src = url; pdf.classList.remove('hidden');
        } else {
            document.getElementById('modal_file_ext').innerText = ext.toUpperCase() + ' FILE';
            document.getElementById('modal_file_icon_i').className = 'fa-solid ' + (['doc', 'docx'].includes(ext) ? 'fa-file-word' : ['xls', 'xlsx'].includes(ext) ? 'fa-file-excel' : 'fa-file-lines') + ' text-6xl text-blue-400';
            icon.classList.remove('hidden');
        }
    } else {
        document.getElementById('modal_file_name').innerText = '';
        noFile.classList.remove('hidden');
        actions.classList.add('hidden');
    }

    const modal = document.getElementById('detailsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modal_pdf_frame').src = '';
}
function openProofLightbox(url) {
    document.getElementById('proofLightboxImg').src = url;
    const lb = document.getElementById('proofLightbox');
    lb.classList.remove('hidden'); lb.classList.add('flex');
}
function closeProofLightbox() {
    const lb = document.getElementById('proofLightbox');
    lb.classList.add('hidden'); lb.classList.remove('flex');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (!document.getElementById('proofLightbox').classList.contains('hidden')) closeProofLightbox();
        else closeDetailsModal();
    }
});
// Auto-refresh kada 20 segundo (kapag hindi nakabukas ang details) para makita agad ang bagong status
setInterval(() => { if (document.getElementById('detailsModal').classList.contains('hidden') && document.getElementById('proofLightbox').classList.contains('hidden')) location.reload(); }, 20000);