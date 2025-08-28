<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashSpin - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="light">
    <!-- Sidebar -->
    <div id="sidebar">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <a href="#" class="flex items-center">
                    <div class="logo-icon hidden">
                        <i class="fas fa-coins text-xl text-purple-600"></i>
                    </div>
                    <span class="logo-text text-xl font-bold text-purple-600">CashSpin</span>
                </a>
                <button id="sidebarToggle" class="text-gray-500 hover:text-purple-600 focus:outline-none">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <!-- User Profile -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center">
                <div class="relative">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="w-10 h-10 rounded-full">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-success-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                </div>
                <div class="ml-3 sidebar-text">
                    <p class="text-sm font-medium">Admin User</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Super Admin</p>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto">
                <nav class="p-4 space-y-1">
                    <div>
                        <p class="nav-group-title sidebar-text">Dashboard</p>
                        <a href="#" class="nav-link active flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-tachometer-alt mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Overview</span>
                        </a>
                    </div>

                    <div>
                        <p class="nav-group-title sidebar-text">Betting</p>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-dice mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Live Bets</span>
                            <span class="ml-auto bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-100 text-xs font-medium px-2 py-0.5 rounded-full">42</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-history mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Bet History</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-trophy mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Winning Bets</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-ban mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Cancelled Bets</span>
                        </a>
                    </div>

                    <div>
                        <p class="nav-group-title sidebar-text">Users</p>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-users mr-3 text-purple-600"></i>
                            <span class="sidebar-text">All Users</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-user-plus mr-3 text-purple-600"></i>
                            <span class="sidebar-text">New Users</span>
                            <span class="ml-auto bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-100 text-xs font-medium px-2 py-0.5 rounded-full">5</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-user-shield mr-3 text-purple-600"></i>
                            <span class="sidebar-text">VIP Users</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-user-lock mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Banned Users</span>
                        </a>
                    </div>

                    <div>
                        <p class="nav-group-title sidebar-text">Payments</p>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-money-bill-wave mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Deposits</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-hand-holding-usd mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Withdrawals</span>
                            <span class="ml-auto bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-100 text-xs font-medium px-2 py-0.5 rounded-full">12</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-exchange-alt mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Transactions</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-credit-card mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Payment Methods</span>
                        </a>
                    </div>

                    <div>
                        <p class="nav-group-title sidebar-text">Games</p>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-gamepad mr-3 text-purple-600"></i>
                            <span class="sidebar-text">All Games</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-plus-circle mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Add New Game</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-chart-line mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Game Performance</span>
                        </a>
                    </div>

                    <div>
                        <p class="nav-group-title sidebar-text">Reports</p>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-chart-pie mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Financial Reports</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-chart-bar mr-3 text-purple-600"></i>
                            <span class="sidebar-text">User Activity</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-file-export mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Export Data</span>
                        </a>
                    </div>

                    <div>
                        <p class="nav-group-title sidebar-text">Settings</p>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-cog mr-3 text-purple-600"></i>
                            <span class="sidebar-text">System Settings</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-shield-alt mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Security</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-bell mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Notifications</span>
                        </a>
                        <a href="#" class="nav-link flex items-center px-3 py-2 rounded-md">
                            <i class="fas fa-plug mr-3 text-purple-600"></i>
                            <span class="sidebar-text">Integrations</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between sidebar-text">
                    <div class="theme-toggle light" id="themeToggle">
                        <div class="theme-toggle-handle"></div>
                    </div>
                    <a href="#" class="flex items-center text-sm text-gray-500 hover:text-purple-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="sidebar-text">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Header -->
        <header id="header" class="flex items-center justify-between px-6">
            <div class="flex items-center">
                <button id="mobileMenuToggle" class="mr-4 text-gray-500 hover:text-purple-600 lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-semibold">Dashboard Overview</h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Search...">
                </div>

                <div class="relative">
                    <button id="notificationButton" class="p-2 text-gray-500 hover:text-purple-600 relative">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <div id="notificationDropdown" class="dropdown-menu w-80 p-0">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-medium">Notifications (5)</h3>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-80 overflow-y-auto">
                            <a href="#" class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                        <i class="fas fa-coins text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">New high roller bet placed</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">$5,000 on Roulette - 5 minutes ago</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-success-100 dark:bg-success-900 flex items-center justify-center">
                                        <i class="fas fa-check text-success-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">Withdrawal processed</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">$2,500 to Bank Transfer - 1 hour ago</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-warning-100 dark:bg-warning-900 flex items-center justify-center">
                                        <i class="fas fa-exclamation text-warning-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">Suspicious activity detected</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">User ID: 45782 - 2 hours ago</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-info-100 dark:bg-info-900 flex items-center justify-center">
                                        <i class="fas fa-user-plus text-info-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">New VIP member registered</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Deposited $10,000 - 5 hours ago</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-danger-100 dark:bg-danger-900 flex items-center justify-center">
                                        <i class="fas fa-server text-danger-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium">Server load high</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">85% CPU usage - 8 hours ago</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-center">
                            <a href="#" class="text-sm font-medium text-purple-600 hover:text-purple-700">View all notifications</a>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline-block">Admin User</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div id="userMenuDropdown" class="dropdown-menu">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="font-medium">Admin User</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">admin@cashspin.com</p>
                        </div>
                        <div class="p-1">
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Activity Log</a>
                        </div>
                        <div class="p-1 border-t border-gray-200 dark:border-gray-700">
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                            <p class="text-2xl font-semibold">12,487</p>
                            <p class="text-xs text-success-500 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>8.5% from last week</span>
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                            <i class="fas fa-users text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Bets</p>
                            <p class="text-2xl font-semibold">1,248</p>
                            <p class="text-xs text-success-500 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>15.2% from last week</span>
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <i class="fas fa-dice text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Revenue</p>
                            <p class="text-2xl font-semibold">$124,789</p>
                            <p class="text-xs text-danger-500 flex items-center">
                                <i class="fas fa-arrow-down mr-1"></i>
                                <span>3.4% from yesterday</span>
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Withdrawals</p>
                            <p class="text-2xl font-semibold">$58,420</p>
                            <p class="text-xs text-success-500 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>22.7% from last week</span>
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                            <i class="fas fa-hand-holding-usd text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Player</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Game</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bet Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payout</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Johnson">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Sarah Johnson</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">VIP Member</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-cards text-purple-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Blackjack</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                $3,500
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                $7,000
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-1"></i> Won
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                15 min ago
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael Brown">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Michael Brown</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">New Player</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-slot-machine text-blue-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Mega Fortune</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                $3,500
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                $52,500
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-1"></i> Won (Jackpot)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                32 min ago
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael Brown">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Michael Brown</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">New Player</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-slot-machine text-blue-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Diamond Wild</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                $2,800
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600 dark:text-red-400">
                                $0
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    <i class="fas fa-times-circle mr-1"></i> Lost
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                1 hour ago
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Emily Davis">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Emily Davis</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">High Roller</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-poker-chips text-yellow-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Texas Hold'em</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                $4,200
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                $8,400
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-1"></i> Won
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                2 hours ago
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Games Management & User Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Game Management</h2>
                    <button class="text-sm text-purple-600 hover:text-purple-700 flex items-center">
                        <i class="fas fa-plus-circle mr-1"></i>
                        <span>Add Game</span>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Game</th>
                                <th>Bets</th>
                                <th>Revenue</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-dice text-blue-600"></i>
                                    </div>
                                    <span>Roulette</span>
                                </td>
                                <td>1,245</td>
                                <td>$42,580</td>
                                <td><span class="badge game-status-active">Active</span></td>
                            </tr>
                            <tr>
                                <td class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-cards text-green-600"></i>
                                    </div>
                                    <span>Blackjack</span>
                                </td>
                                <td>892</td>
                                <td>$31,240</td>
                                <td><span class="badge game-status-active">Active</span></td>
                            </tr>
                            <tr>
                                <td class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-slot-machine text-yellow-600"></i>
                                    </div>
                                    <span>Slots</span>
                                </td>
                                <td>2,347</td>
                                <td>$68,920</td>
                                <td><span class="badge game-status-active">Active</span></td>
                            </tr>
                            <tr>
                                <td class="flex items-center">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-poker-chips text-red-600"></i>
                                    </div>
                                    <span>Poker</span>
                                </td>
                                <td>756</td>
                                <td>$25,380</td>
                                <td><span class="badge game-status-maintenance">Maintenance</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Recent User Activity</h2>
                    <a href="#" class="text-sm text-purple-600 hover:text-purple-700">View All</a>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                            <i class="fas fa-user-plus text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">New VIP member registered</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">John Doe - Deposited $15,000 - 10 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <i class="fas fa-trophy text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Big win on Roulette</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sarah Johnson won $25,000 - 1 hour ago</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Suspicious activity detected</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Multiple accounts from same IP - 3 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <i class="fas fa-hand-holding-usd text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Large withdrawal requested</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Michael Brown - $12,000 - 5 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Notification Dropdown -->
<script src="js/app.js"></script>
</body> 
</html> 