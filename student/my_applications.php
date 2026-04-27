<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('student');

$user = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT jobs.title, applications.status
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    WHERE applications.student_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php include("includes/student_header.php"); ?>

<div class="dashboard-card glass-card animate-up">
    <div class="mb-4">
        <h2 class="fw-800 text-secondary mb-1">My Applications</h2>
        <p class="text-muted small">Track the status of your active job applications.</p>
    </div>

    <div class="table-responsive">
        <table class="table table-premium">
            <thead>
                <tr>
                    <th>Opportunity / Job Role</th>
                    <th>Application Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $statusClass = strtolower($row['status']);
                    ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($row['title']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo e(ucfirst($row['status'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="text-center py-5 text-muted">You haven't applied to any roles yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/student_footer.php"); ?>