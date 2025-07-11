<?php
session_start();
require_once __DIR__ . '/bootstrap.php';

use App\Models\Product;
use App\Models\Category; // Category मॉडल को इम्पोर्ट करें

// एक्सेस कंट्रोल
if (!isset($_SESSION["user_id"])) {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

// >> नया: डेटाबेस से सभी कैटेगरी को लाएं ताकि हम उन्हें फॉर्म में दिखा सकें
$categories = Category::all();

// वेरिएबल्स को इनिशियलाइज़ करें
$name_err = $description_err = $price_err = $file_err = $image_err = $category_err = "";
$success_msg = "";

// जब फॉर्म सबमिट हो
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. टेक्स्ट और कैटेगरी इनपुट को वैलिडेट करें
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $user_id = $_SESSION['user_id'];
    $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : []; // यह एक ऐरे होगा

    if (empty($name)) $name_err = "Product name is required.";
    if (empty($description)) $description_err = "Description is required.";
    if (!is_numeric($price) || $price < 0) $price_err = "Please enter a valid price.";
    // >> नया: कैटेगरी वैलिडेशन
    if (empty($selected_categories)) $category_err = "Please select at least one category.";
    
    // ... (आपका मौजूदा फाइल अपलोडिंग और वैलिडेशन का कोड यहाँ आएगा) ...
    // ... (कोई बदलाव नहीं) ...
    $preview_image_path = null;
    $product_file_path = null;
    
    // ... (इमेज हैंडलिंग) ...
    // ... (प्रोडक्ट फाइल हैंडलिंग) ...

    // 3. अगर सब कुछ सही है, तो डेटाबेस में सेव करें
    if (empty($name_err) && empty($description_err) && empty($price_err) && empty($image_err) && empty($file_err) && empty($category_err)) {
        try {
            // पहले प्रोडक्ट बनाएं
            $product = Product::create([
                'user_id' => $user_id,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'preview_image' => $preview_image_path,
                'file_path' => $product_file_path,
            ]);

            // >> नया: प्रोडक्ट को चुनी हुई कैटेगरी से जोड़ें <<
            if ($product && !empty($selected_categories)) {
                $product->categories()->sync($selected_categories); // 'sync' मेथड पिवट टेबल को अपडेट करता है
            }

            $success_msg = "Your product has been listed successfully!";

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $file_err = "A database error occurred. Could not save the product.";
        }
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="form-wrapper">
            <h1>Sell Your Software</h1>
            <p>List your script, plugin, or theme on our marketplace.</p>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php echo $success_msg; ?>
                    <p><a href="<?php echo BASE_URL; ?>software_marketplace.php">View Marketplace</a></p>
                </div>
            <?php else: ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    
                    <!-- (नाम, विवरण, और कीमत के फील्ड्स जैसे थे वैसे ही रहेंगे) -->
                    
                    <div class="form-group form-group-vertical">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="e.g., Ultimate Blogging Plugin" required>
                        <span class="error"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-group form-group-vertical">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="8" placeholder="..." required></textarea>
                        <span class="error"><?php echo $description_err; ?></span>
                    </div>
                    <div class="form-group form-group-vertical">
                        <label for="price">Price (in INR)</label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" placeholder="e.g., 499.00" required>
                        <span class="error"><?php echo $price_err; ?></span>
                    </div>
                    
                    <!-- ======================================================== -->
                    <!-- >> यहाँ नया कैटेगरी सेक्शन जोड़ा गया है << -->
                    <div class="form-group form-group-vertical">
                        <label>Categories</label>
                        <div class="checkbox-group">
                            <?php if ($categories->isEmpty()): ?>
                                <p>No categories found. Please add them first.</p>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="category_<?php echo $category->id; ?>" name="categories[]" value="<?php echo $category->id; ?>">
                                        <label for="category_<?php echo $category->id; ?>"><?php echo htmlspecialchars($category->name); ?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <span class="error"><?php echo $category_err; ?></span>
                    </div>
                    <!-- ======================================================== -->

                    <!-- (प्रीव्यू इमेज और प्रोडक्ट फाइल के फील्ड्स जैसे थे वैसे ही रहेंगे) -->
                    <div class="form-group form-group-vertical">
                        <label for="preview_image">Preview Image</label>
                        <input type="file" id="preview_image" name="preview_image" class="form-control" accept="image/jpeg, image/png, image/gif">
                        <span class="error"><?php echo $image_err; ?></span>
                    </div>
                    <div class="form-group form-group-vertical">
                        <label for="product_file">Product File (.zip only)</label>
                        <input type="file" id="product_file" name="product_file" class="form-control" accept=".zip" required>
                        <span class="error"><?php echo $file_err; ?></span>
                    </div>
                    
                    <div class="form-group full-width-btn">
                        <input type="submit" class="btn btn-primary btn-lg" value="List My Product">
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . '/includes/footer.php';
?>