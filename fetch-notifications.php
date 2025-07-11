<?php
// PHP की एरर्स को सीधे आउटपुट में दिखाने से रोकें
// यह JSON को खराब होने से बचाता है
error_reporting(0);
ini_set('display_errors', 0);

// आउटपुट का प्रकार हमेशा JSON सेट करें
header('Content-Type: application/json');

// सेशन हमेशा टॉप पर शुरू करें
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Notification;

// --- सुरक्षा जांच ---
// अगर यूजर लॉग-इन नहीं है, तो एक उचित JSON एरर भेजें और बाहर निकल जाएं
if (!isset($_SESSION["user_id"])) { // <-- 'user_id' से जांच करें
    http_response_code(401); // Unauthorized स्टेटस कोड
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

// एक डिफ़ॉल्ट रिस्पांस स्ट्रक्चर तैयार करें
$response = [
    'success' => false,
    'notifications' => [],
    'error' => null
];

try {
    // >> यहाँ मुख्य सुधार है: $_SESSION['user_id'] का उपयोग करें <<
    $current_user_id = $_SESSION['user_id'];

    // यूजर की नोटिफिकेशन्स लाएं
    $notifications = Notification::where('user_id', $current_user_id)
                                 ->latest()
                                 ->take(10) // सिर्फ लेटेस्ट 10 नोटिफिकेशन्स लाएं
                                 ->get();

    // रिस्पांस को तैयार करें
    $formatted_notifications = [];
    foreach ($notifications as $notification) {
        $formatted_notifications[] = [
            'id' => $notification->id,
            'message' => htmlspecialchars($notification->message),
            'link' => $notification->link ? BASE_URL . $notification->link : '#', // अगर लिंक null है तो '#' का उपयोग करें
            'is_read' => (bool)$notification->is_read, // इसे हमेशा बूलियन के रूप में भेजें
            'time_ago' => $notification->created_at->diffForHumans()
        ];
    }
    
    // अंतिम रिस्पांस को अपडेट करें
    $response['success'] = true;
    $response['notifications'] = $formatted_notifications;

} catch (\Exception $e) {
    // अगर डेटाबेस या किसी और चीज़ में कोई समस्या आती है
    // आप इस एरर को लॉग कर सकते हैं: error_log($e->getMessage());
    http_response_code(500); // Internal Server Error
    $response['error'] = 'A server error occurred while fetching notifications.';
}

// अंतिम JSON रिस्पांस भेजें
echo json_encode($response);
exit();