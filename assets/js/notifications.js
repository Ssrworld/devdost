document.addEventListener("DOMContentLoaded", function() {
    const bell = document.getElementById("notification-bell");
    const dropdown = document.querySelector(".notification-dropdown .dropdown-content");
    const notificationList = document.getElementById("notification-list");
    const notificationCountSpan = document.querySelector(".notification-count"); // इसे 'span' कहना बेहतर है

    if (!bell || !dropdown || !notificationList) {
        console.error("Notification elements not found!");
        return;
    }

    // --- फंक्शन: सर्वर से नोटिफिकेशन्स लाना ---
    function fetchNotifications() {
        notificationList.innerHTML = "<li>Loading...</li>";
        
        fetch(BASE_URL + "fetch-notifications.php") // BASE_URL का उपयोग करें
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // >> यहाँ मुख्य सुधार है <<
                // अब हम सर्वर से मिले संरचित ऑब्जेक्ट को प्रोसेस करेंगे
                if (data.success && data.notifications) {
                    notificationList.innerHTML = ""; // पुरानी लिस्ट को खाली करें
                    const notifications = data.notifications; // ऐरे को निकालें
                    let unreadCount = 0;

                    if (notifications.length === 0) {
                        notificationList.innerHTML = "<li>No notifications yet.</li>";
                    } else {
                        // अब `notifications` (जो एक ऐरे है) पर लूप चलाएं
                        notifications.forEach(notif => {
                            const li = document.createElement("li");
                            li.classList.add("notification-item"); // एक सामान्य क्लास दें
                            
                            if (!notif.is_read) {
                                li.classList.add("unread");
                                unreadCount++; // अनपढ़ी नोटिफिकेशन्स की गिनती करें
                            }
                            
                            // लिंक को 'data-id' के साथ बनाएं
                            li.innerHTML = `<a href="${notif.link}" data-id="${notif.id}">
                                                <p class="message">${notif.message}</p>
                                                <small class="time">${notif.time_ago}</small>
                                            </a>`;
                            notificationList.appendChild(li);
                        });
                    }

                    // घंटी के ऊपर गिनती को अपडेट करें
                    updateNotificationCount(unreadCount);

                } else {
                    throw new Error(data.error || "Invalid data structure from server.");
                }
            })
            .catch(error => {
                console.error("Error fetching notifications:", error);
                notificationList.innerHTML = "<li>Could not load notifications.</li>";
            });
    }

    // --- फंक्शन: नोटिफिकेशन की गिनती को अपडेट करना ---
    function updateNotificationCount(count) {
        if (notificationCountSpan) {
            if (count > 0) {
                notificationCountSpan.textContent = count;
                notificationCountSpan.style.display = 'inline-block';
            } else {
                notificationCountSpan.style.display = 'none';
            }
        }
    }

    // --- इवेंट लिस्टनर: घंटी पर क्लिक ---
    bell.addEventListener("click", function(e) {
        e.preventDefault();
        
        const isVisible = dropdown.style.display === "block";
        dropdown.style.display = isVisible ? "none" : "block";

        if (!isVisible) {
            fetchNotifications(); // जब ड्रॉपडाउन खुले, तभी डेटा लाएं
        }
    });

    // --- इवेंट लिस्टनर: नोटिफिकेशन आइटम पर क्लिक ---
    notificationList.addEventListener("click", function(e) {
        const link = e.target.closest('a');
        if (link && link.parentElement.classList.contains('unread')) {
            const notificationId = link.dataset.id;
            
            const formData = new FormData();
            formData.append('id', notificationId);

            // सर्वर को बताएं कि यह नोटिफिकेशन पढ़ ली गई है
            fetch(BASE_URL + "mark-notification-read.php", {
                method: "POST",
                body: formData
            }).catch(error => console.error("Error marking notification as read:", error));
            
            // तुरंत UI अपडेट करें
            link.parentElement.classList.remove("unread");
            if (notificationCountSpan) {
                let currentCount = parseInt(notificationCountSpan.textContent) || 0;
                updateNotificationCount(currentCount - 1);
            }
        }
    });

    // पेज लोड होने पर और हर 30 सेकंड में ऑटो-रिफ्रेश करें
    fetchNotifications();
    setInterval(fetchNotifications, 30000); // हर 30 सेकंड में नई नोटिफिकेशन्स की जांच करें
});