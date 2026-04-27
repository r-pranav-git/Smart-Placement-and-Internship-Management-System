<?php
session_start();
include("config/db.php");
include("includes/functions.php");

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch student profile using prepared statement */
$stmt = mysqli_prepare($conn, "
    SELECT users.name, students.*
    FROM users
    LEFT JOIN students ON users.id = students.user_id
    WHERE users.id = ?
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if(!$data){
    die("Student record not found.");
}

// Calculate Profile Completion %
$complete_fields = 0;
$total_fields = 7;
if(!empty($data['roll_no'])) $complete_fields++;
if(!empty($data['department'])) $complete_fields++;
if(!empty($data['skills'])) $complete_fields++;
if(!empty($data['linkedin'])) $complete_fields++;
if(!empty($data['resume'])) $complete_fields++;
if(!empty($data['cgpa'])) $complete_fields++;
if(!empty($data['semester'])) $complete_fields++;
$profile_readiness = round(($complete_fields / $total_fields) * 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<title>Student Dashboard | Excellence College</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary: #0f172a;
    --surface: #ffffff;
    --background: #f8fafc;
    --text-main: #1e293b;
    --text-light: #64748b;
    --border: #e2e8f0;
    --radius-md: 12px;
    --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
}

body{
    background: var(--background);
    font-family: 'Inter', sans-serif;
    color: var(--text-main);
    -webkit-font-smoothing: antialiased;
}

/* Navbar */
.navbar{
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    padding: 15px 0;
}

.navbar-brand{
    font-weight: 800;
    color: var(--secondary) !important;
    font-size: 20px;
}
.navbar-brand span {
    color: var(--primary);
}

.nav-link{
    color: var(--text-main) !important;
    font-weight: 500;
    font-size: 15px;
    margin-right: 15px;
    transition: 0.2s ease;
}

.nav-link:hover{
    color: var(--primary) !important;
}

.nav-link.text-danger {
    color: #ef4444 !important;
    font-weight: 600;
}
.nav-link.text-danger:hover {
    color: #dc2626 !important;
}

/* Cards */
.dashboard-card{
    background: var(--surface);
    border-radius: var(--radius-md);
    padding: 30px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--primary);
}

.card-title {
    color: var(--secondary);
    font-weight: 700;
    font-size: 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i {
    color: var(--primary);
}

.info-label {
    font-size: 13px;
    color: var(--text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.info-value {
    font-size: 16px;
    font-weight: 500;
    color: var(--text-main);
    margin-bottom: 20px;
}

/* Tables */
.table {
    margin-bottom: 0;
}
.table th{
    background: #f1f5f9;
    color: var(--text-light);
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: none;
    padding: 15px;
}
.table td {
    padding: 16px 15px;
    vertical-align: middle;
    color: var(--text-main);
    font-size: 15px;
    border-bottom: 1px solid var(--border);
}

.btn-primary {
    background: var(--primary);
    border: none;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}
.btn-primary:hover {
    background: var(--primary-dark);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.btn-success {
    background: #10b981;
    border: none;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 8px;
}
.btn-success:hover {
    background: #059669;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    background: #f1f5f9;
    color: var(--text-light);
}
.status-badge.applied { background: #dbeafe; color: #2563eb; }
.status-badge.shortlisted { background: #dcfce7; color: #16a34a; }
.status-badge.rejected { background: #fee2e2; color: #ef4444; }

</style>

</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
<div class="container">
<a class="navbar-brand" href="#"><i class="fa-solid fa-graduation-cap"></i> Student <span>Panel</span></a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ms-auto">
<li class="nav-item">
<a class="nav-link" href="student_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
</li>
<li class="nav-item">
<a class="nav-link" href="student/edit_profile.php"><i class="fa-solid fa-user-pen"></i> Profile</a>
</li>
<li class="nav-item">
<a class="nav-link" href="student/my_applications.php"><i class="fa-solid fa-paper-plane"></i> Applications</a>
</li>
<li class="nav-item">
<a class="nav-link" href="student/interview_schedule.php"><i class="fa-solid fa-calendar"></i> Interviews</a>
</li>
<li class="nav-item">
<a class="nav-link text-danger" href="auth/logout.php"><i class="fa-solid fa-power-off"></i> Logout</a>
</li>
</ul>
</div>
</div>
</nav>


<div class="container mt-5">
    <div class="row">
        <!-- NOTIFICATIONS -->
        <div class="col-12 mb-4 animate-up">
            <?php
            $notif_stmt = mysqli_prepare($conn, "SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 3");
            mysqli_stmt_bind_param($notif_stmt, "i", $user_id);
            mysqli_stmt_execute($notif_stmt);
            $notif_result = mysqli_stmt_get_result($notif_stmt);
            
            if(mysqli_num_rows($notif_result) > 0) {
            ?>
            <div class="alert alert-info border-info d-flex align-items-center mb-0 glass-card" style="border-radius: 12px; background: rgba(239, 246, 255, 0.6); padding: 20px;">
                <i class="fa-solid fa-bell text-primary fs-3 me-4"></i>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-2">Recent Updates</h6>
                    <div class="small text-secondary">
                        <?php while($n = mysqli_fetch_assoc($notif_result)) { ?>
                            <div class="mb-2 bg-white p-2 rounded border border-light shadow-sm">
                                <?php echo htmlspecialchars($n['message']); ?> 
                                <span class="text-muted" style="font-size: 11px; margin-left: 8px;"><i class="fa-regular fa-clock"></i> <?php echo date('M d, g:i A', strtotime($n['created_at'])); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- PROFILE & HUB -->
        <div class="col-lg-8">
            <div class="dashboard-card glass-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h4 class="card-title mb-0"><i class="fa-solid fa-id-card"></i> Academic Profile</h4>
                    <div class="text-end">
                        <div class="extra-small fw-bold text-muted uppercase">Profile Readiness</div>
                        <div class="progress mt-1" style="width: 120px; height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo $profile_readiness; ?>%"></div>
                        </div>
                        <span class="extra-small fw-bold text-success"><?php echo $profile_readiness; ?>% Complete</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo $data['name'] ?? ''; ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Roll Number</div>
                        <div class="info-value"><?php echo $data['roll_no'] ?? '<span class="text-danger">Not Updated</span>'; ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Department</div>
                        <div class="info-value"><?php echo $data['department'] ?? '<span class="text-danger">Not Updated</span>'; ?></div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="info-label">Semester</div>
                        <div class="info-value"><?php echo e($data['semester'] ?? 'Not Updated'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">CGPA</div>
                        <div class="info-value"><?php echo e($data['cgpa'] ?? 'Not Updated'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Active Backlogs</div>
                        <div class="info-value"><?php echo e($data['backlogs'] ?? '0'); ?></div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="info-label">Technical Skills</div>
                        <div class="info-value">
                            <?php 
                            if(!empty($data['skills'])){
                                $skills = explode(',', $data['skills']);
                                foreach($skills as $skill){
                                    echo '<span class="badge bg-primary me-1 mb-1">'.e(trim($skill)).'</span>';
                                }
                            } else {
                                echo '<span class="text-muted small">No skills listed</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="info-label">Social Links</div>
                        <div class="d-flex gap-3 mt-1">
                            <?php if(!empty($data['linkedin'])){ ?>
                                <a href="<?php echo e($data['linkedin']); ?>" target="_blank" class="text-primary fs-5" title="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                            <?php } ?>
                            <?php if(!empty($data['github'])){ ?>
                                <a href="<?php echo e($data['github']); ?>" target="_blank" class="text-dark fs-5" title="GitHub"><i class="fa-brands fa-github"></i></a>
                            <?php } ?>
                            <?php if(!empty($data['portfolio_url'])){ ?>
                                <a href="<?php echo e($data['portfolio_url']); ?>" target="_blank" class="text-secondary fs-5" title="Portfolio"><i class="fa-solid fa-globe"></i></a>
                            <?php } ?>
                            <?php if(empty($data['linkedin']) && empty($data['github']) && empty($data['portfolio_url'])){ ?>
                                <span class="text-muted small">None provided</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Career Hub Section (Fills space) -->
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <div class="dashboard-card glass-card h-100" style="padding: 24px; display: block; border-left: 4px solid var(--acc-color);">
                        <h6 class="fw-bold mb-3 small"><i class="fa-solid fa-lightbulb text-warning me-2"></i> Career Tip</h6>
                        <p class="text-muted" style="font-size: 13px;">Update your technical skills regularly to appear in more recruiter searches. Current trending: <strong>React, Node.js, Python</strong>.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dashboard-card glass-card h-100" style="padding: 24px; display: block; border-left: 4px solid var(--p-color);">
                        <h6 class="fw-bold mb-3 small"><i class="fa-solid fa-book-open text-primary me-2"></i> Resources</h6>
                        <ul class="list-unstyled mb-0" style="font-size: 13px;">
                            <li class="mb-2"><a href="#" class="text-decoration-none"><i class="fa-solid fa-file-lines me-2"></i> Resume Templates</a></li>
                            <li><a href="#" class="text-decoration-none"><i class="fa-solid fa-circle-play me-2"></i> Interview Prep Docs</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESUME -->
        <div class="col-lg-4">
            <div class="dashboard-card glass-card" style="height: calc(100% - 30px);">
                <h4 class="card-title"><i class="fa-solid fa-file-pdf"></i> Resume</h4>
                
                <?php if(!empty($data['resume'])){ ?>
                <div class="d-flex align-items-center gap-2 mb-4 text-success fw-bold">
                    <i class="fa-solid fa-circle-check"></i> Resume Uploaded
                </div>
                <a class="btn btn-outline-primary btn-sm w-100 mb-4 fw-bold" href="resumes/<?php echo $data['resume']; ?>" target="_blank">
                    <i class="fa-solid fa-eye"></i> View Current Resume
                </a>
                <?php } else { ?>
                    <p class="text-muted small mb-4">No resume uploaded yet. Ensure you upload one to apply for opportunities.</p>
                <?php } ?>

                <form action="student/upload_resume.php" method="POST" enctype="multipart/form-data">
                    <?php csrf_field(); ?>
                    <div class="mb-3">
                        <input type="file" name="resume" class="form-control form-control-sm" accept=".pdf" required>
                    </div>
                    <button class="btn-premium btn-sm w-100"><i class="fa-solid fa-cloud-arrow-up"></i> Upload New</button>
                </form>
            </div>
        </div>
    </div>


    <div class="dashboard-card glass-card animate-up" style="animation-delay: 0.2s;">
        <h4 class="card-title fw-800 mb-4"><i class="fa-solid fa-briefcase"></i> Eligible Opportunities</h4>
        <div class="table-responsive">
            <table class="table table-premium">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Department Req.</th>
                        <th>Type</th>
                        <th>Deadline</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $dept = $data['department'] ?? '';
                $cgpa = floatval($data['cgpa'] ?? 0);
                $backlogs = intval($data['backlogs'] ?? 0);

                $job_stmt = mysqli_prepare($conn, "
                    SELECT * FROM jobs
                    WHERE 
                    (department = ? OR department IS NULL)
                    AND min_cgpa <= ?
                    AND max_backlogs >= ?
                    AND deadline >= CURDATE()
                ");
                mysqli_stmt_bind_param($job_stmt, "sdi", $dept, $cgpa, $backlogs);
                mysqli_stmt_execute($job_stmt);
                $jobs = mysqli_stmt_get_result($job_stmt);

                if(mysqli_num_rows($jobs) > 0) {
                    while($row = mysqli_fetch_assoc($jobs)) {
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['department']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><i class="fa-regular fa-clock text-muted"></i> <?php echo htmlspecialchars($row['deadline']); ?></td>
                        <td class="text-center">
                            <form action="student/apply_job.php" method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="job_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-success-premium btn-sm py-2 px-4">
                                    Apply Now
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No eligible opportunities match your profile at the moment.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="dashboard-card glass-card animate-up" style="animation-delay: 0.3s;">
        <h4 class="card-title fw-800 mb-4"><i class="fa-solid fa-list-check"></i> Application Status</h4>
        <div class="table-responsive">
            <table class="table table-premium">
                <thead>
                    <tr>
                        <th>Opportunity details</th>
                        <th>Current Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $app_stmt = mysqli_prepare($conn, "
                    SELECT jobs.title, applications.status
                    FROM applications
                    JOIN jobs ON applications.job_id = jobs.id
                    WHERE applications.student_id = ?
                ");
                mysqli_stmt_bind_param($app_stmt, "i", $user_id);
                mysqli_stmt_execute($app_stmt);
                $app_results = mysqli_stmt_get_result($app_stmt);

                if(mysqli_num_rows($app_results) > 0) {
                    while($row = mysqli_fetch_assoc($app_results)) {
                        $statusClass = strtolower($row['status']);
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                    }
                } else {
                    echo "<tr><td colspan='2' class='text-center py-4 text-muted'>You haven't applied to any opportunities yet.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>