function toggleOnlinePayment(isOnline) {
    const qrBox = document.getElementById('qr_container');
    qrBox.style.display = isOnline ? 'block' : 'none';
}

// Function para lumitaw agad ang preview ng inupload na screenshot sa screen
function previewImage(event) {
    const reader = new FileReader();
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');

    reader.onload = function() {
        imagePreview.src = reader.result;
        previewContainer.classList.remove('hidden');
    }

    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}