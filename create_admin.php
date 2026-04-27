<?php
include("config/db.php");

// Admin details
$name = "Admin";
$email = "admin@gmail.com";
$password = password_hash("admin@123", PASSWORD_DEFAULT);
$role = "admin";

// Check if admin already exists
$check = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $check);

if(mysqli_num_rows($result) > 0){
    echo "Admin already exists.";
    exit();
}

// Insert admin
$sql = "INSERT INTO users (name, email, password, role)
        VALUES ('$name', '$email', '$password', '$role')";

if(mysqli_query($conn, $sql)){
    echo "Admin account created successfully.";
}else{
    echo "Error: " . mysqli_error($conn);
}
?>