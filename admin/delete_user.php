<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('admin');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Prevent admin from deleting themselves
    if($id == $_SESSION['user_id']){
        header("Location: manage_users.php?error=You cannot delete yourself.");
        exit();
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: manage_users.php");
exit();
?>
