<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन 'client' ही इस पेज को देख सकता है
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== 'client') {
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
                'user_id'     => $_SESSION['id'], // लॉग-इन क्लाइंट की ID
                'title'       => $title,
                'description' => $description,
                'budget'      => !empty($budget) ? $budget : null, // अगर बजट खाली है तो NULL सेव करें
            ]);

            $success_msg = "Your project has been posted successfully!";

        } catch (\Exception $e) {
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
            <h1>Post a New Project</h1>
            <p>Describe your project, and let the best developers find you.</p>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php else: ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group form-group-vertical">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g., Build a modern e-commerce website">
                    <span class="error"><?php echo $title_err; ?></span>
                </div>
                
                <div class="form-group form-group-vertical">
                    <label for="description">Project Description</label>
                    <textarea id="description" name="description" class="form-control" rows="8" placeholder="Describe your project in detail..."></textarea>
                    <span class="error"><?php echo $description_err; ?></span>
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