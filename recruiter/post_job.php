<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

$recruiter_id = $_SESSION['user_id'];

if(isset($_POST['post']))
{
    if($_SESSION['status'] !== 'approved') {
        $error = "Your account must be approved by the admin before you can post jobs.";
    } else {
        verify_csrf_token($_POST['csrf_token']);
        $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $department = trim($_POST['department']);
    $cgpa = (float) $_POST['cgpa'];
    $backlogs = (int) $_POST['backlogs'];
    $deadline = trim($_POST['deadline']);
    $salary = (float) $_POST['salary_package'];
    $location = trim($_POST['location']);

    $stmt = mysqli_prepare($conn, "INSERT INTO jobs (recruiter_id, title, description, type, department, min_cgpa, max_backlogs, deadline, salary, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssdisds", $recruiter_id, $title, $description, $type, $department, $cgpa, $backlogs, $deadline, $salary, $location);

    if(mysqli_stmt_execute($stmt)){
        $success = "Job posted successfully!";
    } else {
        $error = "Failed to post job: " . mysqli_error($conn);
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Job / Internship | Recruiter Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/premium.css">
    <link rel="stylesheet" href="recruiter_ui.css">
    <style>
        .form-card {
            padding: 30px;
            max-width: 800px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            padding: 10px 14px;
            font-size: 14px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 10px 24px;
            border-radius: var(--radius-md);
            font-weight: 500;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
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
        <a href="post_job.php" class="active"><i class="fa-solid fa-file-circle-plus"></i> <span>Post Opportunity</span></a>
        <a href="my_jobs.php"><i class="fa-solid fa-list-check"></i> <span>Manage Postings</span></a>
        <a href="view_applications.php"><i class="fa-solid fa-users-viewfinder"></i> <span>Review Applicants</span></a>
        <a href="send_message.php"><i class="fa-solid fa-envelope-open-text"></i> <span>Send Notifications</span></a>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/logout.php"><i class="fa-solid fa-power-off"></i> <span>Sign Out</span></a>
    </div>
</div>

<div class="main-content">
    <div class="page-header animate-up">
        <h2 class="page-title">Create Job Posting</h2>
        <p class="page-subtitle">Fill out the details below to attract top talent.</p>
    </div>

    <?php 
    if(isset($success)) echo "<div class='alert alert-success'><i class='fa-solid fa-circle-check me-2'></i> ".e($success)."</div>"; 
    if(isset($error)) echo "<div class='alert alert-danger'><i class='fa-solid fa-circle-exclamation me-2'></i> ".e($error)."</div>"; 
    ?>

    <div class="form-card surface-card glass-card animate-up" style="animation-delay: 0.1s;">
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="row g-4">
                
                <div class="col-md-8">
                    <label class="form-label">Job Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Software Engineer" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Opportunity Type</label>
                    <select name="type" class="form-select">
                        <option value="placement">Full-time Placement</option>
                        <option value="internship">Internship</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Job Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe the role, responsibilities, and perks..." required></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Target Department</label>
                    <input type="text" name="department" class="form-control" placeholder="e.g. Computer Science" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Application Deadline</label>
                    <input type="date" name="deadline" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Salary Package (LPA)</label>
                    <input type="number" step="0.1" name="salary_package" class="form-control" placeholder="e.g. 12.5" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Work Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Mumbai / Remote" required>
                </div>

                <div class="col-12"><hr class="text-muted"></div>
                <h6 class="text-secondary fw-bold mb-0">Eligibility Criteria</h6>

                <div class="col-md-6">
                    <label class="form-label">Minimum CGPA Required</label>
                    <input type="number" step="0.01" name="cgpa" class="form-control" placeholder="e.g. 7.5" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Maximum Allowed Backlogs</label>
                    <input type="number" name="backlogs" class="form-control" placeholder="e.g. 0" required>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" name="post" class="btn-premium">
                        <i class="fa-solid fa-paper-plane me-2"></i> Publish Job
                    </button>
                    <a href="../recruiter_dashboard.php" class="btn btn-light ms-2 border">Cancel</a>
                </div>

            </div>
        </form>
    </div>
</div>

</body>
</html>