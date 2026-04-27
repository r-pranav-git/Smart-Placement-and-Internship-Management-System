<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

if(isset($_POST['send']))
{
    verify_csrf_token($_POST['csrf_token']);
    $student = (int)$_POST['student'];
    $message = trim($_POST['message']);

    $stmt = mysqli_prepare($conn, "INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $student, $message);
    
    if(mysqli_stmt_execute($stmt)) {
        $success = "Announcement sent to student successfully!";
    } else {
        $error = "Failed to send message.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message | Recruiter Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <link rel="stylesheet" href="recruiter_ui.css">
    <style>
        .form-card { max-width: 600px; padding: 35px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h2>Excellence <span>Portal</span></h2>
        <p>Hiring Partner Panel</p>
    </div>
    <div class="sidebar-nav">
        <a href="../recruiter_dashboard.php"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a>
        <a href="post_job.php"><i class="fa-solid fa-file-circle-plus"></i> <span>Post Opportunity</span></a>
        <a href="my_jobs.php"><i class="fa-solid fa-list-check"></i> <span>Manage Postings</span></a>
        <a href="view_applications.php"><i class="fa-solid fa-users-viewfinder"></i> <span>Review Applicants</span></a>
        <a href="send_message.php" class="active"><i class="fa-solid fa-envelope-open-text"></i> <span>Send Notifications</span></a>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/logout.php"><i class="fa-solid fa-power-off"></i> <span>Sign Out</span></a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4 animate-up">
        <h2 class="page-title mb-0">Direct Communication</h2>
        <p class="page-subtitle">Send interview invites or results to specific students.</p>
    </div>

    <?php 
    if(isset($success)) echo "<div class='alert alert-success animate-up'><i class='fa-solid fa-check-circle me-2'></i> ".e($success)."</div>"; 
    if(isset($error)) echo "<div class='alert alert-danger animate-up'><i class='fa-solid fa-exclamation-circle me-2'></i> ".e($error)."</div>"; 
    ?>

    <div class="form-card surface-card glass-card animate-up" style="animation-delay: 0.1s;">
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label fw-600">Student ID</label>
                <input type="number" name="student" class="form-control" placeholder="Enter student database ID" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-600">Your Message</label>
                <textarea name="message" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
            </div>
            <button type="submit" name="send" class="btn-premium w-100">
                <i class="fa-solid fa-paper-plane me-1"></i> Send Announcement
            </button>
        </form>
    </div>
</div>

</body>
</html>