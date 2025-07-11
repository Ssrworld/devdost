<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन 'client' ही इस पेज को देख सकता है
// >> नोट: आप शायद भविष्य में 'client' की जगह सभी यूजर्स को पोस्ट करने की अनुमति देना चाहें
if (!isset($_SESSION["user_id"])) { // सिर्फ user_id से चेक करना पर्याप्त है
    // अगर एक्सेस नहीं है, तो उसे लॉगिन पेज पर भेज दें
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

$title_err = $description_err = $budget_err = "";
$success_msg = "";

// जब फॉर्म सबमिट हो
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $budget = trim($_POST['budget']);
    // >> यहाँ बदलाव है: स्किल्स को भी ले रहे हैं (मानकर कि फॉर्म में यह फील्ड है)
    $skills = isset($_POST['skills']) ? trim($_POST['skills']) : ''; 

    // बेसिक वैलिडेशन
    if (empty($title)) {
        $title_err = "Please enter a project title.";
    }
    if (empty($description)) {
        $description_err = "Please provide a project description.";
    }
    if (!empty($budget) && !is_numeric($budget)) {
        $budget_err = "Please enter a valid budget amount.";
    }

    // अगर कोई एरर नहीं है
    if (empty($title_err) && empty($description_err) && empty($budget_err)) {
        try {
            // Eloquent ORM का उपयोग करके नया प्रोजेक्ट बनाएं
            Project::create([
                'user_id'         => $_SESSION['user_id'], // लॉग-इन क्लाइंट की ID
                'title'           => $title,
                'description'     => $description,
                'budget'          => !empty($budget) ? $budget : null, // अगर बजट खाली है तो NULL सेव करें
                'skills_required' => $skills, // स्किल्स को सेव करें
                // >> यहाँ मुख्य बदलाव है: post_type को हार्डकोड कर रहे हैं
                'post_type'       => 'project'
            ]);

            $success_msg = "Your project has been posted successfully!";
            
            // >> सफलता के बाद डैशबोर्ड पर भेजना एक अच्छा विचार है
            // header("refresh:3;url=" . BASE_URL . "dashboard.php");

        } catch (\Exception $e) {
            // बेहतर एरर रिपोर्टिंग के लिए
            error_log($e->getMessage());
            die("Oops! Something went wrong. Please try again later.");
        }
    }
}

// हेडर को include करें
include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="form-wrapper">
            <h1>Post a New Freelance Project</h1>
            <p>Describe your project, and let the best developers find you.</p>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php echo $success_msg; ?>
                    <p><a href="<?php echo BASE_URL; ?>browse_projects.php">View Projects</a> or <a href="<?php echo BASE_URL; ?>dashboard.php">Go to Dashboard</a>.</p>
                </div>
            <?php else: ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group form-group-vertical">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g., Build a modern e-commerce website" required>
                    <span class="error"><?php echo $title_err; ?></span>
                </div>
                
                <div class="form-group form-group-vertical">
                    <label for="description">Project Description</label>
                    <textarea id="description" name="description" class="form-control" rows="8" placeholder="Describe your project in detail..." required></textarea>
                    <span class="error"><?php echo $description_err; ?></span>
                </div>

                <!-- >> स्किल्स के लिए एक नया फील्ड जोड़ा गया है -->
                <div class="form-group form-group-vertical">
                    <label for="skills">Required Skills</label>
                    <input type="text" id="skills" name="skills" class="form-control" placeholder="e.g., PHP, MySQL, JavaScript, HTML, CSS">
                    <small>Separate skills with a comma.</small>
                </div>

                <div class="form-group form-group-vertical">
                    <label for="budget">Your Budget (in INR, optional)</label>
                    <input type="text" id="budget" name="budget" class="form-control" placeholder="e.g., 50000">
                    <span class="error"><?php echo $budget_err; ?></span>
                </div>
                
                <div class="form-group full-width-btn">
                    <input type="submit" class="btn btn-primary btn-lg" value="Post My Project">
                </div>
            </form>

            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// फुटर को include करें
include_once __DIR__ . '/includes/footer.php';
?>