document.addEventListener('DOMContentLoaded', function() {
    // Fetch data and initialize charts
    fetch('handlers/analytics_handler.php')
        .then(response => response.json())
        .then(data => {
            initializePopularExhibitsChart(data.popularExhibits);
            initializeLanguageChart(data.languageDistribution);
        });
});

function initializePopularExhibitsChart(data) {
    const ctx = document.getElementById('popularExhibitsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.title),
            datasets: [{
                label: 'Visits in Last 30 Days',
                data: data.map(item => item.visits),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

function initializeLanguageChart(data) {
    const ctx = document.getElementById('languageChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                data: data.map(item => item.usage_count),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.5)',
                    'rgba(16, 185, 129, 0.5)',
                    'rgba(245, 158, 11, 0.5)',
                    'rgba(239, 68, 68, 0.5)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}