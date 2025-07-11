document.addEventListener("DOMContentLoaded", function() {
    const chatForm = document.getElementById("chat-form");
    const chatMessages = document.querySelector(".chat-messages");
    const conversationIdInput = document.getElementById("conversation_id");

    if (!chatForm || !chatMessages || !conversationIdInput) {
        return; // अगर जरूरी एलिमेंट्स नहीं हैं तो कुछ न करें
    }

    const conversationId = conversationIdInput.value;

    // चैट विंडो को सबसे नीचे स्क्रॉल करें
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // फॉर्म सबमिट होने पर
    chatForm.addEventListener("submit", function(e) {
        e.preventDefault(); // फॉर्म को रीलोड होने से रोकें

        const formData = new FormData(chatForm);
        const messageBody = formData.get('body').trim();

        if (messageBody === "") {
            return; // खाली मैसेज न भेजें
        }

        fetch("send-message.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // नया भेजा हुआ मैसेज चैट में जोड़ें
                const newMessage = `
                    <div class="message-bubble sent">
                        <p>${data.data.body}</p>
                        <small>${data.data.timestamp}</small>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('afterbegin', newMessage);
                
                // इनपुट बॉक्स को खाली करें
                chatForm.querySelector("textarea").value = "";
                chatMessages.scrollTop = chatMessages.scrollHeight; // फिर से स्क्रॉल करें
            } else {
                alert(data.error || "Failed to send message.");
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // नए मैसेजेस के लिए हर 3 सेकंड में पोलिंग (चेक करना)
    setInterval(function() {
        fetch(`fetch-messages.php?conversation_id=${conversationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                     const newMessage = `
                        <div class="message-bubble received">
                            <p>${msg.body}</p>
                            <small>${msg.timestamp}</small>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('afterbegin', newMessage);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        })
        .catch(error => console.error("Error polling messages:", error));
    }, 3000); // हर 3000ms (3 सेकंड) में चेक करें
});