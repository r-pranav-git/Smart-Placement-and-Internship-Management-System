<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

$rid = $_SESSION['user_id'];

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id'], $_POST['status'])) {

    verify_csrf_token($_POST['csrf_token']);

    $app_id = (int)$_POST['app_id'];
    $status = $_POST['status'];

    if (in_array($status, ['shortlisted', 'selected', 'rejected'])) {

        // Update Application Status
        $update_stmt = mysqli_prepare($conn, "UPDATE applications SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "si", $status, $app_id);

        if (mysqli_stmt_execute($update_stmt)) {

            // Get student + job details safely
            $notif_stmt2 = mysqli_prepare($conn, "
                SELECT a.student_id, j.title, r.company_name 
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN recruiters r ON j.recruiter_id = r.user_id
                WHERE a.id = ?
            ");

            mysqli_stmt_bind_param($notif_stmt2, "i", $app_id);
            mysqli_stmt_execute($notif_stmt2);
            $notif_result = mysqli_stmt_get_result($notif_stmt2);

            if ($row = mysqli_fetch_assoc($notif_result)) {
                $student_id = $row['student_id'];
                $job_title = $row['title'];
                $company = $row['company_name'];

                $message = "Your application for $job_title at $company has been marked as $status.";

                // Insert Notification
                $notif_stmt = mysqli_prepare($conn, "INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                mysqli_stmt_bind_param($notif_stmt, "is", $student_id, $message);
                mysqli_stmt_execute($notif_stmt);
            }

            $success = "Application status updated successfully!";
        } else {
            $error = "Failed to update status.";
        }
    }
}

/* Get applications for recruiter */
$sql = "
SELECT 
    applications.id as app_id,
    applications.student_id,
    applications.job_id,
    applications.status,
    users.name,
    users.email,
    jobs.title,
    students.cgpa,
    students.backlogs,
    students.resume
FROM applications
JOIN users ON applications.student_id = users.id
JOIN jobs ON applications.job_id = jobs.id
JOIN students ON students.user_id = users.id
WHERE jobs.recruiter_id = ?
ORDER BY applications.id DESC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $rid);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Result Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Applications | Recruiter Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <link rel="stylesheet" href="recruiter_ui.css">
    <style>
        .status-badge.rejected { background: #fee2e2; color: #b91c1c; }

        .skill-badge {
            font-size: 10px;
            padding: 2px 6px;
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 4px;
            color: var(--text-light);
        }

        .action-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .action-select {
            font-size: 13px;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid var(--border);
            outline: none;
            cursor: pointer;
        }
        
        .btn-update {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }
        
        .btn-update:hover {
            background: var(--primary-dark);
        }

        .table-wrap {
            padding: 0;
            overflow: hidden;
        }

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
    
    <div class="page-header d-flex justify-content-between align-items-center animate-up">
        <div>
            <h2 class="page-title">Candidate Pipeline</h2>
            <p class="page-subtitle mb-0">Review and manage student applications for your postings.</p>
        </div>
    </div>

    <?php 
    if(isset($success)) echo "<div class='alert alert-success py-2'><i class='fa-solid fa-circle-check me-2'></i> ".e($success)."</div>"; 
    if(isset($error)) echo "<div class='alert alert-danger py-2'><i class='fa-solid fa-circle-exclamation me-2'></i> ".e($error)."</div>"; 
    ?>

    <div class="dashboard-card surface-card table-wrap glass-card animate-up" style="animation-delay: 0.1s;">
        <div class="table-responsive">
            <table class="table table-premium">
                <thead>
                    <tr>
                        <th>Candidate Profile</th>
                        <th>Job Applied For</th>
                        <th>Metrics</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $statusClass = strtolower($row['status']);
                ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
                        </td>
                        
                        <td class="fw-bold text-secondary"><?php echo htmlspecialchars($row['title']); ?></td>
                        
                        <td>
                            <div class="small"><span class="text-muted">CGPA:</span> <span class="fw-bold"><?php echo e($row['cgpa']); ?></span></div>
                            <div class="small"><span class="text-muted">Backlogs:</span> <span class="fw-bold"><?php echo e($row['backlogs']); ?></span></div>
                            
                            <div class="mt-2 d-flex flex-wrap gap-1">
                                <?php 
                                if(!empty($row['skills'])){
                                    $skills = explode(',', $row['skills']);
                                    foreach(array_slice($skills, 0, 3) as $sk) echo '<span class="skill-badge">'.e(trim($sk)).'</span>';
                                    if(count($skills) > 3) echo '<span class="skill-badge">...</span>';
                                }
                                ?>
                            </div>
                        </td>
                        
                        <td>
                            <?php if(!empty($row['resume'])): ?>
                                <a href="../resumes/<?php echo $row['resume']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fa-solid fa-file-pdf"></i> View
                                </a>
                            <?php else: ?>
                                <span class="text-muted small border px-2 py-1 rounded">Missing</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        
                        <td>
                            <form method="POST" class="action-form">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="app_id" value="<?php echo $row['app_id']; ?>">
                                <select name="status" class="action-select">
                                    <option value="applied" <?php if($row['status'] == 'applied') echo 'selected'; ?>>Under Review</option>
                                    <option value="shortlisted" <?php if($row['status'] == 'shortlisted') echo 'selected'; ?>>Shortlist</option>
                                    <option value="selected" <?php if($row['status'] == 'selected') echo 'selected'; ?>>Select (Hired)</option>
                                    <option value="rejected" <?php if($row['status'] == 'rejected') echo 'selected'; ?>>Reject</option>
                                </select>
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                            <div class="mt-2">
                                <a href="schedule_interview.php?student_id=<?php echo $row['student_id']; ?>&job_id=<?php echo $row['job_id']; ?>" class="btn btn-outline-success btn-sm w-100" style="border-radius: 6px; font-weight: 600; padding: 4px 10px; border-color: #10b981; color: #059669;">
                                    <i class="fa-solid fa-calendar-plus me-1"></i> Schedule
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No applications received yet.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>