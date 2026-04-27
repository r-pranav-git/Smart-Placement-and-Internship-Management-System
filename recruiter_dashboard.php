<?php
session_start();
include("config/db.php");
include("includes/functions.php");

require_role('recruiter');

$rid = $_SESSION['user_id'];

// Recruiter Stats
$active_jobs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM jobs WHERE recruiter_id = $rid"))['count'] ?? 0;
$total_apps = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.recruiter_id = $rid"))['count'] ?? 0;

// Recent Applicants
$latest_apps = mysqli_query($conn, "
    SELECT u.name, j.title, a.status, a.applied_at 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN users u ON a.student_id = u.id 
    WHERE j.recruiter_id = $rid 
    ORDER BY a.id DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Recruiter Dashboard | Excellence College</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/premium.css">

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
    --radius-lg: 20px;
    --shadow-md: 0 10px 25px -5px rgba(52, 14, 14, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Inter', sans-serif;
}

body{
    background: var(--background);
    display: flex;
    color: var(--text-main);
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
}

/* Sidebar */
.sidebar{
    width: 260px;
    background: var(--surface);
    border-right: 1px solid var(--border);
    padding-top: 30px;
    position: fixed;
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.sidebar-brand {
    padding: 0 25px 30px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}

.sidebar h2 {
    color: var(--secondary);
    font-weight: 800;
    font-size: 22px;
}
.sidebar h2 span {
    color: var(--primary);
}
.sidebar p {
    color: var(--text-light);
    font-size: 13px;
    font-weight: 500;
    margin-top: 4px;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 0 15px;
    flex: 1;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    color: var(--text-light);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.sidebar-nav a i {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.sidebar-nav a:hover, .sidebar-nav a.active {
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary);
}

.sidebar-footer {
    padding: 20px 15px;
    border-top: 1px solid var(--border);
}

.sidebar-footer a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    color: #ef4444;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.sidebar-footer a:hover {
    background: #fef2f2;
}


/* Main Content */
.main{
    margin-left: 260px;
    padding: 40px;
    width: calc(100% - 260px);
}

.status-locked {
    background: #fff7ed;
    border: 1px solid #ffedd5;
    padding: 20px;
    border-radius: var(--radius-md);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
}
.status-locked i {
    color: #f97316;
    font-size: 24px;
}

.header{
    margin-bottom: 40px;
}

.header h1 {
    font-weight: 800;
    color: var(--secondary);
    font-size: 28px;
    letter-spacing: -0.5px;
}

.header p {
    color: var(--text-light);
    font-size: 15px;
    margin-top: 5px;
}

/* Dashboard cards */
.cards{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

.card{
    background: var(--surface);
    padding: 30px;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.card:hover{
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
    border-color: rgba(37, 99, 235, 0.2);
}

.card-icon {
    width: 50px;
    height: 50px;
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 20px;
}

.card h3 {
    margin-bottom: 10px;
    color: var(--secondary);
    font-weight: 700;
    font-size: 18px;
}

.card p {
    color: var(--text-light);
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 20px;
    flex: 1;
}

.card a {
    text-decoration: none;
    color: var(--primary);
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: 0.2s;
}

.card a i {
    transition: transform 0.2s;
}

.card:hover a i {
    transform: translateX(3px);
}

.card a:hover {
    color: var(--primary-dark);
}

@media (max-width: 768px) {
    .sidebar {
        width: 80px;
    }
    .sidebar-brand h2, .sidebar-brand p, .sidebar-nav a span, .sidebar-footer a span {
        display: none;
    }
    .main {
        margin-left: 80px;
        width: calc(100% - 80px);
        padding: 20px;
    }
}
.bg-blue-100 { background: rgba(37, 99, 235, 0.1); }
.bg-green-100 { background: rgba(16, 185, 129, 0.1); }
</style>

</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>Excellence <span>Portal</span></h2>
        <p>Hiring Partner Panel</p>
    </div>

    <div class="sidebar-nav">
        <a href="recruiter_dashboard.php" class="active"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a>
        <a href="recruiter/post_job.php"><i class="fa-solid fa-file-circle-plus"></i> <span>Post Opportunity</span></a>
        <a href="recruiter/my_jobs.php"><i class="fa-solid fa-list-check"></i> <span>Manage Postings</span></a>
        <a href="recruiter/view_applications.php"><i class="fa-solid fa-users-viewfinder"></i> <span>Review Applicants</span></a>
        <a href="recruiter/send_message.php"><i class="fa-solid fa-envelope-open-text"></i> <span>Send Notifications</span></a>
    </div>

    <div class="sidebar-footer">
        <a href="auth/logout.php"><i class="fa-solid fa-power-off"></i> <span>Sign Out</span></a>
    </div>
</div>

<!-- Main Content -->
<div class="main">

    <div class="header animate-up">
        <h1 class="fw-800">Recruiter Dashboard</h1>
        <p class="text-muted">Welcome back! Manage your recruitment drives and discover top talent.</p>
    </div>

    <?php if($_SESSION['status'] == 'pending'): ?>
    <div class="status-locked">
        <i class="fa-solid fa-hourglass-half"></i>
        <div>
            <h6 class="fw-bold mb-1" style="color: #9a3412;">Account Pending Approval</h6>
            <p class="mb-0 small" style="color: #c2410c;">Your account is currently being reviewed by the administration. You will be able to post jobs once approved.</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="cards <?php echo $_SESSION['status'] == 'pending' ? 'opacity-50 pointer-events-none' : ''; ?> animate-up" style="<?php echo $_SESSION['status'] == 'pending' ? 'pointer-events: none;' : ''; ?> animation-delay: 0.1s;">

        </div>

    </div>

    <!-- NEW SECTION: RECENT ACTIVITY & STATS -->
    <div class="row mt-4 g-4 animate-up" style="animation-delay: 0.2s;">
        <!-- Distribution Stats -->
        <div class="col-lg-4">
            <div class="card glass-card h-100">
                <h3 class="mb-4">Hiring Snapshot</h3>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="action-icon bg-blue-100 text-primary fs-4" style="width: 50px; height: 50px;"><i class="fa-solid fa-briefcase"></i></div>
                    <div>
                        <div class="text-muted small">Active Postings</div>
                        <div class="fw-800 fs-4"><?php echo $active_jobs; ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon bg-green-100 text-success fs-4" style="width: 50px; height: 50px;"><i class="fa-solid fa-user-group"></i></div>
                    <div>
                        <div class="text-muted small">Total Applicants</div>
                        <div class="fw-800 fs-4"><?php echo $total_apps; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applicants Feed -->
        <div class="col-lg-8">
            <div class="card glass-card h-100" style="padding: 0; overflow: hidden;">
                <div class="p-4 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Recent Applicants</h3>
                    <a href="recruiter/view_applications.php" class="small text-decoration-none">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 13px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Candidate</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($latest_apps) > 0){ ?>
                                <?php while($app = mysqli_fetch_assoc($latest_apps)){ ?>
                                    <tr>
                                        <td class="ps-4 fw-600"><?php echo e($app['name']); ?></td>
                                        <td><?php echo e($app['title']); ?></td>
                                        <td><span class="badge bg-opacity-10 <?php echo $app['status'] == 'applied' ? 'bg-primary text-primary' : 'bg-success text-success'; ?>"><?php echo ucfirst($app['status']); ?></span></td>
                                        <td class="pe-4 text-muted"><?php echo date('M d', strtotime($app['created_at'])); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No applications received yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>