<?php
// सेशन को हमेशा सबसे पहले शुरू करें
session_start();

// जरूरी फाइल्स को include करें (ताकि BASE_URL मिल सके)
require_once __DIR__ . '/bootstrap.php';

// यह चेक करें कि यूजर लॉग-इन है या नहीं। अगर नहीं, तो उसे लॉगिन पेज पर भेज दें।
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

// हेडर को include करें
include_once __DIR__ . '/includes/header.php';
?>

<main class="dashboard-page">
    <div class="container">
        <h1 class="dashboard-title">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <p class="dashboard-subtitle">This is your personal dashboard. From here, you can manage everything.</p>

        <div class="dashboard-grid">
            
            <?php // यह चेक करें कि यूजर का टाइप क्या है और उसके अनुसार ऑप्शन दिखाएं ?>

            <?php if ($_SESSION["user_type"] == 'developer'): ?>
                <!-- डेवलपर के लिए ऑप्शन -->
                <div class="dashboard-card">
                    <h3>My Profile</h3>
                    <p>Update your skills, portfolio, and bio to attract clients.</p>
                    
                    <!-- ================== यह लाइन अपडेट की गई है ================== -->
                    <a href="<?php echo BASE_URL; ?>edit-profile.php" class="btn btn-secondary">Edit Profile</a>
                    <!-- ========================================================== -->

                </div>
                <div class="dashboard-card">
                    <h3>Find Projects</h3>
                    <p>Browse and bid on projects that match your expertise.</p>
                    <a href="<?php echo BASE_URL; ?>browse-projects.php" class="btn btn-primary">Browse Projects</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>My Work</h3>
                    <p>View projects assigned to you and manage their progress.</p>
                    <a href="<?php echo BASE_URL; ?>my-projects.php" class="btn btn-secondary">View My Work</a>
                </div>
            
            <?php elseif ($_SESSION["user_type"] == 'client'): ?>
                <!-- क्लाइंट के लिए ऑप्शन -->
                <div class="dashboard-card">
                    <h3>Post a New Project</h3>
                    <p>Have a new idea? Post a project and get proposals from top developers.</p>
                    <a href="<?php echo BASE_URL; ?>post-project.php" class="btn btn-primary">Post Project</a>
                </div>
                <div class="dashboard-card">
                    <h3>My Projects</h3>
                    <p>Manage your active, completed, and pending projects.</p>
                    <a href="<?php echo BASE_URL; ?>my-projects.php" class="btn btn-secondary">View My Projects</a>
                </div>
                <div class="dashboard-card">
                    <h3>Manage Payments</h3>
                    <p>View your transaction history and manage payment methods.</p>
                    <a href="#" class="btn btn-secondary">Payment History</a>
                </div>

            <?php endif; ?>

            <div class="dashboard-card logout-card">
                <h3>Account Settings</h3>
                <p>Need to logout or change your password?</p>
                <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-danger">Logout</a>
            </div>

        </div>
    </div>
</main>

<?php
// फुटर को include करें
include_once __DIR__ . '/includes/footer.php';
?>