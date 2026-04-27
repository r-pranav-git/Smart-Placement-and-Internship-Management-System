<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token($_POST['csrf_token']);

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Use prepared statement (prevents SQL injection)
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['status'] = $user['status'] ?? 'approved';

    // Redirect based on role
    if ($user['role'] == 'student') {
        header("Location: ../student_dashboard.php");
    }
    elseif ($user['role'] == 'recruiter') {
        header("Location: ../recruiter_dashboard.php");
    }
    elseif ($user['role'] == 'admin') {
        header("Location: ../admin_dashboard.php");
    }

    exit();

} else {
    $error = "Invalid email or password!";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Excellence College</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/placement/assets/css/premium.css">

<style>
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --background: #f8fafc;
    --surface: rgba(255, 255, 255, 0.8);
}

body {
    background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05), transparent 50%),
                radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05), transparent 50%),
                #f8fafc;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.login-box {
    background: var(--surface);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(226, 232, 240, 0.5);
    padding: 40px;
    width: 100%;
    max-width: 420px;
    border-radius: 24px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
}

/* NAVBAR */
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 8%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
}

nav .logo-container {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

nav .logo-icon {
    font-size: 24px;
    color: var(--primary);
}

nav h2 {
    font-weight: 800;
    color: var(--secondary);
    font-size: 20px;
    letter-spacing: -0.5px;
}

nav h2 span {
    color: var(--primary);
}

.nav-links {
    display: flex;
    gap: 20px;
}

.nav-links a {
    text-decoration: none;
    color: var(--text-main);
    font-weight: 500;
    font-size: 15px;
    transition: 0.2s ease;
}

.nav-links a:hover {
    color: var(--primary);
}

.nav-links a.active {
    color: var(--primary);
    font-weight: 600;
}

/* LOGIN SECTION */
.login-section {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    background: radial-gradient(circle at top right, rgba(37,99,235,0.05) 0%, rgba(248,250,252,1) 50%);
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--secondary);
    margin-bottom: 6px;
}

.input-group .input-wrapper {
    position: relative;
}

.input-group .input-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    font-size: 14px;
}

.login-box input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    color: var(--text-main);
    transition: all 0.2s ease;
}

.login-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: white;
}

.btn-submit {
    width: 100%;
    padding: 14px;
    border: none;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    font-size: 15px;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
}

.btn-submit:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.register-link {
    margin-top: 24px;
    font-size: 14px;
    text-align: center;
    color: var(--text-light);
}

.register-link a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s ease;
}

.register-link a:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.error {
    background: #fef2f2;
    color: #ef4444;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 20px;
    text-align: center;
    border: 1px solid #fca5a5;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<nav>
    <a href="../index.php" class="logo-container">
        <i class="fa-solid fa-graduation-cap logo-icon"></i>
        <h2>Excellence <span>Portal</span></h2>
    </a>
    <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="login.php" class="active">Sign In</a>
        <a href="register.php">Create Account</a>
    </div>
</nav>

<!-- LOGIN SECTION -->
<section class="login-section">

    <div class="login-box animate-up glass-card mx-auto mt-5">
        <div class="text-center mb-4">
            <div style="font-size: 40px; color: #6366f1;"><i class="fa-solid fa-graduation-cap"></i></div>
            <h2 class="fw-800 mb-0">Welcome Back</h2>
            <p class="text-muted small">Access your career portal.</p>
        </div>

        <?php if(isset($error)) echo "<div class='error'><i class='fa-solid fa-circle-exclamation'></i> ".e($error)."</div>"; ?>

        <form method="POST">
            <?php csrf_field(); ?>

            <div class="input-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" placeholder="student@college.edu" required>
                </div>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">Sign In to Dashboard</button>

        </form>

        <div class="register-link">
            Don't have an account?  
            <a href="register.php">Register Here</a>
        </div>

    </div>

</section>

</body>
</html>