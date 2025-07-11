<?php
// सेशन शुरू करें
session_start();

// >> यह सबसे महत्वपूर्ण लाइन है जो आपकी एरर को ठीक करेगी <<
// bootstrap.php को include करें ताकि Eloquent और डेटाबेस कनेक्शन लोड हो
require_once __DIR__ . '/bootstrap.php';

// User और Project मॉडल को इम्पोर्ट करें
use App\Models\User;
use App\Models\Project;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन यूजर्स ही इस पेज को देख सकते हैं
if (!isset($_SESSION["user_id"])) {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

// वेरिएबल्स को इनिशियलाइज़ करें
$title_err = $description_err = $budget_err = "";
$success_msg = "";

// जब फॉर्म सबमिट हो
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $budget = trim($_POST['budget']); // आप इसे 'salary' भी कह सकते हैं
    $skills = isset($_POST['skills']) ? trim($_POST['skills']) : ''; 

    // बेसिक वैलिडेशन (आप इसे और बेहतर बना सकते हैं)
    if (empty($title)) {
        $title_err = "Please enter a job title.";
    }
    if (empty($description)) {
        $description_err = "Please provide a job description.";
    }

    // अगर कोई एरर नहीं है
    if (empty($title_err) && empty($description_err)) {
        try {
            // Eloquent ORM का उपयोग करके नई जॉब बनाएं
            // यह `$pdo` का उपयोग करने से बेहतर और सुरक्षित है
            Project::create([
                'user_id'         => $_SESSION['user_id'],
                'title'           => $title,
                'description'     => $description,
                'budget'          => !empty($budget) ? $budget : null,
                'skills_required' => $skills,
                // >> यहाँ मुख्य अंतर है: post_type को 'job' सेट करें <<
                'post_type'       => 'job'
            ]);

            $success_msg = "Your job has been posted successfully!";

        } catch (\Exception $e) {
            error_log($e->getMessage()); // एरर को लॉग करें
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
            <!-- हेडिंग और टेक्स्ट को "Job" के लिए बदलें -->
            <h1>Post a New Job</h1>
            <p>Describe the position, and find the perfect candidate.</p>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php echo $success_msg; ?>
                    <p><a href="<?php echo BASE_URL; ?>browse_jobs.php">View Jobs</a> or <a href="<?php echo BASE_URL; ?>dashboard.php">Go to Dashboard</a>.</p>
                </div>
            <?php else: ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group form-group-vertical">
                    <label for="title">Job Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g., Senior PHP Developer" required>
                    <span class="error"><?php echo $title_err; ?></span>
                </div>
                
                <div class="form-group form-group-vertical">
                    <label for="description">Job Description</label>
                    <textarea id="description" name="description" class="form-control" rows="8" placeholder="Describe the role and responsibilities..." required></textarea>
                    <span class="error"><?php echo $description_err; ?></span>
                </div>

                <div class="form-group form-group-vertical">
                    <label for="skills">Required Skills</label>
                    <input type="text" id="skills" name="skills" class="form-control" placeholder="e.g., PHP, Laravel, MySQL, REST API">
                    <small>Separate skills with a comma.</small>
                </div>

                <div class="form-group form-group-vertical">
                    <!-- लेबल को "Salary" में बदलें -->
                    <label for="budget">Salary / Compensation (in INR, optional)</label>
                    <input type="text" id="budget" name="budget" class="form-control" placeholder="e.g., 1200000 per year">
                    <span class="error"><?php echo $budget_err; ?></span>
                </div>
                
                <div class="form-group full-width-btn">
                    <input type="submit" class="btn btn-primary btn-lg" value="Post This Job">
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