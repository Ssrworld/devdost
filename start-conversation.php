<?php
// start-conversation.php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Conversation;

if (!isset($_SESSION['loggedin']) || !isset($_GET['user_id'])) {
    header("location: " . BASE_URL);
    exit;
}

$user1_id = $_SESSION['id'];
$user2_id = $_GET['user_id'];

// चेक करें कि बातचीत पहले से मौजूद है या नहीं
$conversation = Conversation::where(function ($query) use ($user1_id, $user2_id) {
    $query->where('user1_id', $user1_id)->where('user2_id', $user2_id);
})->orWhere(function ($query) use ($user1_id, $user2_id) {
    $query->where('user1_id', $user2_id)->where('user2_id', $user1_id);
})->first();

if (!$conversation) {
    // अगर नहीं, तो नई बातचीत बनाएं
    $conversation = Conversation::create([
        'user1_id' => $user1_id,
        'user2_id' => $user2_id,
    ]);
}

// यूजर को मैसेजिंग पेज पर उस बातचीत की ID के साथ भेजें
header("location: " . BASE_URL . "messages.php?conversation_id=" . $conversation->id);
exit;
?>