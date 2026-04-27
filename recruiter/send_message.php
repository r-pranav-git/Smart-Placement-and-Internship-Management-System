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
    <style>
        body { background-color: var(--background); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .sidebar { height: 100vh; background: var(--surface); border-right: 1px solid var(--border); padding-top: 20px; position: fixed; width: 260px; }
        .sidebar .brand { padding: 0 24px 24px; font-size: 20px; font-weight: 800; color: var(--secondary); border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .sidebar .brand span { color: var(--primary); }
        .nav-link { color: var(--text-light); padding: 12px 24px; font-weight: 500; margin: 4px 16px; border-radius: var(--radius-md); transition: 0.2s; }
        .nav-link:hover { color: var(--primary); background: #f1f5f9; }
        .nav-link.active { color: var(--primary); background: #eff6ff; font-weight: 600; }
        .main-content { margin-left: 260px; padding: 40px; }
        .form-card { max-width: 600px; padding: 35px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-graduation-cap"></i> Excellence <span>Portal</span>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="../recruiter_dashboard.php"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="post_job.php"><i class="fa-solid fa-briefcase me-2"></i> Post Job</a></li>
        <li class="nav-item"><a class="nav-link" href="my_jobs.php"><i class="fa-solid fa-list-check me-2"></i> My Jobs</a></li>
        <li class="nav-item"><a class="nav-link" href="view_applications.php"><i class="fa-solid fa-users me-2"></i> Applications</a></li>
        <li class="nav-item mt-4"><a class="nav-link text-danger" href="../logout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="mb-4 animate-up">
        <h2 class="fw-800">Direct Communication</h2>
        <p class="text-muted">Send interview invites or results to specific students.</p>
    </div>

    <?php 
    if(isset($success)) echo "<div class='alert alert-success animate-up'><i class='fa-solid fa-check-circle me-2'></i> ".e($success)."</div>"; 
    if(isset($error)) echo "<div class='alert alert-danger animate-up'><i class='fa-solid fa-exclamation-circle me-2'></i> ".e($error)."</div>"; 
    ?>

    <div class="form-card glass-card animate-up" style="animation-delay: 0.1s;">
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