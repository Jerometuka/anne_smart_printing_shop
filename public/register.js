function redirectToLogin(delayMs = 1500) {
    setTimeout(function() {
        window.location.href = "/login";
    }, delayMs);
}