<?php
include("../config/db.php");
include("../includes/functions.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token($_POST['csrf_token']);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Recruiter-specific fields
    $company_name = trim($_POST['company_name']);
    $contact_person = trim($_POST['contact_person']);

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
                "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'recruiter', 'pending')"
            );
            mysqli_stmt_bind_param($stmt1, "sss", $name, $email, $password);
            if (!mysqli_stmt_execute($stmt1)) {
                $err = mysqli_stmt_error($stmt1);
                if (strpos($err, 'Duplicate entry') !== false) {
                    throw new Exception("This email address is already registered.");
                }
                throw new Exception("An error occurred while creating the recruiter account. Please try again.");
            }

            $user_id = mysqli_insert_id($conn);

            // Insert into recruiters table
            $stmt2 = mysqli_prepare($conn, 
                "INSERT INTO recruiters (user_id, company_name, contact_person) 
                 VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, "iss", $user_id, $company_name, $contact_person);
            if (!mysqli_stmt_execute($stmt2)) {
                $err = mysqli_stmt_error($stmt2);
                if (strpos($err, 'Duplicate entry') !== false) {
                    throw new Exception("This Company Name is already registered in the system.");
                }
                throw new Exception("An error occurred while creating the company profile. Please try again.");
            }

            // Commit
            mysqli_commit($conn);

            $success = "Registration successful! You can now login as a Recruiter.";

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
<title>Recruiter Registration | Excellence College</title>
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

        <h2 class="auth-title text-center mb-1">Partner with Us</h2>
        <p class="auth-subtitle text-center">Join as a hiring partner to discover top talent.</p>

        <div class="toggle-role">
            <a href="register.php"><i class="fa-solid fa-user-graduate"></i> Student</a>
            <a href="register_recruiter.php" class="active"><i class="fa-solid fa-building"></i> Recruiter</a>
        </div>

        <?php 
        if(isset($error)) echo "<div class='message error'><i class='fa-solid fa-circle-exclamation'></i> $error</div>"; 
        if(isset($success)) echo "<div class='message success'><i class='fa-solid fa-circle-check'></i> $success</div>"; 
        ?>

        <form method="POST">
            <?php csrf_field(); ?>
            <div class="form-grid">
                
                <div class="input-group full-width">
                    <label>Company Name</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-building"></i>
                        <input type="text" name="company_name" placeholder="e.g. Acme Corp" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Contact Person (Your Name)</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="name" placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Official Email Address</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" placeholder="hr@company.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Create a strong password" required>
                    </div>
                </div>

                <!-- Hidden contact person mapped directly as well -->
                <input type="hidden" name="contact_person" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

            </div>

            <button type="submit" class="btn-premium w-100 py-3 mt-2">Register Company</button>

        </form>

        <div class="auth-link">
            Already registered?  
            <a href="login.php">Sign In</a>
        </div>

    </div>

</section>

<script>
    // Keep 'name' and 'contact_person' insync for ease of server validation
    document.querySelector('input[name="name"]').addEventListener('input', function(e) {
        document.querySelector('input[name="contact_person"]').value = e.target.value;
    });
</script>

</body>
</html>
