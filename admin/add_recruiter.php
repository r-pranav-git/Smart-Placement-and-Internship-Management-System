<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");
include("../includes/admin_navbar.php");

require_role('admin');

if(isset($_POST['add']))
{
    verify_csrf_token($_POST['csrf_token']);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'recruiter', 'approved')");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
    
    if(mysqli_stmt_execute($stmt)){
        $msg = "Recruiter account created successfully!";
    } else {
        $error = "Failed to create account. Email might already exist.";
    }
}
?>

<div class="dashboard-header">
    <div class="dashboard-title">
        <h2>Add New Recruiter</h2>
        <p>Onboard a new hiring partner manually.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="dashboard-card glass-card animate-up">
            <div class="text-center mb-4">
                <div class="action-icon bg-blue-100 text-primary mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
            </div>

            <?php if(isset($msg)){ ?>
                <div class="alert alert-success small animate-up"><?php echo $msg; ?></div>
            <?php } ?>
            <?php if(isset($error)){ ?>
                <div class="alert alert-danger small animate-up"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST">
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label fw-600 small">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="recruiter@company.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small">Temporary Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button class="btn-premium w-100" name="add">Create Account</button>
                <a href="manage_recruiters.php" class="btn btn-light border w-100 mt-2" style="border-radius: 12px; padding: 12px;">Back to List</a>
            </form>
        </div>
    </div>
</div>

<?php include("../includes/admin_footer.php"); ?>