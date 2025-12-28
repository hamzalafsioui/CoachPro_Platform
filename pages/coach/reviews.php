<?php
require_once '../../config/App.php';

// Authentication Check
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$coachObj = new Coach((int)$userId);
$coachId = $coachObj->getCoachIdByUserId();

if (!$coachId) {
    $reviews = [];
    $overall_rating = 0.0;
    $total_reviews = 0;
    $rating_breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
} else {
    $reviewObj = new Review();
    $reviews = $reviewObj->getCoachReviews($coachId);
    $stats = $reviewObj->getCoachReviewStats($coachId);
    
    $overall_rating = $stats['avg_rating'];
    $total_reviews = $stats['total_reviews'];
    $rating_breakdown = $stats['rating_breakdown'];
}

$page_title = "Client Reviews";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/coach_reviews.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>
</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php include '../../includes/coach_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php include '../../includes/header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto space-y-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">Client Reviews</h1>
                    <p class="text-gray-400">Detailed feedback from your coaching sessions.</p>
                </div>
            </div>

            <!-- Rating Overview -->
            <div class="glass-panel p-8 rounded-2xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    <!-- Average Rating -->
                    <div class="text-center md:text-left md:border-r border-gray-700 pr-0 md:pr-8">
                        <div class="text-6xl font-bold text-white mb-2 font-outfit"><?php echo $overall_rating; ?></div>
                        <div class="flex justify-center md:justify-start gap-1 text-yellow-500 text-xl mb-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="text-gray-400">Based on <?php echo $total_reviews; ?> reviews</p>
                    </div>

                    <!-- Rating Breakdown -->
                    <div class="col-span-2 space-y-3">
                        <?php foreach ($rating_breakdown as $stars => $count):
                            $percentage = ($count / $total_reviews) * 100;
                        ?>
                            <div class="flex items-center gap-4">
                                <div class="w-12 text-sm text-gray-400 flex items-center gap-1">
                                    <span><?php echo $stars; ?></span> <i class="fas fa-star text-xs"></i>
                                </div>
                                <div class="flex-1 h-2 rounded-full progress-bar-bg overflow-hidden">
                                    <div class="h-full bg-yellow-500 rounded-full progress-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="w-12 text-sm text-gray-400 text-right"><?php echo $count; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="space-y-6">
                <?php foreach ($reviews as $review): ?>
                    <div class="glass-panel p-6 rounded-2xl review-card" id="review-card-<?php echo $review['id']; ?>">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Client Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center text-lg font-bold text-white border border-gray-600">
                                    <?php echo $review['avatar']; ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2">
                                    <div>
                                        <h3 class="font-bold text-white text-lg"><?php echo $review['client']; ?></h3>
                                        <p class="text-sm text-gray-400"><?php echo $review['session_type']; ?></p>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 md:mt-0"><?php echo $review['date']; ?></span>
                                </div>

                                <div class="flex gap-1 text-yellow-500 text-sm mb-3">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php if ($i < $review['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-gray-600"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>

                                <p class="text-gray-300 leading-relaxed mb-4">
                                    "<?php echo htmlspecialchars($review['comment']); ?>"
                                </p>

                                <!-- Existing Reply -->
                                <?php if (!empty($review['reply'])): ?>
                                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                <?php echo strtoupper(substr($coachObj->getFirstname(), 0, 1) . substr($coachObj->getLastname(), 0, 1)); ?>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-white font-medium text-sm">You</span>
                                                    <span class="text-gray-500 text-xs"><?php echo htmlspecialchars($review['reply']['date']); ?></span>
                                                </div>
                                                <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($review['reply']['text']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Actions -->
                                <div class="flex justify-between items-center border-t border-gray-700/50 pt-4 actions-area">
                                    <?php if (empty($review['reply'])): ?>
                                        <button onclick="toggleReply(<?php echo $review['id']; ?>)" class="text-sm text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-2">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                    <?php else: ?>
                                        <button onclick="toggleReply(<?php echo $review['id']; ?>)" class="text-sm text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-2">
                                            <i class="fas fa-edit"></i> Edit Reply
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Reply Form -->
                                <div id="reply-form-<?php echo $review['id']; ?>" class="hidden mt-4 bg-gray-900/50 p-4 rounded-xl border border-gray-700/50">
                                    <textarea id="reply-textarea-<?php echo $review['id']; ?>" class="w-full bg-gray-800 border-none rounded-lg p-3 text-white text-sm focus:ring-1 focus:ring-blue-500 mb-2 placeholder-gray-500" rows="3" placeholder="Write your reply..."><?php echo !empty($review['reply']) ? htmlspecialchars($review['reply']['text']) : ''; ?></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button onclick="toggleReply(<?php echo $review['id']; ?>)" class="px-3 py-1.5 text-xs text-gray-400 hover:text-white transition-colors">Cancel</button>
                                        <button onclick="submitReply(<?php echo $review['id']; ?>)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs rounded-lg transition-colors"><?php echo !empty($review['reply']) ? 'Update Reply' : 'Send Reply'; ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- JS -->
    <script src="../../assets/js/coach_reviews.js"></script>
</body>

</html>