<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('admin');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token($_POST['csrf_token']);
    
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    
    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ? AND role = 'recruiter'");
        mysqli_stmt_bind_param($stmt, "si", $action, $id);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: manage_recruiters.php");
exit();
?>
