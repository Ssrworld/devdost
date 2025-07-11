<?php
session_start();
require_once __DIR__ . '/bootstrap.php';

use App\Models\Product;

// सभी प्रोडक्ट्स को डेटाबेस से लाएं, साथ में सेलर की जानकारी भी (Eager Loading)
$products = Product::with('user')->latest()->get();

include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Software Marketplace</h1>
            <p>Discover scripts, plugins, themes, and tools built by top developers.</p>
        </div>

        <!-- प्रोडक्ट लिस्टिंग ग्रिड -->
        <div class="product-grid">
            <?php if ($products->isEmpty()): ?>
                <div class="alert alert-info full-width-alert">
                    <p>No products available in the marketplace yet. Be the first to <a href="sell_software.php">sell your software</a>!</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="product_details.php?id=<?php echo $product->id; ?>" class="product-card-link">
                            <div class="product-image-container">
                                <?php 
                                    // ========================================================
                                    // >> यहाँ सुधार किया गया है: इमेज पाथ में 'images/' जोड़ा गया है <<
                                    $image_url = $product->preview_image 
                                                ? BASE_URL . 'assets/uploads/products/images/' . $product->preview_image
                                                : 'https://via.placeholder.com/300x200.png?text=' . urlencode($product->name);
                                    // ========================================================
                                ?>
                                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product->name); ?>" class="product-preview-image">
                            </div>
                            <div class="product-card-content">
                                <h3 class="product-name"><?php echo htmlspecialchars($product->name); ?></h3>
                                <p class="product-seller">by <?php echo htmlspecialchars($product->user->username); ?></p>
                            </div>
                            <div class="product-card-footer">
                                <span class="product-price">₹<?php echo number_format($product->price, 2); ?></span>
                                <span class="btn btn-secondary btn-sm">View Details</span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . '/includes/footer.php';
?>