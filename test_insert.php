<?php
include('config/db.php');

$stmt1 = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
$name = 'Test User';
$email = 'test' . rand() . '@test.com';
$password = '123';
mysqli_stmt_bind_param($stmt1, "sss", $name, $email, $password);
if(!mysqli_stmt_execute($stmt1)){
    die("User insert failed: " . mysqli_stmt_error($stmt1));
}
$user_id = mysqli_insert_id($conn);

$stmt2 = mysqli_prepare($conn, "INSERT INTO students (user_id, roll_no, department, semester) VALUES (?, ?, ?, ?)");
$roll_no = 'ROLL' . rand();
$department = 'CS';
$semester = 6;
mysqli_stmt_bind_param($stmt2, "issi", $user_id, $roll_no, $department, $semester);
if(!mysqli_stmt_execute($stmt2)){
    echo "Student insert failed: " . mysqli_stmt_error($stmt2);
} else {
    echo "Success! Inserted user $user_id and student record.";
}
?>
