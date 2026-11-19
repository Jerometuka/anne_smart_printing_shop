 <body> (galing sa layouts/admin.blade.php) */
window.ADMIN = {
    dashboardUrl: document.body.dataset.dashboardUrl,
    notificationsUrl: document.body.dataset.notificationsUrl,
    monthlySales: JSON.parse(document.body.dataset.monthlySales || '[]'),
};