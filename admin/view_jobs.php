<?php
include("../config/db.php");
include("../includes/admin_navbar.php");

$sql="SELECT jobs.*,users.name
FROM jobs
JOIN users ON jobs.recruiter_id=users.id";

$result=mysqli_query($conn,$sql);
?>

<div class="dashboard-header">
    <div class="dashboard-title">
        <h2>Placement Drives</h2>
        <p>Review and manage all published opportunities.</p>
    </div>
</div>

<div class="dashboard-card">
<table class="table align-middle">

<thead>
    <tr>
        <th>Job Title</th>
        <th>Company</th>
        <th>Deadline</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>

<?php
while($row=mysqli_fetch_assoc($result))
{
?>

<tr>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['deadline']; ?></td>

<td>
<a class="btn btn-danger btn-sm"
href="delete_job.php?id=<?php echo $row['id']; ?>">
Delete
</a>
</td>

</tr>

<?php } ?>

</tbody>
</table>
</div>

<?php include("../includes/admin_footer.php"); ?>