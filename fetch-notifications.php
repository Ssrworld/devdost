<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Notification;

if (!isset($_SESSION['loggedin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$notifications = Notification::where('user_id', $_SESSION['id'])
                             ->latest()
                             ->take(10) // सिर्फ लेटेस्ट 10 नोटिफिकेशन्स लाएं
                             ->get();

$response = [];
foreach ($notifications as $notification) {
    $response[] = [
        'id' => $notification->id,
        'message' => htmlspecialchars($notification->message),
        'link' => BASE_URL . $notification->link,
        'is_read' => $notification->is_read,
        'time_ago' => $notification->created_at->diffForHumans()
    ];
}

echo json_encode($response);
?>