<?php
// user details to display
$header_name = 'User';
$header_initials = 'U';

if (isset($sportifObj) && $sportifObj instanceof Sportif) {
    $header_name = $sportifObj->getFirstname();
    $header_initials = strtoupper(substr($sportifObj->getFirstname(), 0, 1) . substr($sportifObj->getLastname(), 0, 1));
} elseif (isset($coachObj) && $coachObj instanceof Coach) {
    $header_name = $coachObj->getFirstname();
    $header_initials = strtoupper(substr($coachObj->getFirstname(), 0, 1) . substr($coachObj->getLastname(), 0, 1));
} elseif (isset($_SESSION['user'])) {
    // by session
    $userId = $_SESSION['user']['id'];
    $role = $_SESSION['role'] ?? '';

    if ($role === 'coach') {
        $u = new Coach((int)$userId);
    } elseif ($role === 'sportif') {
        $u = new Sportif((int)$userId);
    } else {
        $u = new User((int)$userId);
    }

    if ($u && $u->getId()) {
        $header_name = $u->getFirstname();
        $header_initials = strtoupper(substr($u->getFirstname(), 0, 1) . substr($u->getLastname(), 0, 1));
    }
}
?>


<header class="h-20 glass-panel border-b border-gray-800 sticky top-0 z-30 px-8 flex items-center justify-between">
    <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-2">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <div class="flex items-center space-x-6 ml-auto">
        <button class="relative p-2 text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        </button>
        <div class="flex items-center gap-3 pl-6 border-l border-gray-700">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold text-xs uppercase">
                <?php echo htmlspecialchars($header_initials); ?>
            </div>
            <span class="text-sm font-medium text-white hidden md:block"><?php echo htmlspecialchars($header_name); ?></span>
        </div>
    </div>
</header>