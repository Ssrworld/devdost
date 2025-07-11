<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन यूज़र्स ही इस पेज को देख सकते हैं
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

$user_id = $_SESSION['id'];
$user_type = $_SESSION['user_type'];
$projects = [];
$page_title = "My Projects"; // Default title
$page_subtitle = "Here are projects related to your account."; // Default subtitle

// यूजर के टाइप के आधार पर प्रोजेक्ट्स लाएं
if ($user_type == 'client') {
    // क्लाइंट के लिए: उसके द्वारा पोस्ट किए गए सभी प्रोजेक्ट्स
    $projects = Project::where('user_id', $user_id)->latest()->get();
    $page_title = "My Posted Projects";
    $page_subtitle = "Here are all the projects you have posted on DevDost.";
} elseif ($user_type == 'developer') {
    // डेवलपर के लिए: वे सभी प्रोजेक्ट्स जो उसे असाइन किए गए हैं
    $projects = Project::where('developer_id', $user_id)->latest()->get();
    $page_title = "My Assigned Projects";
    $page_subtitle = "Here are all the projects you are working on or have completed.";
}

// हेडर को include करें
include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="page-header">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>
            <p><?php echo htmlspecialchars($page_subtitle); ?></p>
        </div>

        <div class="project-list">
            <?php if (count($projects) == 0): ?>
                <div class="alert alert-info">
                    <p>You have no projects to display here yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <a href="project-details.php?id=<?php echo $project->id; ?>" class="project-card-link">
                        <div class="project-card">
                            <div class="project-card-header">
                                <h3 class="project-title"><?php echo htmlspecialchars($project->title); ?></h3>
                                <span class="project-status status-<?php echo htmlspecialchars($project->status); ?>">
                                    <?php echo str_replace('_', ' ', htmlspecialchars($project->status)); ?>
                                </span>
                            </div>
                            <div class="project-card-body">
                                <p class="project-description">
                                    <?php echo htmlspecialchars(substr($project->description, 0, 200)); ?>...
                                </p>
                            </div>
                            <div class="project-card-footer">
                                <span class="project-budget">Budget: ₹<?php echo number_format((float)$project->budget, 2); ?></span>
                                <span class="project-posted-date">Posted: <?php echo $project->created_at->diffForHumans(); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// फुटर को include करें
include_once __DIR__ . '/includes/footer.php';
?>