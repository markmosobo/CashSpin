// Sidebar Toggle
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');
const sidebarToggle = document.getElementById('sidebarToggle');
const mobileMenuToggle = document.getElementById('mobileMenuToggle');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
    
    const icon = sidebarToggle.querySelector('i');
    if (sidebar.classList.contains('collapsed')) {
        icon.classList.remove('fa-chevron-left');
        icon.classList.add('fa-chevron-right');
    } else {
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-left');
    }
});

mobileMenuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
const tables = document.querySelectorAll('table'); // Select all tables

themeToggle.addEventListener('click', () => {
    themeToggle.classList.toggle('dark');
    if (themeToggle.classList.contains('dark')) {
        body.classList.remove('light');
        body.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        
        // Apply dark mode to tables
        tables.forEach(table => {
            table.classList.remove('light-mode');
            table.classList.add('dark-mode');
        });
    } else {
        body.classList.remove('dark');
        body.classList.add('light');
        localStorage.setItem('theme', 'light');
        
        // Remove dark mode from tables
        tables.forEach(table => {
            table.classList.remove('dark-mode');
            table.classList.add('light-mode');
        });
    }
    updateChartsTheme();
});

// Check for saved theme preference
if (localStorage.getItem('theme') === 'dark') {
    body.classList.remove('light');
    body.classList.add('dark');
    themeToggle.classList.add('dark');
    
    // Apply dark mode to tables on initial load
    tables.forEach(table => {
        table.classList.remove('light-mode');
        table.classList.add('dark-mode');
    });
} else {
    body.classList.remove('dark');
    body.classList.add('light');
    themeToggle.classList.remove('dark');
    
    // Ensure tables are in light mode on initial load
    tables.forEach(table => {
        table.classList.remove('dark-mode');
        table.classList.add('light-mode');
    });
}

// Dropdown Menus
const notificationButton = document.getElementById('notificationButton');
const notificationDropdown = document.getElementById('notificationDropdown');
const userMenuButton = document.getElementById('userMenuButton');
const userMenuDropdown = document.getElementById('userMenuDropdown');

notificationButton.addEventListener('click', () => {
    notificationDropdown.classList.toggle('show');
    userMenuDropdown.classList.remove('show');
});

userMenuButton.addEventListener('click', () => {
    userMenuDropdown.classList.toggle('show');
    notificationDropdown.classList.remove('show');
});

// Close dropdowns when clicking outside
document.addEventListener('click', (event) => {
    if (!notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.remove('show');
    }
    if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
        userMenuDropdown.classList.remove('show');
    }
});

// Revenue Chart
const revenueChartOptions = {
    series: [
        {
            name: "Revenue",
            data: [45000, 52000, 48000, 61000, 59000, 72000, 79000, 101000, 92000, 85000, 98000, 105000]
        },
        {
            name: "Bets",
            data: [1200, 1400, 1300, 1600, 1500, 1800, 1900, 2100, 2000, 1900, 2200, 2400]
        }
    ],
    chart: {
        height: '100%',
        type: 'area',
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true
            }
        },
        zoom: {
            enabled: true
        }
    },
    colors: ['var(--primary)', 'var(--info)'], 
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 2
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
            stops: [0, 90, 100]
        }
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        }
    },
    yaxis: [
        {
            seriesName: "Revenue",
            axisTicks: {
                show: true
            },
            axisBorder: {
                show: true,
                color: 'var(--primary)' 
            },
            labels: {
                style: {
                    colors: 'var(--primary)' 
                },
                formatter: function (value) {
                    return '$' + (value / 1000) + 'k';
                }
            },
            title: {
                text: "Revenue (USD)",
                style: {
                    color: 'var(--primary)' 
                }
            }
        },
        {
            seriesName: "Bets",
            opposite: true,
            axisTicks: {
                show: true
            },
            axisBorder: {
                show: true,
                color: 'var(--info)' // Fixed: Added quotes
            },
            labels: {
                style: {
                    colors: 'var(--info)' // Fixed: Added quotes
                }
            },
            title: {
                text: "Bets Count",
                style: {
                    color: 'var(--info)' // Fixed: Added quotes
                }
            }
        }
    ],
    tooltip: {
        y: {
            formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                return seriesIndex === 0 ? '$' + value.toLocaleString() : value.toLocaleString() + ' bets';
            }
        }
    },
    legend: {
        position: 'top'
    }
};

const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueChartOptions);
revenueChart.render();

// Game Distribution Chart
const gameDistributionChartOptions = {
    series: [44, 55, 41, 17, 15],
    chart: {
        type: 'donut',
        height: '100%'
    },
    colors: ['var(--primary)', 'var(--success)', 'var(--warning)', 'var(--danger)', 'var(--info)'], // Fixed: Added quotes
    labels: ['Slots', 'Roulette', 'Blackjack', 'Poker', 'Other'],
    legend: {
        position: 'bottom'
    },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Bets',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => {
                                return a + b
                            }, 0).toLocaleString()
                        }
                    }
                }
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

const gameDistributionChart = new ApexCharts(document.querySelector("#gameDistributionChart"), gameDistributionChartOptions);
gameDistributionChart.render();

// Update charts theme when toggled
function updateChartsTheme() {
    const theme = body.classList.contains('dark') ? 'dark' : 'light';
    
    revenueChart.updateOptions({
        theme: {
            mode: theme
        }
    });
    
    gameDistributionChart.updateOptions({
        theme: {
            mode: theme
        }
    });
}

// Initialize tooltips
try {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
} catch (error) {
    console.warn('Bootstrap tooltips could not be initialized:', error);
}