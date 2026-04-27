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

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Inter', sans-serif;
}

body {
    background: var(--background);
    color: var(--text-main);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
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

/* REGISTER SECTION */
.register-section {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    background: radial-gradient(circle at top right, rgba(37,99,235,0.05) 0%, rgba(248,250,252,1) 50%);
}

/* REGISTER BOX */
.register-box {
    background: var(--surface);
    padding: 40px 32px;
    width: 100%;
    max-width: 480px;
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}

.register-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--primary);
}

.register-box h2 {
    margin-bottom: 8px;
    color: var(--secondary);
    font-size: 24px;
    font-weight: 700;
    text-align: center;
}

.register-box p.subtitle {
    text-align: center;
    color: var(--text-light);
    font-size: 14px;
    margin-bottom: 20px;
}

.toggle-role {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 30px;
}
.toggle-role a {
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-light);
    background: var(--background);
    border: 1px solid var(--border);
    transition: 0.2s;
}
.toggle-role a:hover {
    background: #e2e8f0;
}
.toggle-role a.active {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
    border-color: var(--primary);
}

/* FORM GRID */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 16px;
}
.full-width {
    grid-column: 1 / -1;
}

/* INPUTS */
.input-group {
    margin-bottom: 16px;
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

.register-box input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    color: var(--text-main);
    transition: all 0.2s ease;
}

.register-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: white;
}

/* BUTTON */
.btn-submit {
    width: 100%;
    padding: 14px;
    border: none;
    background: var(--primary);
    color: white;
    font-size: 15px;
    font-weight: 600;
    border-radius: var(--radius-md);
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.btn-submit:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

/* LOGIN LINK */
.login-link {
    margin-top: 24px;
    font-size: 14px;
    text-align: center;
    color: var(--text-light);
}

.login-link a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s ease;
}

.login-link a:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

/* MESSAGES */
.message {
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 20px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.error {
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fca5a5;
}

.success {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #86efac;
}

@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
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
        <a href="login.php">Sign In</a>
        <a href="register.php" class="active">Create Account</a>
    </div>
</nav>

<section class="register-section">

    <div class="register-box">

        <h2>Partner with Us</h2>
        <p class="subtitle">Join as a hiring partner to discover top talent.</p>

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

        <div class="login-link">
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
