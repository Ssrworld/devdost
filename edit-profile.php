<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\User;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन 'developer' ही इस पेज को देख सकता है
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== 'developer') {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

$user = User::with('profile')->find($_SESSION['id']);
// अगर प्रोफाइल नहीं है, तो एक नई खाली प्रोफाइल बनाएं ताकि फॉर्म में एरर न आए
$profile = $user->profile ?: $user->profile()->create([]);

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- प्रोफाइल फोटो अपलोड का लॉजिक ---
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["avatar"]["name"];
        $filetype = $_FILES["avatar"]["type"];
        $filesize = $_FILES["avatar"]["size"];

        // फाइल एक्सटेंशन और टाइप को वेरीफाई करें
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            $error_msg = "Error: Please select a valid file format (JPG, PNG, GIF).";
        }

        // फाइल साइज को वेरीफाई करें (5MB मैक्स)
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $error_msg = "Error: File size is larger than the allowed 5MB limit.";
        }

        // अगर कोई एरर नहीं है तो अपलोड करें
        if (empty($error_msg)) {
            // एक यूनिक नाम बनाएं
            $new_filename = uniqid('avatar_', true) . "." . $ext;
            $upload_path = __DIR__ . '/assets/uploads/avatars/' . $new_filename;

            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $upload_path)) {
                // पुराना अवतार डिलीट करें अगर मौजूद है
                if ($user->avatar && file_exists(__DIR__ . '/assets/uploads/avatars/' . $user->avatar)) {
                    unlink(__DIR__ . '/assets/uploads/avatars/' . $user->avatar);
                }
                // डेटाबेस में नया फाइल नाम सेव करें
                $user->avatar = $new_filename;
                $user->save();
            } else {
                $error_msg = "Error: There was a problem uploading your file.";
            }
        }
    }
    // --- फोटो अपलोड का लॉजिक यहाँ खत्म ---

    // बाकी प्रोफाइल जानकारी को अपडेट करें
    $profile->tagline = $_POST['tagline'];
    $profile->bio = $_POST['bio'];
    $profile->skills = $_POST['skills'];
    $profile->location = $_POST['location'];
    $profile->website_url = $_POST['website_url'];
    $profile->github_url = $_POST['github_url'];
    
    if ($profile->save() && empty($error_msg)) {
        $success_msg = "Your profile has been updated successfully!";
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="form-wrapper">
            <h1>Edit Your Profile</h1>
            <p>Keep your profile up-to-date to attract more clients.</p>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                
                <!-- ============== नया प्रोफाइल फोटो फील्ड ============== -->
                <div class="form-group form-group-vertical">
                    <label>Profile Picture</label>
                    <div class="avatar-upload-wrapper">
                        <?php 
                            $avatar_url = $user->avatar ? 
                                        BASE_URL . 'assets/uploads/avatars/' . $user->avatar : 
                                        'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&size=128&background=random';
                        ?>
                        <img src="<?php echo $avatar_url; ?>" alt="Current Avatar" class="current-avatar">
                        <input type="file" name="avatar" id="avatar" class="form-control">
                    </div>
                     <small>Upload a square image (JPG, PNG, GIF). Max size: 5MB.</small>
                </div>
                <!-- ==================================================== -->

                <div class="form-group form-group-vertical">
                    <label for="tagline">Tagline</label>
                    <input type="text" id="tagline" name="tagline" class="form-control" placeholder="e.g., Senior PHP & Laravel Developer" value="<?php echo htmlspecialchars($profile->tagline ?? ''); ?>">
                </div>
                <div class="form-group form-group-vertical">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" placeholder="e.g., Mumbai, India" value="<?php echo htmlspecialchars($profile->location ?? ''); ?>">
                </div>
                <div class="form-group form-group-vertical">
                    <label for="skills">Skills</label>
                    <input type="text" id="skills" name="skills" class="form-control" placeholder="e.g., PHP, JavaScript, Vue.js, MySQL" value="<?php echo htmlspecialchars($profile->skills ?? ''); ?>">
                    <small>Please enter skills separated by commas.</small>
                </div>
                <div class="form-group form-group-vertical">
                    <label for="bio">About Me</label>
                    <textarea id="bio" name="bio" class="form-control" rows="8" placeholder="Tell clients a little about yourself, your experience, and what makes you a great developer."><?php echo htmlspecialchars($profile->bio ?? ''); ?></textarea>
                </div>
                <div class="form-group form-group-vertical">
                    <label for="website_url">Website URL (Optional)</label>
                    <input type="url" id="website_url" name="website_url" class="form-control" placeholder="https://my-portfolio.com" value="<?php echo htmlspecialchars($profile->website_url ?? ''); ?>">
                </div>
                <div class="form-group form-group-vertical">
                    <label for="github_url">GitHub URL (Optional)</label>
                    <input type="url" id="github_url" name="github_url" class="form-control" placeholder="https://github.com/my-username" value="<?php echo htmlspecialchars($profile->github_url ?? ''); ?>">
                </div>
                <div class="form-group full-width-btn">
                    <input type="submit" class="btn btn-primary btn-lg" value="Save Profile">
                </div>
            </form>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>