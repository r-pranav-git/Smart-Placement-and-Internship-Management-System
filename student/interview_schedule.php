<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('student');

$user = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT i.*, j.title, r.company_name AS company
    FROM interviews i
    JOIN jobs j ON i.job_id = j.id
    JOIN recruiters r ON j.recruiter_id = r.user_id
    WHERE i.student_id = ?
    ORDER BY i.date ASC
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
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['company']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($row['title']); ?></div>
                            </td>
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