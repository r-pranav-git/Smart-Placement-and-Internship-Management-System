<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

require_role('admin');

if(isset($_POST['upload']))
{
    verify_csrf_token($_POST['csrf_token']);
    
    if(!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK){
        $error = "File upload error.";
    } else {
        $file = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        // Allow common document formats
        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];
        if(in_array($ext, $allowed)){
            $new_name = "resource_" . time() . "_" . $file;
            if(move_uploaded_file($tmp, "../resources/" . $new_name)){
                $success = "Resource '$file' uploaded successfully!";
            } else {
                $error = "Failed to save file to server.";
            }
        } else {
            $error = "Invalid file format.";
        }
    }
}
?>

<?php include("../includes/admin_navbar.php"); ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="dashboard-card glass-card animate-up">
                <div class="text-center mb-4">
                    <div class="action-icon bg-blue-100 text-primary mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <h2 class="fw-800 mb-1" style="font-size: 24px;">Upload Resources</h2>
                    <p class="text-muted small">Share preparation materials and guides with students.</p>
                </div>

                <?php if(isset($success)) echo "<div class='alert alert-success small animate-up'>$success</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert alert-danger small animate-up'>$error</div>"; ?>

                <form method="POST" enctype="multipart/form-data">
                    <?php csrf_field(); ?>
                    <div class="mb-4">
                        <label class="form-label fw-600 small">Select Document</label>
                        <input type="file" name="file" class="form-control" style="border-radius: 10px;" required>
                        <div class="form-text extra-small">Allowed: PDF, DOCX, PPTX, XLSX</div>
                    </div>

                    <button class="btn-premium w-100" name="upload">Start Upload</button>
                    <a href="../admin_dashboard.php" class="btn btn-light border w-100 mt-2" style="border-radius: 12px; padding: 12px;">Back to Dashboard</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/admin_footer.php"); ?>