<?php
session_start();

// Mock data
$page_title = "My Clients";
$clients = [
    [
        'id' => 1,
        'name' => 'John Doe',
        'avatar' => 'JD',
        'status' => 'active',
        'plan' => 'Premium - Personal Training',
        'join_date' => 'Oct 15, 2023',
        'progress' => 75,
        'last_session' => '2 days ago'
    ],
    [
        'id' => 2,
        'name' => 'Sarah Smith',
        'avatar' => 'SS',
        'status' => 'active',
        'plan' => 'Standard - HIIT',
        'join_date' => 'Nov 02, 2023',
        'progress' => 45,
        'last_session' => 'Yesterday'
    ],
    [
        'id' => 3,
        'name' => 'Mike Johnson',
        'avatar' => 'MJ',
        'status' => 'inactive',
        'plan' => 'Basic - Strength',
        'join_date' => 'Sep 10, 2023',
        'progress' => 90,
        'last_session' => '2 weeks ago'
    ],
    [
        'id' => 4,
        'name' => 'Emma Wilson',
        'avatar' => 'EW',
        'status' => 'active',
        'plan' => 'Premium - Cardio',
        'join_date' => 'Dec 01, 2023',
        'progress' => 20,
        'last_session' => 'Today'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/coach_clients.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>

</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php require '../../includes/coach_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php include '../../includes/header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto space-y-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">My Clients</h1>
                    <p class="text-gray-400">Manage your athlete portfolio and track progress.</p>
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-64">
                    <input type="text" id="clientSearch" placeholder="Search clients..." class="w-full bg-gray-800/50 border border-gray-700 rounded-xl px-4 py-2.5 pl-10 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Clients Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($clients as $client): ?>
                    <div class="glass-panel p-6 rounded-2xl client-card flex flex-col h-full" data-name="<?php echo htmlspecialchars($client['name']); ?>" data-plan="<?php echo htmlspecialchars($client['plan']); ?>">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-xl font-bold text-white shadow-lg">
                                    <?php echo $client['avatar']; ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white text-lg"><?php echo htmlspecialchars($client['name']); ?></h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="status-badge status-<?php echo $client['status']; ?> text-xs">
                                            <?php echo ucfirst($client['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>

                        <div class="space-y-4 flex-1">
                            <div>
                                <p class="text-gray-500 text-xs uppercase font-semibold mb-1">Current Plan</p>
                                <p class="text-gray-300"><?php echo htmlspecialchars($client['plan']); ?></p>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-500 font-semibold uppercase">Progress</span>
                                    <span class="text-blue-400"><?php echo $client['progress']; ?>%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-value" style="width: <?php echo $client['progress']; ?>%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div>
                                    <p class="text-gray-500 text-xs uppercase font-semibold mb-1">Joined</p>
                                    <p class="text-gray-300 text-sm"><?php echo $client['join_date']; ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs uppercase font-semibold mb-1">Last Session</p>
                                    <p class="text-gray-300 text-sm"><?php echo $client['last_session']; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button onclick="messageClient(<?php echo $client['id']; ?>)" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-comment-alt"></i> Message
                            </button>
                            <button onclick="openClientModal(<?php echo $client['id']; ?>)" class="flex-1 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 py-2 rounded-lg text-sm font-medium transition-colors border border-blue-500/30">
                                Details
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- JS -->
    <script src="../../assets/js/coach_clients.js"></script>
</body>

</html>