<?php
include("../config/db.php");

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roll_no = $_POST['roll_no'];
    $department = $_POST['department'];
    $semester = $_POST['semester'];

    // Insert into users table
    $userQuery = "INSERT INTO users (name, email, password, role)
                  VALUES ('$name', '$email', '$password', 'student')";

    if (mysqli_query($conn, $userQuery)) {

        $user_id = mysqli_insert_id($conn);

        // Insert into students table
        $studentQuery = "INSERT INTO students (user_id, roll_no, department, semester)
                         VALUES ('$user_id', '$roll_no', '$department', '$semester')";

        mysqli_query($conn, $studentQuery);

        echo "Student registered successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
