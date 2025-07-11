<?php
// bootstrap.php को include करें ताकि BASE_URL मिल सके
// यह सबसे पहले होना चाहिए ताकि कोई भी आउटपुट भेजने से पहले हेडर सेट हो सकें
require_once __DIR__ . '/../bootstrap.php';

// सेशन शुरू करें
session_start();

// अगर यूजर पहले से ही लॉग-इन है, तो उसे डैशबोर्ड पर भेज दें
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: " . BASE_URL . "dashboard.php");
    exit;
}

// User मॉडल को इम्पोर्ट करें
use App\Models\User;

// वेरिएबल्स को इनिशियलाइज़ करें
$email = "";
$email_err = $password_err = $login_err = "";

// जब फॉर्म सबमिट हो
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ईमेल को वैलिडेट करें
    if (empty(trim($_POST['email']))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST['email']);
    }

    // पासवर्ड को वैलिडेट करें
    if (empty($_POST['password'])) {
        $password_err = "Please enter your password.";
    } else {
        $password = $_POST['password'];
    }

    // अगर कोई वैलिडेशन एरर नहीं है
    if (empty($email_err) && empty($password_err)) {
        // डेटाबेस से यूजर को ढूंढें
        $user = User::where('email', $email)->first();

        // अगर यूजर मौजूद है और पासवर्ड सही है
        if ($user && password_verify($password, $user->password)) {
            
            // >> यहाँ मुख्य सुधार है <<
            // सेशन को रीजेनरेट करें (सुरक्षा के लिए)
            session_regenerate_id(true);

            // सेशन वेरिएबल्स को सही और सुसंगत नामों से सेट करें
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $user->id; // <-- 'user_id' का उपयोग करें, 'id' का नहीं
            $_SESSION["username"] = $user->username;
            $_SESSION["user_type"] = $user->user_type; // <-- यह सुनिश्चित करें कि आपके users टेबल में यह कॉलम है
            
            // यूजर को डैशबोर्ड पर भेजें
            header("location: " . BASE_URL . "dashboard.php");
            exit(); // रीडायरेक्ट के बाद हमेशा exit() का उपयोग करें

        } else {
            // अगर यूजर नहीं मिला या पासवर्ड गलत है
            $login_err = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DevDost</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<main class="split-layout">
    <div class="split-layout__left">
        <div class="brand-panel">
            <a href="<?php echo BASE_URL; ?>index.php" class="logo-large">DevDost</a>
            <h1 class="brand-title">Welcome Back! Let's get to work.</h1>
            <p class="brand-subtitle">Login to manage your projects and connect with your "dost".</p>
        </div>
    </div>
    <div class="split-layout__right">
        <div class="form-container">
            <h2>Login to Your Account</h2>
            <p>Enter your details below.</p>

            <?php if(!empty($login_err)) echo '<div class="alert alert-danger">' . $login_err . '</div>'; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error"><?php echo $email_err; ?></span>
                </div>    
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <span class="error"><?php echo $password_err; ?></span>
                </div>
                
                <div class="form-extra-actions">
                    <a href="forgot-password.php" class="forgot-password-link">Forgot Password?</a>
                </div>

                <div class="form-group full-width-btn">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p class="form-footer-text">Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </form>
        </div>
    </div>
</main>

</body>
</html>