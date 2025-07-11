<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Message;
use App\Models\Conversation;

// --- सुरक्षा और एक्सेस कंट्रोल ---

// 1. चेक करें कि रिक्वेस्ट POST मेथड से आई है
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

// 2. चेक करें कि यूजर लॉग-इन है
if (!isset($_SESSION["loggedin"])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'You must be logged in to send a message.']);
    exit;
}

// 3. फॉर्म से डेटा लें
$conversation_id = $_POST['conversation_id'] ?? null;
$receiver_id = $_POST['receiver_id'] ?? null;
$body = trim($_POST['body'] ?? '');
$sender_id = $_SESSION['id'];

// 4. डेटा को वैलिडेट करें
if (empty($conversation_id) || empty($receiver_id) || empty($body)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid data provided.']);
    exit;
}

// 5. सुरक्षा जांच: क्या यह यूजर इस बातचीत का हिस्सा है?
$conversation = Conversation::find($conversation_id);
if (!$conversation || ($conversation->user1_id != $sender_id && $conversation->user2_id != $sender_id)) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'You are not authorized to send messages in this conversation.']);
    exit;
}

// --- डेटाबेस में मैसेज सेव करना ---

try {
    // Eloquent ORM का उपयोग करके नया मैसेज बनाएं
    $message = Message::create([
        'conversation_id' => $conversation_id,
        'sender_id'       => $sender_id,
        'receiver_id'     => $receiver_id,
        'body'            => $body,
    ]);

    // सफलता पर, JSON रिस्पांस भेजें (AJAX के लिए)
    http_response_code(201); // Created
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully!',
        'data' => [
            'body' => htmlspecialchars($message->body),
            'timestamp' => $message->created_at->format('h:i A')
        ]
    ]);

} catch (\Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Something went wrong while sending the message.']);
}

exit;
?>