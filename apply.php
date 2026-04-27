<?php
session_start();
require "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'];

// Prevent duplicate application
$check = mysqli_query($conn,"
SELECT * FROM applications 
WHERE job_id='$job_id' AND student_id='$user_id'
");

if(mysqli_num_rows($check) == 0){

    mysqli_query($conn,"
    INSERT INTO applications(job_id,student_id)
    VALUES('$job_id','$user_id')
    ");

}

header("Location: student/dashboard.php");
exit();
?>