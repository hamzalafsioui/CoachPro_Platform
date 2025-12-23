<?php
require_once '../../config/App.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

// Get Coach ID
$coachObj = new Coach($_SESSION['user_id']);
$coachId = $coachObj->getCoachIdByUserId();

if (!$coachId) {
    $reservations = [];
} else {
    $reservationObj = new Reservation();
    $reservations = $reservationObj->getByCoach($coachId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/coach_reservations.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>

    <style>

    </style>
</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php require_once '../../includes/coach_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php require_once '../../includes/header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto space-y-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">Reservations</h1>
                    <p class="text-gray-400">Manage all your booking requests and history.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn active bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium" data-filter="all">All</button>
                <button class="filter-btn bg-gray-800 text-gray-400 px-5 py-2 rounded-lg text-sm font-medium hover:text-white" data-filter="pending">Pending</button>
                <button class="filter-btn bg-gray-800 text-gray-400 px-5 py-2 rounded-lg text-sm font-medium hover:text-white" data-filter="confirmed">Upcoming</button>
                <button class="filter-btn bg-gray-800 text-gray-400 px-5 py-2 rounded-lg text-sm font-medium hover:text-white" data-filter="completed">Completed</button>
                <button class="filter-btn bg-gray-800 text-gray-400 px-5 py-2 rounded-lg text-sm font-medium hover:text-white" data-filter="cancelled">Cancelled</button>
            </div>

            <!-- Reservations List -->
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($reservations as $res): ?>
                    <div class="glass-panel p-6 rounded-2xl reservation-card" data-status="<?php echo $res['status']; ?>" data-id="<?php echo $res['id']; ?>">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <!-- Client Info -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-lg font-bold text-white border border-gray-600 shadow-md">
                                    <?php echo $res['avatar']; ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white text-lg"><?php echo $res['client']; ?></h3>
                                    <div class="flex items-center gap-2 text-sm text-gray-400">
                                        <span><?php echo $res['type']; ?></span>
                                        <span>&bull;</span>
                                        <span><?php echo $res['price']; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time -->
                            <div class="flex items-center gap-6">
                                <div class="text-right md:text-left">
                                    <div class="flex items-center gap-2 text-gray-300">
                                        <i class="fas fa-calendar text-blue-400"></i>
                                        <span><?php echo date('M d, Y', strtotime($res['date'])); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-400 text-sm mt-1">
                                        <i class="fas fa-clock text-blue-400"></i>
                                        <span><?php echo $res['time']; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                <span class="status-badge status-<?php echo $res['status']; ?>">
                                    <?php echo ucfirst($res['status']); ?>
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 w-full md:w-auto mt-4 md:mt-0">
                                <?php if ($res['status'] === 'pending'): ?>
                                    <button onclick="handleAction('accept', <?php echo $res['id']; ?>)" class="flex-1 md:flex-none px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-check mr-1"></i> Accept
                                    </button>
                                    <button onclick="handleAction('decline', <?php echo $res['id']; ?>)" class="flex-1 md:flex-none px-4 py-2 bg-red-600/20 hover:bg-red-600/40 text-red-500 rounded-lg text-sm font-medium transition-colors border border-red-600/30">
                                        <i class="fas fa-times mr-1"></i> Decline
                                    </button>
                                <?php elseif ($res['status'] === 'confirmed'): ?>
                                    <button onclick="handleAction('cancel', <?php echo $res['id']; ?>)" class="flex-1 md:flex-none px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm font-medium transition-colors">
                                        Cancel
                                    </button>
                                <?php else: ?>
                                    <button class="flex-1 md:flex-none px-4 py-2 bg-gray-800 text-gray-500 rounded-lg text-sm font-medium cursor-not-allowed">
                                        Archived
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- JS -->
    <script src="../../assets/js/coach_reservations.js"></script>
</body>

</html>