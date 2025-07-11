<?php
// bootstrap.php को include करें ताकि ORM, Autoloader और BASE_URL लोड हो जाएं
require_once __DIR__ . '/../bootstrap.php';

// User मॉडल को इम्पोर्ट करें
use App\Models\User;

// सेशन शुरू करें
session_start();

// अगर यूजर पहले से ही लॉग-इन है, तो उसे डैशबोर्ड पर भेज दें
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: " . BASE_URL . "dashboard.php"); // बाद में dashboard.php बनाएंगे
    exit;
}

// एरर वेरिएबल्स को खाली रखें
$username_err = $email_err = $password_err = $user_type_err = "";
$success_msg = "";

// जब फॉर्म सबमिट हो, तो डेटा प्रोसेस करें
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // बेसिक वैलिडेशन
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'] ?? '';

    if (empty($username)) {
        $username_err = "Please enter a username.";
    }

    if (empty($email)) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $password_err = "Please enter a password.";
    } elseif (strlen($password) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }

    if (empty($user_type)) {
        $user_type_err = "Please select an account type.";
    }

    // डुप्लीकेट ईमेल और यूजरनेम चेक करें
    if (empty($username_err) && User::where('username', $username)->exists()) {
        $username_err = "This username is already taken.";
    }
    if (empty($email_err) && User::where('email', $email)->exists()) {
        $email_err = "This email is already registered.";
    }

    // अगर कोई एरर नहीं है, तो यूजर बनाएं
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($user_type_err)) {
        try {
            User::create([
                'username'  => $username,
                'email'     => $email,
                'password'  => password_hash($password, PASSWORD_DEFAULT),
                'user_type' => $user_type,
            ]);
            $success_msg = "Registration successful! You can now <a href='login.php' style='color: #065F46; font-weight: bold;'>login</a>.";
        } catch (\Exception $e) {
            die("Oops! Something went wrong. Please try again later.");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - DevDost</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<main class="split-layout">
    <div class="split-layout__left">
        <div class="brand-panel">
            <a href="<?php echo BASE_URL; ?>index.php" class="logo-large">DevDost</a>
            <h1 class="brand-title">Join a community of India's finest developers and clients.</h1>
            <p class="brand-subtitle">Build your dream project or find your next big opportunity.</p>
        </div>
    </div>
    <div class="split-layout__right">
        <div class="form-container">
            <h2>Create Your Account</h2>
            <p>Let's get started!</p>
            
            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php else: ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <span class="error"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <span class="error"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    <span class="error"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group form-group-vertical">
                    <label>I am a:</label>
                    <select name="user_type" class="form-control">
                        <option value="">-- Select Account Type --</option>
                        <option value="client" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'client') ? 'selected' : ''; ?>>Client (I want to hire)</option>
                        <option value="developer" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'developer') ? 'selected' : ''; ?>>Developer (I'm looking for work)</option>
                    </select>
                    <span class="error"><?php echo $user_type_err; ?></span>
                </div>
                <div class="form-group full-width-btn">
                    <input type="submit" class="btn btn-primary" value="Create Account">
                </div>
                <p class="form-footer-text">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
            
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>