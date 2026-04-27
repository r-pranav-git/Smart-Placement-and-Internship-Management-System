<?php
include("../config/db.php");
include("../includes/functions.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token($_POST['csrf_token']);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roll_no = trim($_POST['roll_no']);
    $department = trim($_POST['department']);
    $semester = (int) $_POST['semester'];

    // Check if email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {

        // Start transaction
        mysqli_begin_transaction($conn);

        try {

            // Insert into users table
            $stmt1 = mysqli_prepare($conn, 
                "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')"
            );
            mysqli_stmt_bind_param($stmt1, "sss", $name, $email, $password);
            if (!mysqli_stmt_execute($stmt1)) {
                $err = mysqli_stmt_error($stmt1);
                if (strpos($err, 'Duplicate entry') !== false) {
                    throw new Exception("This email address is already registered.");
                }
                throw new Exception("An error occurred while creating the user account. Please try again.");
            }

            $user_id = mysqli_insert_id($conn);

            // Insert into students table
            $stmt2 = mysqli_prepare($conn, 
                "INSERT INTO students (user_id, roll_no, department, semester) 
                 VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, "issi", $user_id, $roll_no, $department, $semester);
            if (!mysqli_stmt_execute($stmt2)) {
                $err = mysqli_stmt_error($stmt2);
                if (strpos($err, 'Duplicate entry') !== false) {
                    throw new Exception("This Roll Number is already registered in the system.");
                }
                throw new Exception("An error occurred while creating the student profile. Please try again.");
            }

            // Commit
            mysqli_commit($conn);

            $success = "Registration successful! You can now login.";

        } catch (Exception $e) {

            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration | Excellence College</title>
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
        <a href="login.php">Sign In</a>
        <a href="register.php" class="active">Create Account</a>
    </div>
</nav>

<section class="auth-section">

    <div class="auth-card register-box">

        <h2 class="auth-title text-center mb-1">Student Registration</h2>
        <p class="auth-subtitle text-center">Create an account to begin your career journey.</p>

        <div class="toggle-role">
            <a href="register.php" class="active"><i class="fa-solid fa-user-graduate"></i> Student</a>
            <a href="register_recruiter.php"><i class="fa-solid fa-building"></i> Recruiter</a>
        </div>

        <?php 
        if(isset($error)) echo "<div class='message error'><i class='fa-solid fa-circle-exclamation'></i> ".e($error)."</div>"; 
        if(isset($success)) echo "<div class='message success'><i class='fa-solid fa-circle-check'></i> ".e($success)."</div>"; 
        ?>

        <form method="POST">
            <?php csrf_field(); ?>
            <div class="form-grid">
                <div class="input-group full-width">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="name" placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" placeholder="student@college.edu" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Create a strong password" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Roll Number</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-id-badge"></i>
                        <input type="text" name="roll_no" placeholder="E.g. 2024CS01" value="<?php echo htmlspecialchars($_POST['roll_no'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Current Semester</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-layer-group"></i>
                        <input type="number" name="semester" placeholder="e.g. 6" value="<?php echo htmlspecialchars($_POST['semester'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Department</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-book"></i>
                        <input type="text" name="department" placeholder="Computer Science" value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Register Account</button>

        </form>

        <div class="auth-link">
            Already have an account?  
            <a href="login.php">Sign In</a>
        </div>

    </div>

</section>

</body>
</html>