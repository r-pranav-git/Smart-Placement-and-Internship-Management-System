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
<link rel="stylesheet" href="/placement/assets/css/auth_ui.css">
</head>

<body class="auth-shell">

<!-- NAVBAR -->
<nav class="auth-navbar">
    <a href="../index.php" class="auth-logo">
        <i class="fa-solid fa-graduation-cap"></i>
        <h2>Excellence <span>Portal</span></h2>
    </a>
    <div class="auth-nav-links">
        <a href="../index.php">Home</a>
        <a href="login.php" class="active">Sign In</a>
        <a href="register.php">Create Account</a>
    </div>
</nav>

<!-- LOGIN SECTION -->
<section class="auth-section">

    <div class="auth-card auth-card-sm animate-up glass-card">
        <div class="text-center mb-4">
            <div style="font-size: 40px; color: #6366f1;"><i class="fa-solid fa-graduation-cap"></i></div>
            <h2 class="auth-title mb-0">Welcome Back</h2>
            <p class="auth-subtitle">Access your career portal.</p>
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

            <button type="submit" class="btn-premium auth-btn">Sign In to Dashboard</button>

        </form>

        <div class="auth-link">
            Don't have an account?  
            <a href="register.php">Register Here</a>
        </div>

    </div>

</section>

</body>
</html>