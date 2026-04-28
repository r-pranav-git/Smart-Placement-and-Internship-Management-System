<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch existing student data using prepared statement */
$stmt_fetch = mysqli_prepare($conn, "SELECT * FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt_fetch, "i", $user_id);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data = mysqli_fetch_assoc($result_fetch);

/* Update profile */
if(isset($_POST['update'])) {
    verify_csrf_token($_POST['csrf_token']);
    
    $roll = trim($_POST['roll_no']);
    $dept = trim($_POST['department']);
    $sem = intval($_POST['semester']);
    $cgpa = floatval($_POST['cgpa']);
    $backlogs = intval($_POST['backlogs']);
    $skills = trim($_POST['skills']);
    $linkedin = trim($_POST['linkedin']);
    $github = trim($_POST['github']);
    $portfolio = trim($_POST['portfolio_url']);

    /* check if student row exists */
    $stmt_check = mysqli_prepare($conn, "SELECT user_id FROM students WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $user_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if(mysqli_stmt_num_rows($stmt_check) > 0) {
        $stmt_update = mysqli_prepare($conn, "
            UPDATE students
            SET roll_no = ?, department = ?, semester = ?, cgpa = ?, backlogs = ?, skills = ?, linkedin = ?, github = ?, portfolio_url = ?
            WHERE user_id = ?
        ");
        mysqli_stmt_bind_param($stmt_update, "ssidissssi", $roll, $dept, $sem, $cgpa, $backlogs, $skills, $linkedin, $github, $portfolio, $user_id);
        mysqli_stmt_execute($stmt_update);
    } else {
        $stmt_insert = mysqli_prepare($conn, "
            INSERT INTO students (user_id, roll_no, department, semester, cgpa, backlogs, skills, linkedin, github, portfolio_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt_insert, "issidissss", $user_id, $roll, $dept, $sem, $cgpa, $backlogs, $skills, $linkedin, $github, $portfolio);
        mysqli_stmt_execute($stmt_insert);
    }

    header("Location: ../student_dashboard.php");
    exit();
}
?>

<?php include("includes/student_header.php"); ?>


<div class="dashboard-card glass-card animate-up">

<h4 class="fw-800 text-primary mb-4"><i class="fa-solid fa-pen-to-square"></i> Edit Academic Profile</h4>

<form method="POST">
    <?php csrf_field(); ?>

<div class="mb-3">
<label class="form-label">Roll Number</label>
<input type="text"
name="roll_no"
class="form-control"
value="<?php echo $data['roll_no'] ?? ''; ?>"
required>
</div>


<div class="mb-3">
<label class="form-label">Department</label>
<input type="text"
name="department"
class="form-control"
value="<?php echo $data['department'] ?? ''; ?>"
required>
</div>


<div class="mb-3">
<label class="form-label">Semester</label>
<input type="number"
name="semester"
class="form-control"
value="<?php echo $data['semester'] ?? ''; ?>"
required>
</div>


<div class="mb-3">
<label class="form-label">CGPA</label>
<input type="number"
step="0.01"
name="cgpa"
class="form-control"
value="<?php echo $data['cgpa'] ?? ''; ?>"
required>
</div>


<div class="mb-3">
<label class="form-label">Backlogs</label>
<input type="number"
name="backlogs"
class="form-control"
value="<?php echo e($data['backlogs'] ?? 0); ?>"
required>
</div>


<div class="mb-3">
<label class="form-label">Key Skills</label>
<textarea name="skills" class="form-control" rows="3" placeholder="e.g. PHP, Python, React, MySQL"><?php echo e($data['skills'] ?? ''); ?></textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">LinkedIn URL</label>
        <input type="url" name="linkedin" class="form-control" value="<?php echo e($data['linkedin'] ?? ''); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">GitHub URL</label>
        <input type="url" name="github" class="form-control" value="<?php echo e($data['github'] ?? ''); ?>">
    </div>
</div>

<div class="mb-3">
<label class="form-label">Portfolio URL</label>
<input type="url" name="portfolio_url" class="form-control" value="<?php echo e($data['portfolio_url'] ?? ''); ?>">
</div>

<button name="update" class="btn-premium me-2">
Update Profile
</button>

<a href="../student_dashboard.php" class="btn btn-light border" style="border-radius: 12px; padding: 12px 24px; font-weight: 600;">
Back to Dashboard
</a>

</form>

</div>


<?php include("includes/student_footer.php"); ?>