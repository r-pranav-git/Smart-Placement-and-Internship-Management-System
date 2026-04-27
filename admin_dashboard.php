<?php
session_start();
include("config/db.php");
include("includes/functions.php");

require_role('admin');

$students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM students"))['total'] ?? 0;
$recruiters = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM recruiters"))['total'] ?? 0;
$jobs = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM jobs"))['total'] ?? 0;
$applications = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM applications"))['total'] ?? 0;

// Chart Data (Application Status)
$status_query = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM applications GROUP BY status");
$status_data = [];
while($row = mysqli_fetch_assoc($status_query)) $status_data[] = $row;

// Recent Activities
$latest_jobs = mysqli_query($conn, "SELECT j.title, r.company_name, j.created_at FROM jobs j JOIN recruiters r ON j.recruiter_id = r.user_id ORDER BY j.id DESC LIMIT 5");
$latest_students = mysqli_query($conn, "SELECT name, email, created_at FROM users WHERE role='student' ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<title>Admin Dashboard | Excellence College</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/premium.css">



</head>

<body>

<nav class="navbar navbar-expand-lg sticky-top navbar-premium">
<div class="container">
<a class="navbar-brand" href="#"><i class="fa-solid fa-shield-halved text-primary"></i> Excellence <span>Admin</span></a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ms-auto">
<li class="nav-item">
<a class="nav-link" href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Overview</a>
</li>
<li class="nav-item">
<a class="nav-link" href="admin/manage_users.php"><i class="fa-solid fa-users"></i> Students</a>
</li>
<li class="nav-item">
<a class="nav-link" href="admin/manage_recruiters.php"><i class="fa-solid fa-building"></i> Recruiters</a>
</li>
<li class="nav-item">
<a class="nav-link" href="admin/view_jobs.php"><i class="fa-solid fa-briefcase"></i> Drives</a>
</li>
<li class="nav-item">
<a class="nav-link" href="admin/reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
</li>
<li class="nav-item">
<a class="nav-link text-danger" href="auth/logout.php"><i class="fa-solid fa-power-off"></i> Logout</a>
</li>
</ul>
</div>
</div>
</nav>


<div class="container">

<div class="dashboard-header">
    <div class="dashboard-title">
        <h2>System Overview</h2>
        <p>Real-time statistics covering all placement portal activity.</p>
    </div>
</div>

<div class="row g-4 mt-2 animate-up" style="animation-delay: 0.1s;">
    <!-- Analytics Chart -->
    <div class="col-lg-8">
        <div class="dashboard-card glass-card h-100" style="display: block; padding: 25px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Application Pipeline</h6>
                <a href="admin/reports.php" class="btn btn-sm btn-light border" style="border-radius: 8px;">Detailed Report</a>
            </div>
            <div style="height: 300px;">
                <canvas id="pipelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="dashboard-card glass-card h-100" style="display: block; padding: 25px;">
            <h6 class="fw-bold mb-4">Quick Actions</h6>
            <div class="d-grid gap-2">
                <a href="admin/manage_recruiters.php" class="btn btn-premium-alt text-start p-3 d-flex align-items-center gap-3">
                    <div class="action-icon bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-user-check"></i></div>
                    <div><div class="fw-bold small">Verify Recruiters</div><div class="text-muted" style="font-size: 11px;">Pending approvals</div></div>
                </a>
                <a href="admin/upload_resources.php" class="btn btn-premium-alt text-start p-3 d-flex align-items-center gap-3">
                    <div class="action-icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <div><div class="fw-bold small">Upload Resources</div><div class="text-muted" style="font-size: 11px;">Material for students</div></div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Job Postings -->
    <div class="col-lg-6">
        <div class="dashboard-card glass-card mt-2" style="display: block; padding: 25px;">
            <h6 class="fw-bold mb-4">Latest Opportunities</h6>
            <div class="activity-list">
                <?php while($job = mysqli_fetch_assoc($latest_jobs)){ ?>
                    <div class="activity-item d-flex align-items-start gap-3 mb-3 pb-3 border-bottom border-light">
                        <div class="activity-icon bg-blue-100 text-blue-600"><i class="fa-solid fa-briefcase"></i></div>
                        <div>
                            <div class="fw-bold small"><?php echo e($job['title']); ?></div>
                            <div class="text-muted extra-small"><?php echo e($job['company_name']); ?> • <?php echo date('M d', strtotime($job['created_at'])); ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- New Student Registrations -->
    <div class="col-lg-6">
        <div class="dashboard-card glass-card mt-2" style="display: block; padding: 25px;">
            <h6 class="fw-bold mb-4">New Student Profiles</h6>
            <div class="activity-list">
                <?php while($std = mysqli_fetch_assoc($latest_students)){ ?>
                    <div class="activity-item d-flex align-items-start gap-3 mb-3 pb-3 border-bottom border-light">
                        <div class="activity-icon bg-green-100 text-green-600"><i class="fa-solid fa-user-plus"></i></div>
                        <div>
                            <div class="fw-bold small"><?php echo e($std['name']); ?></div>
                            <div class="text-muted extra-small"><?php echo e($std['email']); ?> • <?php echo date('M d', strtotime($std['created_at'])); ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusData = <?php echo json_encode($status_data); ?>;
    new Chart(document.getElementById('pipelineChart'), {
        type: 'bar',
        data: {
            labels: statusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
            datasets: [{
                label: 'Applications',
                data: statusData.map(d => d.count),
                backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
        }
    });
</script>
</body>
</html>