<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

if(!isset($_GET['job'])){
    header("Location: my_jobs.php");
    exit();
}

$job_id = (int)$_GET['job'];
$rid = $_SESSION['user_id'];

// Verify ownership
$job_stmt = mysqli_prepare($conn, "SELECT * FROM jobs WHERE id = ? AND recruiter_id = ?");
mysqli_stmt_bind_param($job_stmt, "ii", $job_id, $rid);
mysqli_stmt_execute($job_stmt);
$job_res = mysqli_stmt_get_result($job_stmt);
$job = mysqli_fetch_assoc($job_res);

if(!$job){
    header("Location: my_jobs.php");
    exit();
}

$sql = "SELECT s.*, u.name, u.email FROM students s 
        JOIN users u ON s.user_id = u.id
        WHERE s.cgpa >= ? 
        AND s.backlogs <= ? 
        AND s.department = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "dis", $job['min_cgpa'], $job['max_backlogs'], $job['department']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eligible Candidates | Recruiter</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <style>
        body { background-color: var(--background); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .sidebar { height: 100vh; background: var(--surface); border-right: 1px solid var(--border); padding-top: 20px; position: fixed; width: 260px; }
        .sidebar .brand { padding: 0 24px 24px; font-size: 20px; font-weight: 800; color: var(--secondary); border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .sidebar .brand span { color: var(--primary); }
        .nav-link { color: var(--text-light); padding: 12px 24px; font-weight: 500; margin: 4px 16px; border-radius: var(--radius-md); transition: 0.2; }
        .nav-link:hover { color: var(--primary); background: #f1f5f9; }
        .nav-link.active { color: var(--primary); background: #eff6ff; font-weight: 600; }
        .main-content { margin-left: 260px; padding: 40px; }
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
    <div class="mb-4 animate-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="my_jobs.php">My Jobs</a></li>
                <li class="breadcrumb-item active"><?php echo e($job['title']); ?></li>
            </ol>
        </nav>
        <h2 class="fw-800">Qualified Candidates</h2>
        <p class="text-muted">Students meeting eligibility for: <strong><?php echo e($job['title']); ?></strong></p>
    </div>

    <div class="animate-up" style="animation-delay: 0.1s;">
        <div class="table-responsive">
            <table class="table table-premium">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Academic Record</th>
                        <th>Contact info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo e($row['name']); ?></div>
                                    <div class="text-muted small"><?php echo e($row['department']); ?></div>
                                </td>
                                <td>
                                    <div><span class="text-muted small uppercase">CGPA</span> <strong><?php echo e($row['cgpa']); ?></strong></div>
                                    <div><span class="text-muted small uppercase">Backlogs</span> <strong><?php echo e($row['backlogs']); ?></strong></div>
                                </td>
                                <td><?php echo e($row['email']); ?></td>
                                <td>
                                    <a href="download_resume.php?id=<?php echo $row['user_id']; ?>" class="btn-premium btn-sm py-2 px-3">
                                        <i class="fa-solid fa-file-pdf me-1"></i> Resume
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">No students currently match these criteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>