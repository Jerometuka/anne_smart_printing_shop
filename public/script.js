document.addEventListener('DOMContentLoaded', function () {
    const pwInput = document.getElementById('loginPassword');
    const pwBtn = document.getElementById('togglePassword');
    const pwIcon = document.getElementById('togglePasswordIcon');

    if (pwBtn && pwInput) {
        pwBtn.addEventListener('click', function () {
            const show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            pwIcon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            pwBtn.setAttribute('aria-label', show ? 'Itago ang password' : 'Ipakita ang password');
        });
    }
});