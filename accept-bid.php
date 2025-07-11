<?php
// सेशन शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Bid;
use App\Models\Project;
use App\Models\Notification; // नोटिफिकेशन मॉडल को भी इम्पोर्ट करें
use Illuminate\Database\Capsule\Manager as DB;

// --- सुरक्षा और एक्सेस कंट्रोल ---

// 1. चेक करें कि रिक्वेस्ट POST मेथड से आई है
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: " . BASE_URL);
    exit;
}

// 2. चेक करें कि यूजर लॉग-इन है और वह एक 'client' है
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== 'client') {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

// 3. फॉर्म से bid_id और project_id लें
if (!isset($_POST['bid_id']) || !isset($_POST['project_id'])) {
    die("Invalid request.");
}

$bid_id = $_POST['bid_id'];
$project_id = $_POST['project_id'];

// --- ट्रांजैक्शन के अंदर डेटाबेस ऑपरेशन ---
try {
    DB::transaction(function () use ($bid_id, $project_id) {
        // 1. बिड और प्रोजेक्ट की जानकारी लाएं
        $bid = Bid::findOrFail($bid_id);
        $project = Project::findOrFail($project_id);

        // 2. सुरक्षा जांच: क्या यह क्लाइंट इस प्रोजेक्ट का मालिक है?
        if ($project->user_id != $_SESSION['id']) {
            throw new Exception("You are not authorized to perform this action.");
        }
        
        // 3. सुरक्षा जांच: क्या यह प्रोजेक्ट अभी भी 'open' है?
        if ($project->status != 'open') {
            throw new Exception("This project is no longer open for bidding.");
        }

        // --- मुख्य एक्शन ---

        // 4. प्रोजेक्ट को अपडेट करें
        $project->status = 'in_progress';
        $project->developer_id = $bid->developer_id;
        $project->accepted_bid_id = $bid->id;
        $project->save();

        // 5. स्वीकार की गई बिड का स्टेटस अपडेट करें
        $bid->status = 'accepted';
        $bid->save();
        
        // ============== यहाँ से नया नोटिफिकेशन का लॉजिक ==============
        // स्वीकार किए गए डेवलपर को बधाई का नोटिफिकेशन भेजें
        Notification::create([
            'user_id' => $bid->developer_id,
            'message' => "Congratulations! Your bid on the project \"" . htmlspecialchars($project->title) . "\" has been accepted.",
            'link'    => "project-details.php?id=" . $project_id,
        ]);

        // 6. बाकी सभी बिड्स को 'rejected' के रूप में अपडेट करें और उन्हें भी नोटिफिकेशन भेजें
        $rejected_bids = Bid::where('project_id', $project_id)
                            ->where('id', '!=', $bid_id)
                            ->get();

        foreach($rejected_bids as $rejected_bid) {
            $rejected_bid->status = 'rejected';
            $rejected_bid->save();
            // रिजेक्ट किए गए डेवलपर्स को नोटिफिकेशन भेजें
            Notification::create([
                'user_id' => $rejected_bid->developer_id,
                'message' => "Unfortunately, your bid on the project \"" . htmlspecialchars($project->title) . "\" was not accepted this time.",
                'link'    => "project-details.php?id=" . $project_id,
            ]);
        }
        // ====================== लॉजिक खत्म ======================
    });

    // सफलता पर, यूजर को वापस प्रोजेक्ट पेज पर एक सक्सेस मैसेज के साथ भेजें
    header("location: " . BASE_URL . "project-details.php?id=" . $project_id . "&success=BidAccepted");
    exit;

} catch (\Exception $e) {
    // अगर कोई भी एरर आता है, तो ट्रांजैक्शन रोलबैक हो जाएगा
    // die($e->getMessage()); // डेवलपमेंट के लिए
    header("location: " . BASE_URL . "project-details.php?id=" . $project_id . "&error=AcceptFailed");
    exit;
}
?>