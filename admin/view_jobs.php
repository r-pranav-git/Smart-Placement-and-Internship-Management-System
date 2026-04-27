<?php
include("../config/db.php");
include("../includes/admin_navbar.php");

$sql="SELECT jobs.*,users.name
FROM jobs
JOIN users ON jobs.recruiter_id=users.id";

$result=mysqli_query($conn,$sql);
?>

<h2 class="page-title">Placement Drives</h2>

<table class="table table-bordered bg-white">

<tr>
<th>Job Title</th>
<th>Company</th>
<th>Deadline</th>
<th>Action</th>
</tr>

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

</table>

<?php include("../includes/admin_footer.php"); ?>