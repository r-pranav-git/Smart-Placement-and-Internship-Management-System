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

    $stmt = mysqli_prepare($conn, "INSERT INTO jobs (recruiter_id, title, description, type, department, min_cgpa, max_backlogs, deadline, salary_package, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssssisds", $recruiter_id, $title, $description, $type, $department, $cgpa, $backlogs, $deadline, $salary, $location);

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
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0f172a;
            --background: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
        }

        /* SIDEBAR (matching recruiter_dashboard.php) */
        .sidebar {
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding-top: 20px;
            position: fixed;
            width: 260px;
        }

        .sidebar .brand {
            padding: 0 24px 24px;
            font-size: 20px;
            font-weight: 800;
            color: var(--secondary);
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }
        .sidebar .brand span { color: var(--primary); }
        .sidebar .brand i { color: var(--primary); margin-right: 8px; }

        .nav-link {
            color: var(--text-light);
            padding: 12px 24px;
            font-weight: 500;
            margin: 4px 16px;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .nav-link:hover { color: var(--primary); background: #f1f5f9; }
        .nav-link.active { color: var(--primary); background: #eff6ff; font-weight: 600; }

        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        .page-header { margin-bottom: 30px; }
        .page-title { font-weight: 700; color: var(--secondary); font-size: 24px; }

        .form-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
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
    <div class="brand">
        <i class="fa-solid fa-graduation-cap"></i> Excellence <span>Portal</span>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="../recruiter_dashboard.php">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="post_job.php">
                <i class="fa-solid fa-briefcase"></i> Post Job
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="view_applications.php">
                <i class="fa-solid fa-users"></i> Applications
            </a>
        </li>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="../logout.php">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="page-header animate-up">
        <h2 class="page-title fw-800">Create Job Posting</h2>
        <p class="text-muted">Fill out the details below to attract top talent.</p>
    </div>

    <?php 
    if(isset($success)) echo "<div class='alert alert-success'><i class='fa-solid fa-circle-check me-2'></i> ".e($success)."</div>"; 
    if(isset($error)) echo "<div class='alert alert-danger'><i class='fa-solid fa-circle-exclamation me-2'></i> ".e($error)."</div>"; 
    ?>

    <div class="form-card glass-card animate-up" style="animation-delay: 0.1s;">
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