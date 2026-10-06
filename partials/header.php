<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/Config.php';

$whatsapp_link = defined('WHATSAPP_NUMBER') && !empty(WHATSAPP_NUMBER)
    ? (preg_match('#^https?://#i', WHATSAPP_NUMBER)
        ? WHATSAPP_NUMBER
        : 'https://wa.me/' . preg_replace('/[^0-9]/', '', WHATSAPP_NUMBER))
    : 'https://wa.me/2349078918123';

$instagram_link = defined('INSTAGRAM_HANDLE') && !empty(INSTAGRAM_HANDLE)
    ? (stripos(INSTAGRAM_HANDLE, 'instagram.com') !== false
        ? INSTAGRAM_HANDLE
        : 'https://www.instagram.com/' . ltrim(INSTAGRAM_HANDLE, '@/'))
    : 'https://www.instagram.com/samprimeautos_';

$x_link = defined('X_HANDLE') && !empty(X_HANDLE)
    ? ((stripos(X_HANDLE, 'x.com') !== false || stripos(X_HANDLE, 'twitter.com') !== false)
        ? X_HANDLE
        : 'https://x.com/' . ltrim(X_HANDLE, '@/'))
    : 'https://x.com/Samthebasisst_';

$current_page = basename($_SERVER['PHP_SELF']);
$hide_view_cars_link = ($current_page === 'details.php' || $current_page === 'cars.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samprime Autos Global - Premium Automobile Showcase</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand-logo">
                <img src="assets/logo.jpeg" alt="Samprime Autos Global" class="brand-logo-img"> SAMPRIME <span>AUTOS</span>
            </a>
            <?php if (!$hide_view_cars_link): ?>
                <div class="nav-links">
                    <a href="cars.php" class="btn btn-primary">
                        <i class="fa-solid fa-car-rear"></i> View Cars
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
