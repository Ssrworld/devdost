<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\User;

// Eloquent का उपयोग करके सभी 'developer' यूजर्स और उनकी प्रोफाइल की जानकारी एक साथ लाएं
// with('profile') Eager Loading के लिए है ताकि परफॉरमेंस अच्छी रहे
$developers = User::where('user_type', 'developer')->with('profile')->latest()->paginate(12); // एक पेज पर 12 डेवलपर्स

include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Find Your Perfect Developer</h1>
            <p>Browse through our community of talented and verified developers.</p>
            <!-- बाद में यहाँ सर्च और फ़िल्टर का फॉर्म आएगा -->
        </div>

        <?php if ($developers->isEmpty()): ?>
            <div class="alert alert-info">
                <p>No developers have registered yet. Be the first one!</p>
            </div>
        <?php else: ?>
            <div class="developer-grid">
                <?php foreach ($developers as $dev): ?>
                    <a href="<?php echo BASE_URL . 'profile.php?username=' . urlencode($dev->username); ?>" class="developer-card-link">
                        <div class="developer-card">
                            <div class="developer-avatar">
                                <?php 
                                    $avatar_url = $dev->avatar ? 
                                                BASE_URL . 'assets/uploads/avatars/' . $dev->avatar : 
                                                'https://ui-avatars.com/api/?name=' . urlencode($dev->username) . '&size=96&background=random';
                                ?>
                                <img src="<?php echo $avatar_url; ?>" alt="<?php echo htmlspecialchars($dev->username); ?>">
                            </div>
                            <h3 class="developer-name"><?php echo htmlspecialchars($dev->username); ?></h3>
                            <?php if ($dev->profile && $dev->profile->tagline): ?>
                                <p class="developer-tagline"><?php echo htmlspecialchars($dev->profile->tagline); ?></p>
                            <?php else: ?>
                                 <p class="developer-tagline text-muted">No tagline provided.</p>
                            <?php endif; ?>
                            
                            <div class="developer-skills">
                                <?php 
                                    $skills = $dev->profile && $dev->profile->skills ? explode(',', $dev->profile->skills, 3) : []; // सिर्फ 3 स्किल्स दिखाएंगे
                                    foreach($skills as $skill) {
                                        echo '<span class="skill-tag-sm">' . htmlspecialchars(trim($skill)) . '</span>';
                                    }
                                    if(count(explode(',', $dev->profile->skills ?? '')) > 3) echo '<span class="skill-tag-sm">...</span>';
                                ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Links -->
            <div class="pagination-links">
                <?php echo $developers->links(); ?>
            </div>

        <?php endif; ?>
    </div>
</main>

<?php
include_once __DIR__ . '/includes/footer.php';
?>