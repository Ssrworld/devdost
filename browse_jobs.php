<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

// >> यहाँ मुख्य बदलाव है <<
// Eloquent का उपयोग करके सभी 'open' और 'job' टाइप के पोस्ट्स को लाएं
$jobs = Project::where('status', 'open')
               ->where('post_type', 'job') // <-- यहाँ 'job' से फिल्टर किया गया है
               ->with('user')
               ->latest()
               ->get();

// हेडर को include करें
include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Browse Available Jobs</h1>
            <p>Find your next career opportunity. Here are the latest job openings.</p>
        </div>

        <div class="project-list">
            <?php if ($jobs->isEmpty()): ?>
                <div class="alert alert-info">
                    <p>No jobs available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="project-card">
                        <div class="project-card-header">
                            <h3 class="project-title"><a href="project-details.php?id=<?php echo $job->id; ?>"><?php echo htmlspecialchars($job->title); ?></a></h3>
                            <!-- 'budget' की जगह 'salary' शब्द का इस्तेमाल कर सकते हैं -->
                            <span class="project-budget">Salary: ₹<?php echo number_format((float)$job->budget, 2); ?></span>
                        </div>
                        <div class="project-card-body">
                            <p class="project-description">
                                <?php echo htmlspecialchars(substr($job->description, 0, 200)); ?>...
                            </p>
                        </div>
                        <div class="project-card-footer">
                            <span class="project-client">Posted by: <?php echo htmlspecialchars($job->user->username); ?></span>
                            <span class="project-posted-date">Posted: <?php echo $job->created_at->diffForHumans(); ?></span>
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