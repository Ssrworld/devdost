document.addEventListener("DOMContentLoaded", function() {
    const bell = document.getElementById("notification-bell");
    const dropdown = document.querySelector(".notification-dropdown .dropdown-content");
    const notificationList = document.getElementById("notification-list");
    const notificationCount = document.querySelector(".notification-count");

    if (!bell) return;

    bell.addEventListener("click", function(e) {
        e.preventDefault();
        
        // अगर ड्रॉपडाउन खुला है तो बंद करें, अगर बंद है तो खोलें और डेटा लाएं
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        } else {
            fetchNotifications();
            dropdown.style.display = "block";
        }
    });

    function fetchNotifications() {
        notificationList.innerHTML = "<li>Loading...</li>";
        
        fetch("fetch-notifications.php")
            .then(response => response.json())
            .then(data => {
                notificationList.innerHTML = ""; // पुरानी लिस्ट को खाली करें
                if (data.length === 0) {
                    notificationList.innerHTML = "<li>No notifications yet.</li>";
                } else {
                    data.forEach(notif => {
                        const li = document.createElement("li");
                        if (!notif.is_read) {
                            li.classList.add("unread");
                        }
                        li.innerHTML = `<a href="${notif.link}" data-id="${notif.id}">${notif.message}<br><small>${notif.time_ago}</small></a>`;
                        notificationList.appendChild(li);
                    });
                }
            })
            .catch(error => console.error("Error fetching notifications:", error));
    }

    // नोटिफिकेशन पर क्लिक होने पर उसे 'read' मार्क करें
    notificationList.addEventListener("click", function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            const link = e.target.closest('a');
            const notificationId = link.dataset.id;
            
            // सर्वर को बताएं कि यह नोटिफिकेशन पढ़ ली गई है
            const formData = new FormData();
            formData.append('id', notificationId);

            fetch("mark-notification-read.php", {
                method: "POST",
                body: formData
            }).then(() => {
                // UI में से 'unread' क्लास हटा दें
                link.parentElement.classList.remove("unread");
                
                // काउंटर को अपडेट करें (सरल तरीका)
                if (notificationCount) {
                    let count = parseInt(notificationCount.textContent);
                    if (count > 1) {
                        notificationCount.textContent = count - 1;
                    } else {
                        notificationCount.style.display = "none";
                    }
                }
            });
            
            // लिंक पर जाने से पहले थोड़ा रुकें ताकि रिक्वेस्ट जा सके
            // e.preventDefault();
            // setTimeout(() => { window.location.href = link.href; }, 100);
        }
    });
});