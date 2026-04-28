<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('admin');

$id = intval($_GET['id']);

$sql = "UPDATE users SET status='approved' WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_recruiters.php");
exit();
