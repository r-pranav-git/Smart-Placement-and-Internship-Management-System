<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('admin');

include("../includes/admin_navbar.php");

// data
$sql = "SELECT * FROM users WHERE role='recruiter'";
$result = mysqli_query($conn, $sql);
?>

<div class="dashboard-header">
    <div class="dashboard-title">
        <h2>Manage Recruiters</h2>
        <p>View and manage all registered recruiters.</p>
    </div>
</div>

<div class="dashboard-card">

    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th style="width:120px;">Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo e($row['name']); ?></td>
                <td><?php echo e($row['email']); ?></td>
                <td>
                    <a class="btn btn-danger btn-sm"
                       href="delete_user.php?id=<?php echo $row['id']; ?>"
                       onclick="return confirm('Are you sure you want to delete this recruiter?')">
                       Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

<?php include("../includes/admin_footer.php"); ?>