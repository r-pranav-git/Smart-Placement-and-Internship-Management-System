<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

if(!isset($_GET['id'])){
    header("Location: view_applications.php");
    exit();
}

$app_id = (int)$_GET['id'];

if(isset($_POST['update']))
{
    verify_csrf_token($_POST['csrf_token']);
    $status = $_POST['status'];

    if(in_array($status, ['selected', 'rejected', 'shortlisted'])){
        $stmt = mysqli_prepare($conn, "UPDATE applications SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $app_id);
        mysqli_stmt_execute($stmt);
        // AFTER updating application status
if ($status == 'shortlisted') {

    // Step 1: Get student_id and job_id
    $stmt_get = mysqli_prepare($conn, "SELECT student_id, job_id FROM applications WHERE id = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $app_id);
    mysqli_stmt_execute($stmt_get);

    $result = mysqli_stmt_get_result($stmt_get);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $student_id = $row['student_id'];
        $job_id = $row['job_id'];

        // Step 2: Default interview details
        $date = date("Y-m-d H:i:s", strtotime("+2 days"));
        $location = "Online";

        // Step 3: Check if interview already exists
        $check_stmt = mysqli_prepare($conn, 
            "SELECT id FROM interviews WHERE student_id = ? AND job_id = ?"
        );
        mysqli_stmt_bind_param($check_stmt, "ii", $student_id, $job_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) == 0) {

            // Insert only if not exists
            $stmt_insert = mysqli_prepare($conn,
                "INSERT INTO interviews (student_id, job_id, date, location) VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param($stmt_insert, "iiss",
                $student_id,
                $job_id,
                $date,
                $location
            );

            mysqli_stmt_execute($stmt_insert);
        }

        mysqli_stmt_bind_param($stmt_insert, "iiss",
            $student_id,
            $job_id,
            $date,
            $location
        );

        mysqli_stmt_execute($stmt_insert);
    }
}
        $success = "Application status updated to ".ucfirst($status);
    }
}

// Fetch current app details
$stmt_fetch = mysqli_prepare($conn, "
    SELECT u.name, j.title, a.status 
    FROM applications a 
    JOIN users u ON a.student_id = u.id 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.id = ?
");
mysqli_stmt_bind_param($stmt_fetch, "i", $app_id);
mysqli_stmt_execute($stmt_fetch);
$app = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_fetch));

if(!$app) die("Application not found.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Result | Recruiter</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <link rel="stylesheet" href="recruiter_ui.css">
    <style>
        .update-card { max-width: 520px; width: 100%; padding: 40px; }
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
        <a href="view_applications.php" class="active"><i class="fa-solid fa-users-viewfinder"></i> <span>Review Applicants</span></a>
        <a href="send_message.php"><i class="fa-solid fa-envelope-open-text"></i> <span>Send Notifications</span></a>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/logout.php"><i class="fa-solid fa-power-off"></i> <span>Sign Out</span></a>
    </div>
</div>

<div class="main-content">
    <div class="page-header animate-up">
        <h2 class="page-title">Update Application Result</h2>
        <p class="page-subtitle"><?php echo e($app['name']); ?> for <?php echo e($app['title']); ?></p>
    </div>

    <div class="update-card surface-card glass-card animate-up">
        <div class="text-center mb-4">
            <div class="action-icon bg-blue-100 text-primary mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <h4 class="fw-800 mb-1">Update Application</h4>
            <p class="text-muted small"><?php echo e($app['name']); ?> • <?php echo e($app['title']); ?></p>
        </div>

        <?php if(isset($success)) echo "<div class='alert alert-success small animate-up'>$success</div>"; ?>

        <form method="POST">
            <?php csrf_field(); ?>
            <div class="mb-4">
                <label class="form-label fw-600 small">Select New Status</label>
                <select name="status" class="form-select form-select-lg" style="border-radius: 12px; font-size: 15px;">
                    <option value="selected" <?php if($app['status'] == 'selected') echo 'selected'; ?>>Selected</option>
                    <option value="rejected" <?php if($app['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
                    <option value="shortlisted" <?php if($app['status'] == 'shortlisted') echo 'selected'; ?>>Shortlisted</option>
                </select>
            </div>

            <button type="submit" name="update" class="btn-premium w-100 mb-3">Save Changes</button>
            <a href="view_applications.php" class="btn btn-light border w-100" style="border-radius: 12px; padding: 12px;">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>