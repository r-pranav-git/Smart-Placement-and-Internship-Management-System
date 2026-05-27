mysqli_begin_transaction($conn);

try {
    // check if already applied
    $check = mysqli_prepare($conn, "SELECT id FROM applications WHERE student_id=? AND job_id=?");
    mysqli_stmt_bind_param($check, "ii", $student_id, $job_id);
    mysqli_stmt_execute($check);

    $result = mysqli_stmt_get_result($check);

    if(mysqli_num_rows($result) > 0){
        throw new Exception("Already applied");
    }

    // insert application
    $insert = mysqli_prepare($conn, "INSERT INTO applications(student_id, job_id) VALUES(?, ?)");
    mysqli_stmt_bind_param($insert, "ii", $student_id, $job_id);
    mysqli_stmt_execute($insert);

    mysqli_commit($conn);

} catch (Exception $e) {
    mysqli_rollback($conn);
}