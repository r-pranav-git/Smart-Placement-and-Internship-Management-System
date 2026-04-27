<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Excellence College</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <link rel="stylesheet" href="admin_ui.css">
</head>
<body>

<?php
$active = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg sticky-top navbar-premium">
    <div class="container">
        <a class="navbar-brand fw-800" href="../admin_dashboard.php">
            <i class="fa-solid fa-shield-halved text-primary me-2"></i> Excellence <span>Admin</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link <?php echo $active === 'admin_dashboard.php' ? 'active' : ''; ?>" href="../admin_dashboard.php">
                        <i class="fa-solid fa-chart-pie me-1"></i> Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active === 'manage_users.php' ? 'active' : ''; ?>" href="manage_users.php">
                        <i class="fa-solid fa-users me-1"></i> Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active === 'manage_recruiters.php' || $active === 'add_recruiter.php' ? 'active' : ''; ?>" href="manage_recruiters.php">
                        <i class="fa-solid fa-building me-1"></i> Recruiters
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active === 'view_jobs.php' ? 'active' : ''; ?>" href="view_jobs.php">
                        <i class="fa-solid fa-briefcase me-1"></i> Drives
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active === 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                        <i class="fa-solid fa-chart-line me-1"></i> Reports
                    </a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-danger btn-sm rounded-pill px-4" href="../auth/logout.php">
                        <i class="fa-solid fa-power-off me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container admin-shell">