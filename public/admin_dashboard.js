// I-load ito PAGKATAPOS ng admin.js. Nire-redraw nito ang #salesChart bilang bar chart.
window.addEventListener('load', function () {
    // 1) Monthly sales bar chart
    var canvas = document.getElementById('salesChart');
    if (canvas && window.Chart) {
        var old = Chart.getChart(canvas);
        if (old) old.destroy();

        var data = window.monthlySalesData || new Array(12).fill(0);
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{ data: data, backgroundColor: '#f59e0b', borderRadius: 3, maxBarThickness: 34 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (c) {
                                return ' ₱' + Number(c.parsed.y).toLocaleString('en-PH', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,.12)' },
                        ticks: { color: '#94a3b8', font: { size: 10 }, callback: function (v) { return '₱' + v; } }
                    }
                }
            }
        });
    }

    // 2) Search: sinasala ang rows ng Active Orders at Order History
    var search = document.getElementById('admSearch');
    if (search) {
        search.addEventListener('input', function () {
            var q = search.value.trim().toLowerCase();
            document.querySelectorAll('#orders tbody tr, #history tbody tr').forEach(function (tr) {
                tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }

    // 3) "Open For Order": visual lang muna, naaalala sa browser
    var open = document.getElementById('openForOrder');
    if (open) {
        try { open.checked = localStorage.getItem('openForOrder') !== '0'; } catch (e) {}
        open.addEventListener('change', function () {
            try { localStorage.setItem('openForOrder', open.checked ? '1' : '0'); } catch (e) {}
        });
    }
});