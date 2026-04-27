<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Excellence College</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <link rel="stylesheet" href="student_ui.css">
</head>
<body>

<?php $student_active = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg sticky-top navbar-premium">
    <div class="container">
        <a class="navbar-brand fw-800" href="../student_dashboard.php">
            <i class="fa-solid fa-graduation-cap text-primary me-2"></i> Excellence <span>Portal</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item"><a class="nav-link <?php echo $student_active === 'student_dashboard.php' ? 'active' : ''; ?>" href="../student_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $student_active === 'edit_profile.php' ? 'active' : ''; ?>" href="edit_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $student_active === 'my_applications.php' ? 'active' : ''; ?>" href="my_applications.php">Applications</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $student_active === 'interview_schedule.php' ? 'active' : ''; ?>" href="interview_schedule.php">Interviews</a></li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-danger btn-sm rounded-pill px-4" href="../auth/logout.php">
                        <i class="fa-solid fa-power-off me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container student-shell">