<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('admin');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM jobs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: view_jobs.php");
exit();
?>