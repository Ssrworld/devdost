<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

// >> यहाँ मुख्य बदलाव है <<
// Eloquent का उपयोग करके सभी 'open' और 'project' टाइप के प्रोजेक्ट्स को लाएं
// with('user') का उपयोग करके हम हर प्रोजेक्ट के साथ उसके क्लाइंट की जानकारी भी ले आएंगे
$projects = Project::where('status', 'open')
                   ->where('post_type', 'project') // <-- यह नई कंडीशन है
                   ->with('user')
                   ->latest()
                   ->get();

// हेडर को include करें
include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Browse Freelance Projects</h1>
            <p>Find the next exciting project to work on. Here are the latest opportunities.</p>
        </div>

        <div class="project-list">
            <?php if ($projects->isEmpty()): ?>
                <div class="alert alert-info">
                    <p>No freelance projects available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="project-card-header">
                            <h3 class="project-title"><a href="project-details.php?id=<?php echo $project->id; ?>"><?php echo htmlspecialchars($project->title); ?></a></h3>
                            <span class="project-budget">Budget: ₹<?php echo number_format((float)$project->budget, 2); ?></span>
                        </div>
                        <div class="project-card-body">
                            <p class="project-description">
                                <?php echo htmlspecialchars(substr($project->description, 0, 200)); ?>...
                            </p>
                        </div>
                        <div class="project-card-footer">
                            <span class="project-client">Posted by: <?php echo htmlspecialchars($project->user->username); ?></span>
                            <span class="project-posted-date">Posted: <?php echo $project->created_at->diffForHumans(); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// फुटर को include करें
include_once __DIR__ . '/includes/footer.php';
?>