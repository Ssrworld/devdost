<?php
// सबसे पहले config.php को include करें ताकि BASE_URL मिल सके
// क्योंकि हम इसे header() फंक्शन में इस्तेमाल करेंगे
require_once __DIR__ . '/config.php';

// सेशन को शुरू करें
session_start();

// सभी सेशन वेरिएबल्स को unset करें
$_SESSION = array();

// सेशन को नष्ट कर दें
session_destroy();

// यूजर को होमपेज (BASE_URL) पर रीडायरेक्ट करें
header("location: " . BASE_URL);

// रीडायरेक्ट के बाद स्क्रिप्ट को तुरंत रोक दें
exit;
?>