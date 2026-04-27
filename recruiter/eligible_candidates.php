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
    <link rel="stylesheet" href="recruiter_ui.css">
    <style>
        .table-wrap { overflow: hidden; }
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
        <a href="my_jobs.php" class="active"><i class="fa-solid fa-list-check"></i> <span>Manage Postings</span></a>
        <a href="view_applications.php"><i class="fa-solid fa-users-viewfinder"></i> <span>Review Applicants</span></a>
        <a href="send_message.php"><i class="fa-solid fa-envelope-open-text"></i> <span>Send Notifications</span></a>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/logout.php"><i class="fa-solid fa-power-off"></i> <span>Sign Out</span></a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4 animate-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="my_jobs.php">My Jobs</a></li>
                <li class="breadcrumb-item active"><?php echo e($job['title']); ?></li>
            </ol>
        </nav>
        <h2 class="page-title mb-0">Qualified Candidates</h2>
        <p class="page-subtitle">Students meeting eligibility for: <strong><?php echo e($job['title']); ?></strong></p>
    </div>

    <div class="animate-up" style="animation-delay: 0.1s;">
        <div class="table-responsive surface-card table-wrap">
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