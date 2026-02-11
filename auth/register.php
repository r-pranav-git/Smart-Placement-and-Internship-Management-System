<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roll_no = trim($_POST['roll_no']);
    $department = trim($_POST['department']);
    $semester = (int) $_POST['semester'];

    // Check if email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {

        // Start transaction (important)
        mysqli_begin_transaction($conn);

        try {

            // Insert into users table
            $stmt1 = mysqli_prepare($conn, 
                "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')"
            );
            mysqli_stmt_bind_param($stmt1, "sss", $name, $email, $password);
            mysqli_stmt_execute($stmt1);

            $user_id = mysqli_insert_id($conn);

            // Insert into students table
            $stmt2 = mysqli_prepare($conn, 
                "INSERT INTO students (user_id, roll_no, department, semester) 
                 VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, "issi", $user_id, $roll_no, $department, $semester);
            mysqli_stmt_execute($stmt2);

            // Commit transaction
            mysqli_commit($conn);

            $success = "Registration successful! You can now login.";

        } catch (Exception $e) {

            mysqli_rollback($conn);
            $error = "Registration failed. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
</head>
<body>

<h2>Student Registration</h2>

<?php 
if(isset($error)) echo "<p style='color:red;'>$error</p>"; 
if(isset($success)) echo "<p style='color:green;'>$success</p>"; 
?>

<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <input type="text" name="roll_no" placeholder="Roll Number" required><br><br>
    <input type="text" name="department" placeholder="Department" required><br><br>
    <input type="number" name="semester" placeholder="Current Semester" required><br><br>
    <button type="submit">Register</button>
</form>

</body>
</html>
