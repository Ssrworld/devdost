<?php
// (आपका मौजूदा कोड जैसे session_start(), db_connect.php आदि यहाँ रहेगा)
// ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (आपका मौजूदा कोड जैसे title, description, salary को लेना)
    // आप 'budget' की जगह 'salary' नाम का इस्तेमाल कर सकते हैं
    
    // >> यहाँ बदलाव है: हम post_type को 'job' हार्डकोड कर रहे हैं
    $post_type = 'job';
    // << बदलाव खत्म

    try {
        // >> ध्यान दें: हम उसी 'projects' टेबल का उपयोग कर रहे हैं
        $sql = "INSERT INTO projects (user_id, title, description, budget, skills_required, post_type) VALUES (:user_id, :title, :description, :budget, :skills, :post_type)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget); // आप इसे salary वेरिएबल से बदल सकते हैं
        $stmt->bindParam(':skills', $skills);
        $stmt->bindParam(':post_type', $post_type);

        $stmt->execute();
        
        header("Location: dashboard.php?message=Job posted successfully!");
        exit();
    } catch (PDOException $e) {
        // ... (आपका मौजूदा एरर हैंडलिंग)
    }
}

// नीचे आपका HTML फॉर्म आएगा। आप "Post a Project" की जगह "Post a Job"
// और "Budget" की जगह "Salary" जैसे लेबल बदल सकते हैं।
?>

<!-- यहाँ आपका HTML फॉर्म आएगा -->
<!-- उदाहरण: -->
<h1>Post a New Job</h1>
<form action="post_job.php" method="post">
    <!-- ... title, description, salary, skills के लिए इनपुट फील्ड्स ... -->
    <button type="submit">Post Job</button>
</form>