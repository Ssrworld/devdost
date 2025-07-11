<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

// URL से प्रोजेक्ट की ID लें
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: " . BASE_URL . "browse-projects.php");
    exit;
}
$project_id = $_GET['id'];

// Eloquent का उपयोग करके प्रोजेक्ट, उसके मालिक (यूजर), और असाइन किए गए डेवलपर की जानकारी लाएं
$project = Project::with(['user', 'awardedDeveloper'])->find($project_id);

// अगर प्रोजेक्ट नहीं मिलता, तो ब्राउज पेज पर भेज दें
if (!$project) {
    header("location: " . BASE_URL . "browse-projects.php");
    exit;
}

// --- बिड्स को लोड करने का लॉजिक ---
$bids = [];
$user_can_review = false; // रिव्यु दे सकता है या नहीं, इसका फ्लैग

if (isset($_SESSION['loggedin'])) {
    $current_user_id = $_SESSION['id'];
    $current_user_type = $_SESSION['user_type'];

    if ($current_user_type == 'client' && $current_user_id == $project->user_id) {
        $bids = $project->bids()->with('developer')->latest()->get();
    }
    
    // यह चेक करें कि क्या यूजर इस प्रोजेक्ट पर रिव्यु दे सकता है
    if ($project->status == 'completed') {
        // या तो वह क्लाइंट हो या असाइन किया गया डेवलपर हो
        if (($current_user_type == 'client' && $current_user_id == $project->user_id) ||
            ($current_user_type == 'developer' && $current_user_id == $project->developer_id)) {
            $user_can_review = true;
        }
    }
}
// --- लॉजिक खत्म ---

// हेडर को include करें
include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">

        <?php // --- सफलता और एरर मैसेज दिखाने का सेक्शन --- ?>
        <?php if(isset($_GET['success'])): ?>
            <?php 
                $success_message = "Action completed successfully!";
                if ($_GET['success'] == 'BidPlaced') $success_message = "Your bid has been placed successfully!";
                if ($_GET['success'] == 'BidAccepted') $success_message = "You have successfully accepted the bid! The project is now in progress.";
                if ($_GET['success'] == 'ProjectCompleted') $success_message = "You have successfully marked the project as complete!";
            ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <?php
                $error_message = "Something went wrong. Please try again.";
                if ($_GET['error'] == 'AlreadyBid') $error_message = "You have already placed a bid on this project.";
                if ($_GET['error'] == 'InvalidData') $error_message = "Please fill in all the fields correctly.";
                if ($_GET['error'] == 'AcceptFailed') $error_message = "Failed to accept the bid. Please try again.";
                if ($_GET['error'] == 'CompletionFailed') $error_message = "Failed to mark the project as complete.";
            ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php // --- मैसेज सेक्शन यहाँ खत्म --- ?>


        <div class="project-details-layout">
            <!-- बाईं तरफ: प्रोजेक्ट की जानकारी -->
            <div class="project-main-content">
                <span class="project-status status-<?php echo htmlspecialchars($project->status); ?>"><?php echo str_replace('_', ' ', htmlspecialchars($project->status)); ?></span>
                <h1 class="project-details-title"><?php echo htmlspecialchars($project->title); ?></h1>
                <div class="project-meta">
                    <span>Posted by: <strong><?php echo htmlspecialchars($project->user->username); ?></strong></span>
                    <span>Posted: <strong><?php echo $project->created_at->format('d M, Y'); ?></strong></span>
                </div>
                <div class="project-full-description">
                    <h3>Project Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($project->description)); ?></p>
                </div>
            </div>

            <!-- दाईं तरफ: बजट और एक्शन का सेक्शन -->
            <div class="project-sidebar">
                <div class="sidebar-card budget-card">
                    <h4>Budget</h4>
                    <span class="budget-amount">₹<?php echo number_format((float)$project->budget, 2); ?></span>
                </div>
                
                <?php if ($project->status == 'open'): ?>
                    <!-- प्रोजेक्ट ओपन होने पर बिडिंग का लॉजिक -->
                    <?php include __DIR__ . '/partials/project-sidebar-open.php'; ?>
                
                <?php elseif ($project->status == 'in_progress'): ?>
                    <!-- प्रोजेक्ट इन-प्रोग्रेस होने पर लॉजिक -->
                    <div class="sidebar-card">
                        <h4>Project In Progress</h4>
                        <?php if($project->awardedDeveloper): ?>
                            <p>This project was awarded to <strong><a href="<?php echo BASE_URL . 'profile.php?username=' . urlencode($project->awardedDeveloper->username); ?>" class="developer-name-link"><?php echo htmlspecialchars($project->awardedDeveloper->username); ?></a></strong>.</p>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['id']) && $_SESSION['id'] == $project->user_id): ?>
                            <form action="<?php echo BASE_URL; ?>mark-complete.php" method="POST" onsubmit="return confirm('Are you sure you want to mark this project as complete?');" style="margin-top: 1rem;">
                                <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                                <button type="submit" class="btn btn-primary">Mark as Complete</button>
                            </form>
                        <?php endif; ?>
                    </div>

                <?php elseif ($project->status == 'completed'): ?>
                     <!-- प्रोजेक्ट कम्पलीट होने पर लॉजिक -->
                     <div class="sidebar-card">
                        <h4>Project Completed!</h4>
                        <p class="text-success">This project has been successfully completed.</p>
                        <?php if($user_can_review): ?>
                            <a href="<?php echo BASE_URL; ?>leave-review.php?project_id=<?php echo $project->id; ?>" class="btn btn-secondary" style="margin-top: 1rem;">Leave a Review</a>
                        <?php endif; ?>
                     </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
// फुटर को include करें
include_once __DIR__ . '/includes/footer.php';
?>