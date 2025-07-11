<?php
// सेशन शुरू करें, यह हमेशा PHP कोड के टॉप पर होना चाहिए
session_start();

// bootstrap.php को include करें ताकि BASE_URL मिल सके
// यह इसलिए जरूरी है क्योंकि हम header() में इसका इस्तेमाल कर रहे हैं
require_once __DIR__ . '/../bootstrap.php';

// अगर यूजर पहले से ही लॉग-इन है, तो उसे डैशबोर्ड पर भेज दें
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: " . BASE_URL . "dashboard.php");
    exit;
}

// User मॉडल को इम्पोर्ट करें
use App\Models\User;

$email_err = $password_err = $login_err = "";

// जब फॉर्म सबमिट हो
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) {
        $email_err = "Please enter your email.";
    }
    if (empty($password)) {
        $password_err = "Please enter your password.";
    }

    if (empty($email_err) && empty($password_err)) {
        $user = User::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            // पासवर्ड सही है, तो सेशन वेरिएबल्स सेट करें
            // यह सुनिश्चित करता है कि पुराना सेशन डेटा साफ हो जाए (Session Fixation Attack से बचाव)
            session_regenerate_id(true);

            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $user->id;
            $_SESSION["username"] = $user->username;
            $_SESSION["user_type"] = $user->user_type;
            
            // यूजर को डैशबोर्ड पर भेजें
            header("location: " . BASE_URL . "dashboard.php");
            exit(); // रीडायरेक्ट के बाद हमेशा exit() का उपयोग करें
        } else {
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
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                    <span class="error"><?php echo $email_err; ?></span>
                </div>    
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
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