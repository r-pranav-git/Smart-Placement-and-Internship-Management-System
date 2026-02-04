<?php
session_start();
include("../config/db.php");

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'student') {
            header("Location: ../student/dashboard.php");
        } elseif ($user['role'] == 'recruiter') {
            header("Location: ../recruiter/dashboard.php");
        } else {
            header("Location: ../admin/dashboard.php");
        }

    } else {
        echo "Invalid login credentials";
    }
}
?>
