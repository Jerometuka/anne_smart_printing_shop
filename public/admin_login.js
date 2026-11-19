const pwInput = document.getElementById('adminPassword');
const pwBtn = document.getElementById('togglePassword');
const pwIcon = document.getElementById('togglePasswordIcon');


pwBtn.addEventListener('click', function () {
    const show = pwInput.type === 'password';
    pwInput.type = show ? 'text' : 'password';
    pwIcon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    pwBtn.setAttribute('aria-label', show ? 'Itago ang password' : 'Ipakita ang password');
});


document.getElementById('adminLoginForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const username = document.getElementById('adminUsername').value;
    const password = pwInput.value;

    if (username === 'admin' && password === '1234') {
        window.location.replace('admin.blade.php'); 
    } else {
        document.getElementById('loginError').textContent = 'Maling username o password';
    }
});