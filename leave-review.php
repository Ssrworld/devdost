<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;
use App\Models\Review;

// एक्सेस कंट्रोल
if (!isset($_SESSION["loggedin"])) {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    header("location: " . BASE_URL . "dashboard.php");
    exit;
}

$project = Project::findOrFail($_GET['project_id']);

// यह तय करें कि रिव्यु किसे दिया जा रहा है
$reviewee_id = ($_SESSION['user_type'] == 'client') ? $project->developer_id : $project->user_id;

// चेक करें कि क्या यूजर इस प्रोजेक्ट पर पहले ही रिव्यु दे चुका है
$existing_review = Review::where('project_id', $project->id)->where('reviewer_id', $_SESSION['id'])->exists();

$rating_err = $comment_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$existing_review) {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    if(empty($rating) || $rating < 1 || $rating > 5) $rating_err = "Please select a rating.";
    if(empty($comment)) $comment_err = "Please leave a comment.";

    if(empty($rating_err) && empty($comment_err)) {
        Review::create([
            'project_id' => $project->id,
            'reviewer_id' => $_SESSION['id'],
            'reviewee_id' => $reviewee_id,
            'rating' => $rating,
            'comment' => $comment,
            'review_type' => $_SESSION['user_type'] == 'client' ? 'client_to_developer' : 'developer_to_client',
        ]);
        $success_msg = "Your review has been submitted successfully!";
    }
}

include_once __DIR__ . '/includes/header.php';
?>
<main class="page-container">
    <div class="container">
        <div class="form-wrapper">
            <h1>Leave a Review for "<?php echo htmlspecialchars($project->title); ?>"</h1>
            
            <?php if ($existing_review || !empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg ?: 'You have already submitted a review for this project.'; ?></div>
                <a href="<?php echo BASE_URL; ?>project-details.php?id=<?php echo $project->id; ?>" class="btn btn-secondary">Back to Project</a>
            <?php else: ?>
                <p>Please share your experience to help others in the community.</p>
                <form action="" method="post">
                    <div class="form-group form-group-vertical">
                        <label>Your Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="5-stars" name="rating" value="5" /><label for="5-stars">★</label>
                            <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars">★</label>
                            <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars">★</label>
                            <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars">★</label>
                            <input type="radio" id="1-star" name="rating" value="1" /><label for="1-star">★</label>
                        </div>
                         <span class="error"><?php echo $rating_err; ?></span>
                    </div>
                    <div class="form-group form-group-vertical">
                        <label for="comment">Your Comment</label>
                        <textarea id="comment" name="comment" class="form-control" rows="6"></textarea>
                        <span class="error"><?php echo $comment_err; ?></span>
                    </div>
                    <div class="form-group full-width-btn">
                        <input type="submit" class="btn btn-primary" value="Submit Review">
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include_once __DIR__ . '/includes/footer.php'; ?>