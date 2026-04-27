<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('recruiter');

if(!isset($_GET['id'])){
    die("Student ID required.");
}

$id = (int)$_GET['id'];

// Verify student exists and has a resume
$stmt = mysqli_prepare($conn, "SELECT resume FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($res);

if(!$student || empty($student['resume'])){
    die("Resume not found for this student.");
}

$resume = $student['resume'];
$filepath = "../resumes/" . $resume;

if(file_exists($filepath)){
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit();
} else {
    die("File does not exist on server.");
}
?>