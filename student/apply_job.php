<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('student');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token($_POST['csrf_token']);

    if(isset($_POST['job_id'])){
        $student_id = $_SESSION['user_id'];
        $job_id = (int)$_POST['job_id'];

        // Prevent duplicate applications
        $check = mysqli_prepare($conn, "SELECT id FROM applications WHERE student_id = ? AND job_id = ?");
        mysqli_stmt_bind_param($check, "ii", $student_id, $job_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if(mysqli_stmt_num_rows($check) > 0){
            // Already applied
            header("Location: ../student_dashboard.php?error=already_applied");
            exit();
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO applications (student_id, job_id, status) VALUES (?, ?, 'applied')");
        mysqli_stmt_bind_param($stmt, "ii", $student_id, $job_id);
        mysqli_stmt_execute($stmt);

        header("Location: ../student_dashboard.php?msg=applied");
        exit();
    }
}

header("Location: ../student_dashboard.php");
exit();
?>
