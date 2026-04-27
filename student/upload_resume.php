<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('student');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token($_POST['csrf_token']);

    $user_id = $_SESSION['user_id'];
    
    if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    $file_name = $_FILES['resume']['name'];
    $file_tmp = $_FILES['resume']['tmp_name'];
    
    // Basic file validation
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        die("Only PDF files are allowed.");
    }

    // Sanitize filename
    $new_file_name = "resume_" . $user_id . "_" . time() . ".pdf";

    if (move_uploaded_file($file_tmp, "../resumes/" . $new_file_name)) {
        $stmt = mysqli_prepare($conn, "UPDATE students SET resume = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_file_name, $user_id);
        mysqli_stmt_execute($stmt);
        
        header("Location: ../student_dashboard.php?msg=resume_uploaded");
    } else {
        die("Failed to upload file.");
    }
} else {
    header("Location: ../student_dashboard.php");
}
exit();
?>