<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Conversation;
use App\Models\User;

// एक्सेस कंट्रोल: सिर्फ लॉग-इन यूज़र्स ही इस पेज को देख सकते हैं
if (!isset($_SESSION["loggedin"])) {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}
$current_user_id = $_SESSION['id'];

// लॉग-इन यूज़र की सभी बातचीत को लाएं
$conversations = Conversation::where('user1_id', $current_user_id)
                             ->orWhere('user2_id', $current_user_id)
                             ->latest('updated_at') // सबसे हाल की बातचीत सबसे ऊपर
                             ->get();

// --- किसी एक बातचीत के मैसेजेस को लोड करना ---
$messages = [];
$selected_conversation = null;
if (isset($_GET['conversation_id'])) {
    $conversation_id = $_GET['conversation_id'];
    $selected_conversation = Conversation::find($conversation_id);
    
    // सुरक्षा जांच: क्या यह यूजर इस बातचीत का हिस्सा है?
    if ($selected_conversation && ($selected_conversation->user1_id == $current_user_id || $selected_conversation->user2_id == $current_user_id)) {
        $messages = $selected_conversation->messages()->latest()->get();
    } else {
        // अगर नहीं, तो उसे मैसेज पेज पर बिना किसी सलेक्टेड चैट के भेज दें
        header("location: " . BASE_URL . "messages.php");
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
                                $other_user_id = ($conv->user1_id == $current_user_id) ? $conv->user2_id : $conv->user1_id;
                                $other_user = User::find($other_user_id);
                                if(!$other_user) continue; // अगर यूजर डिलीट हो गया है तो स्किप करें
                                $avatar_url = $other_user->avatar ? 
                                            BASE_URL . 'assets/uploads/avatars/' . $other_user->avatar : 
                                            'https://ui-avatars.com/api/?name=' . urlencode($other_user->username) . '&size=48';
                                $is_active = ($selected_conversation && $selected_conversation->id == $conv->id) ? 'active' : '';
                            ?>
                            <a href="?conversation_id=<?php echo $conv->id; ?>">
                                <li class="conversation-item <?php echo $is_active; ?>">
                                    <img src="<?php echo $avatar_url; ?>" alt="<?php echo htmlspecialchars($other_user->username); ?>" class="avatar">
                                    <div class="conv-details">
                                        <span class="conv-username"><?php echo htmlspecialchars($other_user->username); ?></span>
                                        <span class="conv-preview">Last message will be here...</span>
                                    </div>
                                </li>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- दाईं तरफ: चैट विंडो -->
            <div class="chat-window">
                <?php if ($selected_conversation): ?>
                    <?php 
                        $other_user_id = ($selected_conversation->user1_id == $current_user_id) ? $selected_conversation->user2_id : $selected_conversation->user1_id;
                        $other_user = User::find($other_user_id);
                    ?>
                    <div class="chat-header">
                        <h4>Chat with <?php echo htmlspecialchars($other_user->username); ?></h4>
                    </div>
                    <div class="chat-messages">
                        <?php foreach (array_reverse($messages->all()) as $message): ?>
                            <?php $is_sender = $message->sender_id == $current_user_id; ?>
                            <div class="message-bubble <?php echo $is_sender ? 'sent' : 'received'; ?>">
                                <p><?php echo nl2br(htmlspecialchars($message->body)); ?></p>
                                <small><?php echo $message->created_at->format('h:i A'); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="chat-input-form">

                        <!-- ================ यह फॉर्म अपडेट किया गया है ================ -->
                        <form id="chat-form">
                            <input type="hidden" name="conversation_id" id="conversation_id" value="<?php echo $selected_conversation->id; ?>">
                            <input type="hidden" name="receiver_id" value="<?php echo $other_user_id; ?>">
                            <textarea name="body" placeholder="Type your message..." rows="1"></textarea>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </form>
                        <!-- ========================================================= -->

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