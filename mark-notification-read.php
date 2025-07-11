<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Notification;

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['loggedin']) || !isset($_POST['id'])) {
    http_response_code(400);
    exit;
}

$notification = Notification::where('id', $_POST['id'])
                            ->where('user_id', $_SESSION['id'])
                            ->first();

if ($notification && !$notification->is_read) {
    $notification->is_read = true;
    $notification->save();
}

echo json_encode(['success' => true]);
?>