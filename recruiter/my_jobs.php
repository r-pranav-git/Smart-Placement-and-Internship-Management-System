<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

$rid = $_SESSION['user_id'];

$sql = "SELECT * FROM jobs WHERE recruiter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $rid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Job Postings | Recruiter Dashboard</title>
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
        .job-card { margin-bottom: 20px; padding: 25px; display: flex; justify-content: space-between; align-items: center; }
        .job-info h5 { margin-bottom: 5px; font-weight: 700; color: var(--secondary); }
        .job-meta { font-size: 13px; color: var(--text-light); display: flex; gap: 15px; }
        .job-meta span i { margin-right: 5px; }
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
        <li class="nav-item"><a class="nav-link active" href="my_jobs.php"><i class="fa-solid fa-list-check me-2"></i> My Jobs</a></li>
        <li class="nav-item"><a class="nav-link" href="view_applications.php"><i class="fa-solid fa-users me-2"></i> Applications</a></li>
        <li class="nav-item mt-4"><a class="nav-link text-danger" href="../logout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 animate-up">
        <div>
            <h2 class="fw-800">My Job Posts</h2>
            <p class="text-muted">Manage your active recruitment drives.</p>
        </div>
        <a href="post_job.php" class="btn-premium"><i class="fa-solid fa-plus me-2"></i> Post New Job</a>
    </div>

    <div class="animate-up" style="animation-delay: 0.1s;">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="job-card glass-card">
                    <div class="job-info">
                        <h5><?php echo e($row['title']); ?></h5>
                        <div class="job-meta">
                            <span><i class="fa-solid fa-building"></i> <?php echo e(ucfirst($row['type'])); ?></span>
                            <span><i class="fa-solid fa-calendar-alt"></i> Deadline: <?php echo e($row['deadline']); ?></span>
                            <span><i class="fa-solid fa-location-dot"></i> <?php echo e($row['location'] ?? 'Not Specified'); ?></span>
                        </div>
                    </div>
                    <div>
                        <a href="eligible_candidates.php?job=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                            Eligible Students <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5 glass-card">
                <i class="fa-solid fa-folder-open fs-1 text-muted mb-3"></i>
                <p class="text-muted">You haven't posted any jobs yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>