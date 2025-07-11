<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Bid;
use App\Models\Project; // प्रोजेक्ट मॉडल को भी इम्पोर्ट करें
use App\Models\Notification; // नोटिफिकेशन मॉडल को भी इम्पोर्ट करें

// --- सुरक्षा और एक्सेस कंट्रोल ---

// 1. चेक करें कि रिक्वेस्ट POST मेथड से आई है या नहीं
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: " . BASE_URL); // अगर नहीं, तो होमपेज पर भेजें
    exit;
}

// 2. चेक करें कि यूजर लॉग-इन है और वह एक 'developer' है
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== 'developer') {
    // अगर नहीं, तो उसे लॉगिन पेज पर भेजें
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

// --- फॉर्म डेटा को प्रोसेस करना ---

$project_id = $_POST['project_id'];
$developer_id = $_SESSION['id'];
$bid_amount = $_POST['bid_amount'];
$delivery_time = $_POST['delivery_time'];

// बेसिक वैलिडेशन
if (empty($project_id) || empty($bid_amount) || empty($delivery_time) || !is_numeric($bid_amount) || !is_numeric($delivery_time)) {
    // अगर डेटा गलत है, तो यूजर को वापस उसी प्रोजेक्ट पेज पर एक एरर मैसेज के साथ भेजें
    header("location: " . BASE_URL . "project-details.php?id=" . $project_id . "&error=InvalidData");
    exit;
}

// --- डेटाबेस में सेव करना ---

try {
    // चेक करें कि इस डेवलपर ने इस प्रोजेक्ट पर पहले से बिड तो नहीं लगाई है
    $existingBid = Bid::where('project_id', $project_id)
                      ->where('developer_id', $developer_id)
                      ->exists();

    if ($existingBid) {
        // अगर पहले से बिड है, तो एरर के साथ वापस भेजें
        header("location: " . BASE_URL . "project-details.php?id=" . $project_id . "&error=AlreadyBid");
        exit;
    }

    // Eloquent ORM का उपयोग करके नई बिड बनाएं
    $bid = Bid::create([
        'project_id'     => $project_id,
        'developer_id'   => $developer_id,
        'bid_amount'     => $bid_amount,
        'delivery_time'  => $delivery_time,
    ]);

    // ============== यहाँ से नया नोटिफिकेशन का लॉजिक ==============
    $project = Project::find($project_id);
    if ($project) {
        Notification::create([
            'user_id' => $project->user_id, // क्लाइंट को नोटिफिकेशन भेजें
            'message' => "You have a new bid from " . htmlspecialchars($_SESSION['username']) . " on your project: \"" . htmlspecialchars($project->title) . "\"",
            'link'    => "project-details.php?id=" . $project_id,
        ]);
    }
    // ====================== लॉजिक खत्म ======================

    // सफलता पर, यूजर को वापस प्रोजेक्ट पेज पर एक सक्सेस मैसेज के साथ भेजें
    header("location: " . BASE_URL . "project-details.php?id=" . $project_id . "&success=BidPlaced");
    exit;

} catch (\Exception $e) {
    // डेटाबेस एरर को हैंडल करें
    // die("Oops! Something went wrong. " . $e->getMessage()); // डेवलपमेंट के लिए
    header("location: " . BASE_URL . "project-details.php?id=" . $project_id . "&error=ServerError");
    exit;
}
?>