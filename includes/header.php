<?php
// user details for display
$header_name = 'User';
$header_initials = 'U';

if (isset($_SESSION['user'])) {
    $header_name = $_SESSION['user']['firstname'];
    $header_initials = substr($_SESSION['user']['firstname'], 0, 1) . substr($_SESSION['user']['lastname'], 0, 1);
} elseif (isset($coach_name)) {
    $header_name = $coach_name;
    $parts = explode(' ', $coach_name);
    $header_initials = substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '');
} elseif (isset($user) && is_array($user) && isset($user['firstname'])) {
    $header_name = $user['firstname'];
    $header_initials = substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1);
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