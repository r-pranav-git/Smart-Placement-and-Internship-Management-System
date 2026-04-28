<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

$recruiter_id = $_SESSION['user_id'];

// Pre-fill (from URL)
$selected_student = isset($_GET['student_id']) ? intval($_GET['student_id']) : '';
$selected_job = isset($_GET['job_id']) ? intval($_GET['job_id']) : '';

// Fetch students
$students = mysqli_query($conn, "SELECT id, name FROM users WHERE role='student' ORDER BY name ASC");

// Fetch recruiter jobs
$stmt_jobs = mysqli_prepare($conn, "SELECT id, title FROM jobs WHERE recruiter_id=?");
mysqli_stmt_bind_param($stmt_jobs, "i", $recruiter_id);
mysqli_stmt_execute($stmt_jobs);
$jobs = mysqli_stmt_get_result($stmt_jobs);

// Handle form
if(isset($_POST['schedule'])) {
    verify_csrf_token($_POST['csrf_token']);
    $student_id = intval($_POST['student_id']);
    $job_id = intval($_POST['job_id']);
    $datetime = $_POST['datetime'];
    $location = trim($_POST['location']);

    if (!$student_id || !$job_id || !$datetime || !$location) {
        $error = "All fields are required!";
    } else {
        // Format datetime properly for MySQL
        $date = date("Y-m-d H:i:s", strtotime($datetime));

        // Insert interview
        $stmt = mysqli_prepare($conn, "INSERT INTO interviews (student_id, job_id, date, location) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiss", $student_id, $job_id, $date, $location);

        if (mysqli_stmt_execute($stmt)) {
            // Send notification
            $msg = "Interview scheduled on " . date('d M Y, h:i A', strtotime($datetime)) . " at " . $location;
            $stmt2 = mysqli_prepare($conn, "INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt2, "is", $student_id, $msg);
            mysqli_stmt_execute($stmt2);

            $success = "Interview scheduled successfully!";
        } else {
            $error = "Failed to schedule interview.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Interview | Recruiter Dashboard</title>
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
        <a href="schedule_interview.php" class="active"><i class="fa-solid fa-calendar-check"></i> <span>Schedule Interview</span></a>
        <a href="send_message.php"><i class="fa-solid fa-envelope-open-text"></i> <span>Send Notifications</span></a>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/logout.php"><i class="fa-solid fa-power-off"></i> <span>Sign Out</span></a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4 animate-up">
        <h2 class="page-title mb-0">Schedule Interview</h2>
        <p class="page-subtitle">Set up interview rounds for potential candidates.</p>
    </div>

    <?php 
    if(isset($success)) echo "<div class='alert alert-success animate-up'><i class='fa-solid fa-check-circle me-2'></i> ".e($success)."</div>"; 
    if(isset($error)) echo "<div class='alert alert-danger animate-up'><i class='fa-solid fa-exclamation-circle me-2'></i> ".e($error)."</div>"; 
    ?>

    <div class="form-card surface-card glass-card animate-up" style="animation-delay: 0.1s;">
        <form method="POST">
            <?php csrf_field(); ?>
            
            <div class="mb-3">
                <label class="form-label fw-600">Select Student</label>
                <select name="student_id" class="form-select" required>
                    <option value="">-- Choose Student --</option>
                    <?php while($row = mysqli_fetch_assoc($students)): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selected_student) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-600">Select Job Opportunity</label>
                <select name="job_id" class="form-select" required>
                    <option value="">-- Choose Job --</option>
                    <?php while($row = mysqli_fetch_assoc($jobs)): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selected_job) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-600">Date & Time</label>
                <input type="datetime-local" name="datetime" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-600">Location / Mode</label>
                <input type="text" name="location" class="form-control" placeholder="e.g. Google Meet / HQ Office" required>
            </div>

            <button type="submit" name="schedule" class="btn-premium w-100">
                <i class="fa-solid fa-calendar-plus me-1"></i> Confirm Schedule
            </button>
        </form>
    </div>
</div>

</body>
</html>
