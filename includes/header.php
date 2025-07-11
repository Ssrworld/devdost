<?php
// bootstrap.php को include करें ताकि BASE_URL और ORM लोड हो जाएं
if (file_exists(__DIR__ . '/../bootstrap.php')) {
    require_once __DIR__ . '/../bootstrap.php';
}

// सेशन को शुरू करें अगर यह पहले से शुरू नहीं है
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevDost - Hire Expert Developers in India</title>
    <!-- CSS File Link with BASE_URL -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

    <header class="main-header">
        <div class="container">
            <nav class="main-nav">
                <!-- SVG Logo with BASE_URL -->
                <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                    <svg width="150" height="40" viewBox="0 0 150 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.4 35.2V4.8H28.4C30.9333 4.8 33.0167 5.51667 34.65 6.95C36.2833 8.38333 37.1 10.1667 37.1 12.3C37.1 14.3 36.35 15.9833 34.85 17.35C33.35 18.7167 31.4 19.4 29 19.4H25.6V35.2H22.4ZM25.6 16.6H28.6C29.8 16.6 30.75 16.25 31.45 15.55C32.15 14.85 32.5 13.8 32.5 12.4C32.5 11 32.15 10 31.45 9.3C30.75 8.6 29.8 8.2 28.6 8.2H25.6V16.6Z" fill="#2A65EA"/>
                        <path d="M43.25 35.2V4.8H46.45V35.2H43.25Z" fill="#2A65EA"/>
                        <path d="M57.45 35.2V4.8H68.85V8.2H60.65V16.8H67.65V20.2H60.65V31.8H69.25V35.2H57.45Z" fill="#2A65EA"/>
                        <path d="M74.2 35.2V4.8H77.4V35.2H74.2Z" fill="#2A65EA"/>
                        <path d="M96.2 35.2V4.8H106.3C108.9 4.8 111.05 5.56667 112.75 7.1C114.45 8.63333 115.3 10.5167 115.3 12.75C115.3 15.05 114.45 16.9667 112.75 18.5C111.05 20.0333 108.9 20.8 106.3 20.8H99.4V35.2H96.2ZM99.4 17.4H106C107.533 17.4 108.767 16.9 109.7 15.9C110.633 14.9 111.1 13.7 111.1 12.3C111.1 10.9 110.633 9.76667 109.7 8.9C108.767 8.03333 107.533 7.6 106 7.6H99.4V17.4Z" fill="#333333"/>
                        <path d="M121.25 35.2L116.25 24.8L120.45 16.8L126.65 28.2L121.25 35.2ZM129.85 8.2L124.65 18.6L122.45 13.6L127.45 4.8H131.25L125.65 14.8L133.45 35.2H129.25L123.85 24.6L118.85 35.2H115.05L122.65 20.2L117.85 8.2H121.85L124.65 13.2L127.05 8.2H129.85Z" fill="#333333"/>
                    </svg>
                </a>
                <ul class="main-menu">
                    <li><a href="<?php echo BASE_URL; ?>browse-projects.php">Browse Projects</a></li>
                    <li><a href="<?php echo BASE_URL; ?>browse-developers.php">Find Developers</a></li>
                    <li><a href="#">How It Works</a></li>

                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <!-- अगर यूजर लॉग-इन है -->
                        <li><a href="<?php echo BASE_URL; ?>dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo BASE_URL; ?>messages.php">Messages</a></li>
                        
                        <!-- ============== यहाँ से नया नोटिफिकेशन ड्रॉपडाउन ============== -->
                        <li class="notification-dropdown">
                            <a href="#" id="notification-bell">
                                🔔
                                <?php 
                                    // अनपढ़ी नोटिफिकेशन्स की गिनती लाएं
                                    // Eloquent को सीधे इस्तेमाल करने के लिए App\Models\Notification लिखना होगा
                                    $unread_count = isset($_SESSION['id']) ? \App\Models\Notification::where('user_id', $_SESSION['id'])->where('is_read', false)->count() : 0;
                                    if ($unread_count > 0) {
                                        echo '<span class="notification-count">' . $unread_count . '</span>';
                                    }
                                ?>
                            </a>
                            <div class="dropdown-content">
                                <div class="dropdown-header">Notifications</div>
                                <ul id="notification-list">
                                    <!-- नोटिफिकेशन्स यहाँ AJAX से लोड होंगी -->
                                    <li>Loading...</li>
                                </ul>
                            </div>
                        </li>
                        <!-- ======================================================== -->

                        <li><a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-secondary">Logout</a></li>
                    <?php else: ?>
                        <!-- अगर यूजर लॉग-इन नहीं है -->
                        <li><a href="<?php echo BASE_URL; ?>pages/login.php">Login</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/register.php" class="btn btn-primary">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>