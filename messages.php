<?php
session_start();

// जरूरी फाइल्स और क्लासेस को include करें
require_once __DIR__ . '/bootstrap.php';
use App\Models\Conversation;
use App\Models\Message; // Message मॉडल को भी इम्पोर्ट करें
use App\Models\User;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन यूज़र्स ही इस पेज को देख सकते हैं
if (!isset($_SESSION["user_id"])) {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

// >> यहाँ सुधार है: $_SESSION['user_id'] का उपयोग करें <<
$current_user_id = $_SESSION['user_id'];

// लॉग-इन यूज़र की सभी बातचीत को लाएं, साथ में दूसरे यूजर की जानकारी भी पहले ही ले आएं (N+1 समस्या से बचने के लिए)
$conversations = Conversation::where('user1_id', $current_user_id)
                             ->orWhere('user2_id', $current_user_id)
                             ->with(['user1', 'user2']) // Eager loading
                             ->latest('updated_at')
                             ->get();

// --- किसी एक बातचीत के मैसेजेस को लोड करना ---
$messages = collect(); // एक खाली कलेक्शन बनाएं
$selected_conversation = null;
$other_user_in_chat = null; // दूसरे यूजर की जानकारी स्टोर करने के लिए

if (isset($_GET['conversation_id']) && ctype_digit($_GET['conversation_id'])) { // सुनिश्चित करें कि id एक नंबर है
    
    $conversation_id = (int)$_GET['conversation_id'];
    
    // findOrFail का उपयोग करें, यह न मिलने पर 404 एरर देगा
    // with() का उपयोग करके संबंधित मैसेजेस को पहले ही लोड कर लें
    $selected_conversation = Conversation::with('messages.sender')->findOrFail($conversation_id);
    
    // सुरक्षा जांच: क्या यह यूजर इस बातचीत का हिस्सा है?
    if ($selected_conversation && ($selected_conversation->user1_id == $current_user_id || $selected_conversation->user2_id == $current_user_id)) {
        
        $messages = $selected_conversation->messages;
        
        // दूसरे यूजर की जानकारी सेट करें
        $other_user_in_chat = ($selected_conversation->user1_id == $current_user_id)
                           ? $selected_conversation->user2
                           : $selected_conversation->user1;

    } else {
        // अगर यूजर बातचीत का हिस्सा नहीं है, तो उसे अनुमति नहीं है
        header("location: " . BASE_URL . "messages.php?error=unauthorized");
        exit;
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<main class="page-container">
    <div class="container">
        <div class="messages-layout">
            <!-- बाईं तरफ: बातचीत की लिस्ट -->
            <div class="conversations-list">
                <div class="list-header">
                    <h3>Your Conversations</h3>
                </div>
                <ul>
                    <?php if($conversations->isEmpty()): ?>
                        <li class="no-conversations">You have no conversations yet.</li>
                    <?php else: ?>
                        <?php foreach($conversations as $conv): ?>
                            <?php 
                                // Eager loading के कारण हमें अलग से User::find() करने की जरूरत नहीं है
                                $other_user = ($conv->user1_id == $current_user_id) ? $conv->user2 : $conv->user1;

                                if(!$other_user) continue; 
                                
                                $avatar_url = $other_user->avatar 
                                            ? BASE_URL . 'assets/uploads/avatars/' . $other_user->avatar 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($other_user->username) . '&size=48';
                                            
                                $is_active = ($selected_conversation && $selected_conversation->id == $conv->id) ? 'active' : '';
                            ?>
                            <a href="?conversation_id=<?php echo $conv->id; ?>" class="conversation-link">
                                <li class="conversation-item <?php echo $is_active; ?>">
                                    <img src="<?php echo $avatar_url; ?>" alt="<?php echo htmlspecialchars($other_user->username); ?>" class="avatar">
                                    <div class="conv-details">
                                        <span class="conv-username"><?php echo htmlspecialchars($other_user->username); ?></span>
                                        <span class="conv-preview text-muted">Last message preview...</span>
                                    </div>
                                </li>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- दाईं तरफ: चैट विंडो -->
            <div class="chat-window">
                <?php if ($selected_conversation && $other_user_in_chat): ?>
                    <div class="chat-header">
                        <h4>Chat with <?php echo htmlspecialchars($other_user_in_chat->username); ?></h4>
                    </div>
                    <div class="chat-messages" id="chat-messages-container">
                        <?php foreach ($messages as $message): ?>
                            <?php $is_sender = $message->sender_id == $current_user_id; ?>
                            <div class="message-bubble <?php echo $is_sender ? 'sent' : 'received'; ?>">
                                <p><?php echo nl2br(htmlspecialchars($message->body)); ?></p>
                                <small class="message-time"><?php echo $message->created_at->format('h:i A'); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="chat-input-form">
                        <form id="chat-form" method="post" action="send-message.php">
                            <input type="hidden" name="conversation_id" id="conversation_id" value="<?php echo $selected_conversation->id; ?>">
                            <input type="hidden" name="receiver_id" value="<?php echo $other_user_in_chat->id; ?>">
                            <textarea name="body" placeholder="Type your message..." rows="1" required></textarea>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="no-chat-selected">
                        <p>Select a conversation from the left to start chatting.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>