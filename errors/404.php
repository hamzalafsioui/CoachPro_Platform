<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | CoachPro</title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/CoachPro_Platform/assets/css/style.css">

    <!-- Tailwind Config -->
    <script src="/CoachPro_Platform/assets/js/tailwind.config.js"></script>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="glass-dark max-w-2xl w-full mx-4 p-8 md:p-12 rounded-3xl text-center relative z-10 shadow-2xl border border-white/10">
        <!-- Icon -->
        <div class="mb-8 relative inline-block">
            <div class="w-24 h-24 gradient-blue rounded-full flex items-center justify-center mx-auto pulse-glow relative z-10">
                <i class="fas fa-compass text-4xl text-white"></i>
            </div>
            <div class="absolute inset-0 bg-blue-500 blur-xl opacity-50 animate-pulse"></div>
        </div>

        <!-- Text Content -->
        <h1 class="text-7xl font-outfit font-bold text-white mb-2 tracking-tight">404</h1>
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-200 mb-4">Page Not Found</h2>
        <p class="text-gray-400 text-lg mb-8 max-w-md mx-auto leading-relaxed">
            Oops! The page you're looking for seems to have wandered off during a workout session.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/CoachPro_Platform/index.php" class="w-full sm:w-auto gradient-blue px-8 py-3.5 rounded-xl text-white font-semibold hover-scale smooth-transition shadow-lg flex items-center justify-center gap-2 group">
                <i class="fas fa-home group-hover:-translate-y-0.5 transition-transform"></i>
                <span>Back to Home</span>
            </a>
            <button onclick="history.back()" class="w-full sm:w-auto glass border border-white/10 px-8 py-3.5 rounded-xl text-white font-semibold hover:bg-white/10 smooth-transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Go Back</span>
            </button>
        </div>
    </div>

</body>

</html>