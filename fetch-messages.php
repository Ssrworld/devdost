<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Message;
use Carbon\Carbon;

if (!isset($_SESSION['loggedin']) || !isset($_GET['conversation_id'])) {
    http_response_code(403);
    exit;
}

$conversation_id = $_GET['conversation_id'];
$current_user_id = $_SESSION['id'];

// सिर्फ वे नए मैसेज लाएं जो पढ़े नहीं गए हैं
$new_messages = Message::where('conversation_id', $conversation_id)
                       ->where('receiver_id', $current_user_id)
                       ->where('is_read', false)
                       ->get();

$response_messages = [];
if (!$new_messages->isEmpty()) {
    foreach($new_messages as $message) {
        $response_messages[] = [
            'body' => htmlspecialchars($message->body),
            'timestamp' => Carbon::parse($message->created_at)->format('h:i A')
        ];
        // मैसेज को 'read' मार्क कर दें
        $message->is_read = true;
        $message->save();
    }
}

echo json_encode(['success' => true, 'messages' => $response_messages]);
exit;
?>