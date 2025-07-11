<?php
session_start();
require_once __DIR__ . '/bootstrap.php';
use App\Models\Project;

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['project_id'])) {
    header("location: " . BASE_URL);
    exit;
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["user_type"] !== 'client') {
    header("location: " . BASE_URL . "pages/login.php");
    exit;
}

try {
    $project = Project::findOrFail($_POST['project_id']);

    if ($project->user_id != $_SESSION['id']) {
        throw new Exception("Authorization failed.");
    }

    if ($project->status == 'in_progress') {
        $project->status = 'completed';
        $project->save();
        header("location: " . BASE_URL . "project-details.php?id=" . $project->id . "&success=ProjectCompleted");
        exit;
    } else {
         throw new Exception("Project is not in a state to be completed.");
    }

} catch (\Exception $e) {
    header("location: " . BASE_URL . "project-details.php?id=" . $_POST['project_id'] . "&error=CompletionFailed");
    exit;
}
?>