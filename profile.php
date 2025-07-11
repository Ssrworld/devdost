<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\User;

// URL से यूजरनेम लें
if (!isset($_GET['username'])) {
    die("Username not provided.");
}
$username = $_GET['username'];

// --- Eloquent का उपयोग करके यूजर, प्रोफाइल, और रिव्यु की जानकारी एक साथ लाएं ---
$user = User::with(['profile', 'reviewsReceived.reviewer'])
            ->where('username', $username)
            ->first();

// अगर उस यूजरनेम का कोई डेवलपर नहीं मिलता है
if (!$user || $user->user_type !== 'developer') {
    die("Developer profile not found.");
}

// --- प्रोफाइल और रिव्यु डेटा को वेरिएबल्स में रखें ---
$profile = $user->profile;
$reviews = $user->reviewsReceived;

// स्किल्स को कॉमा से अलग करके एक ऐरे में बदलें
$skills = $profile && $profile->skills ? explode(',', $profile->skills) : [];

// औसत रेटिंग और रिव्यु की संख्या की गणना करें
$review_count = $reviews->count();
$average_rating = $review_count > 0 ? round($reviews->avg('rating'), 1) : 0;


include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="profile-layout">
            <!-- बाईं तरफ: मुख्य जानकारी -->
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php 
                            $avatar_url = $user->avatar ? 
                                        BASE_URL . 'assets/uploads/avatars/' . $user->avatar : 
                                        'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&size=128&background=random';
                        ?>
                        <img src="<?php echo $avatar_url; ?>" alt="<?php echo htmlspecialchars($user->username); ?>">
                    </div>
                    <h1 class="profile-name"><?php echo htmlspecialchars($user->username); ?></h1>
                    
                    <div class="profile-rating">
                        <span class="stars" style="--rating: <?php echo $average_rating; ?>;">★★★★★</span>
                        <strong><?php echo $average_rating; ?></strong>
                        <span>(<?php echo $review_count; ?> reviews)</span>
                    </div>
                    
                    <?php if ($profile && $profile->tagline): ?>
                        <p class="profile-tagline"><?php echo htmlspecialchars($profile->tagline); ?></p>
                    <?php endif; ?>
                    <?php if ($profile && $profile->location): ?>
                        <p class="profile-location">📍 <?php echo htmlspecialchars($profile->location); ?></p>
                    <?php endif; ?>

                    <div class="profile-links">
                        <?php if ($profile && $profile->website_url): ?>
                            <a href="<?php echo htmlspecialchars($profile->website_url); ?>" target="_blank" class="btn btn-secondary">Website</a>
                        <?php endif; ?>
                        <?php if ($profile && $profile->github_url): ?>
                            <a href="<?php echo htmlspecialchars($profile->github_url); ?>" target="_blank" class="btn btn-secondary">GitHub</a>
                        <?php endif; ?>
                    </div>

                    <!-- ============== यहाँ नया "Send Message" बटन जोड़ा गया है ============== -->
                    <div class="profile-message-button">
                        <?php if(isset($_SESSION['loggedin']) && $_SESSION['id'] != $user->id): ?>
                            <a href="<?php echo BASE_URL; ?>start-conversation.php?user_id=<?php echo $user->id; ?>" class="btn btn-primary">Send Message</a>
                        <?php endif; ?>
                    </div>
                    <!-- ==================================================================== -->

                </div>
            </div>

            <!-- दाईं तरफ: बायो और स्किल्स -->
            <div class="profile-main-content">
                <?php if ($profile && $profile->bio): ?>
                <div class="content-card">
                    <h2>About Me</h2>
                    <p><?php echo nl2br(htmlspecialchars($profile->bio)); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($skills)): ?>
                <div class="content-card">
                    <h2>Skills</h2>
                    <div class="skills-container">
                        <?php foreach ($skills as $skill): ?>
                            <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                 <div class="content-card">
                    <h2>Work History & Reviews (<?php echo $review_count; ?>)</h2>
                    <?php if ($reviews->isEmpty()): ?>
                        <p>No reviews yet.</p>
                    <?php else: ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="reviewer-name"><?php echo htmlspecialchars($review->reviewer->username); ?></span>
                                        <span class="review-rating stars" style="--rating: <?php echo $review->rating; ?>;">★★★★★</span>
                                    </div>
                                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($review->comment)); ?></p>
                                    <small class="review-date">Posted on: <?php echo $review->created_at->format('d M, Y'); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>