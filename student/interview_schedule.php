<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('student');

$user = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT jobs.title, interviews.date, interviews.location
    FROM interviews
    JOIN jobs ON interviews.job_id = jobs.id
    WHERE interviews.student_id = ?
    ORDER BY interviews.date ASC
");
mysqli_stmt_bind_param($stmt, "i", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php include("includes/student_header.php"); ?>

<div class="dashboard-card glass-card animate-up">
    <div class="mb-4">
        <h2 class="fw-800 text-secondary mb-1">Interview Schedule</h2>
        <p class="text-muted small">Upcoming interviews and selection rounds.</p>
    </div>

    <div class="table-responsive">
        <table class="table table-premium">
            <thead>
                <tr>
                    <th>Company / Role</th>
                    <th>Interview Date</th>
                    <th>Location / Mode</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($row['title']); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="action-icon bg-blue-100 text-primary small" style="width: 32px; height: 32px; font-size: 12px;"><i class="fa-regular fa-calendar"></i></div>
                                    <div><?php echo date("d M Y", strtotime($row['date'])); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="text-secondary"><i class="fa-solid fa-location-dot me-1 text-muted"></i> <?php echo e($row['location']); ?></div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center py-5 text-muted">No interviews scheduled yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/student_footer.php"); ?>