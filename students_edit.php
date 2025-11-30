<?php
require 'config.php';
$log = $pdo->prepare("
    INSERT INTO activity_log (activity, user, status) 
    VALUES (?, ?, ?)
");
$log->execute([
    "Student updated: ",
    "Admin",
    "Updated"
]);

// Handle POST first to avoid "headers already sent" issues
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id           = $_POST['id'];
    $student_no   = $_POST['student_no'];
    $first_name   = $_POST['first_name'];
    $last_name    = $_POST['last_name'];
    $contact      = $_POST['contact'] ?? '';
    $address      = $_POST['address'] ?? '';
    $grade_level  = $_POST['grade_level'] ?? '';

    $allergies     = $_POST['allergies'] ?? '';
    $immunizations = $_POST['immunizations'] ?? '';

    try {
        $pdo->beginTransaction();

        // Handle photo upload
        $profile_image = $_POST['existing_profile_image'] ?? null;
        if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $profile_image = $student_no . '_' . time() . '.' . $ext;
            $upload_dir = 'uploads/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $profile_image);
        }

        // Update students table
        $stmtUpdate = $pdo->prepare("UPDATE students 
                                     SET student_no=?, first_name=?, last_name=?, contact=?, address=?, grade_level=?, profile_image=? 
                                     WHERE id=?");
        $stmtUpdate->execute([$student_no, $first_name, $last_name, $contact, $address, $grade_level, $profile_image, $id]);

        // Update or insert student_medical table
        $stmtCheck = $pdo->prepare("SELECT id FROM student_medical WHERE student_id = ?");
        $stmtCheck->execute([$id]);
        $medExists = $stmtCheck->fetch();
        
        if($medExists) {
            $stmtMed = $pdo->prepare("UPDATE student_medical 
                                      SET allergies=?, immunizations=? 
                                      WHERE student_id=?");
            $stmtMed->execute([$allergies, $immunizations, $id]);
        } else {
            $stmtMed = $pdo->prepare("INSERT INTO student_medical (student_id, allergies, immunizations) 
                                      VALUES (?, ?, ?)");
            $stmtMed->execute([$id, $allergies, $immunizations]);
        }

        $pdo->commit();

        // Redirect immediately after POST
        header("Location: students.php?success=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("ERROR: " . $e->getMessage());
    }
}

// Include header AFTER handling POST redirect
require 'header.php';

// Check GET parameter
if (!isset($_GET['id'])) {
    die("No student ID provided.");
}

$id = $_GET['id'];

// Fetch student data
$stmt = $pdo->prepare("SELECT s.*, sm.allergies, sm.immunizations
                       FROM students s 
                       LEFT JOIN student_medical sm ON s.id = sm.student_id 
                       WHERE s.id=?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}
?>

<div style="margin-left:280px; max-width:calc(100% - 280px); border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.1); overflow:hidden; font-family:Arial, sans-serif; background:#fff; padding:20px;">
    
    <!-- Card Header -->
    <div style="background:#0a5517ff; color:white; padding:15px; margin:-20px -20px 20px -20px; display:flex; align-items:center;">
        <span style="font-size:18px; margin-right:10px;"></span>
        <h2 style="margin:0; font-size:18px;">Edit Student</h2>
    </div>

    <form method="post" enctype="multipart/form-data" action="students_edit.php?id=<?= $id ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
        <input type="hidden" name="existing_profile_image" value="<?= htmlspecialchars($student['profile_image'] ?? '') ?>">

        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Student Photo</label>
            <?php if(!empty($student['profile_image']) && file_exists('uploads/' . $student['profile_image'])): ?>
                <div style="margin-bottom:10px;">
                    <img src="uploads/<?= htmlspecialchars($student['profile_image']) ?>" 
                         alt="Current Photo" 
                         style="width:80px; height:80px; object-fit:cover; border-radius:50%; border:2px solid #0a5517ff;">
                </div>
            <?php endif; ?>
            <input type="file" name="profile_image" accept="image/*" class="form-control"
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
            <small style="color:#666;">Leave empty to keep current photo</small>
        </div>

        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Student No</label>
            <input name="student_no" class="form-control" value="<?= htmlspecialchars($student['student_no']) ?>" required
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
        </div>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">First Name</label>
            <input name="first_name" class="form-control" value="<?= htmlspecialchars($student['first_name']) ?>" required
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
        </div>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Last Name</label>
            <input name="last_name" class="form-control" value="<?= htmlspecialchars($student['last_name']) ?>" required
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
        </div>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Contact</label>
            <input name="contact" class="form-control" value="<?= htmlspecialchars($student['contact'] ?? '') ?>"
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
        </div>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Address</label>
            <input name="address" class="form-control" value="<?= htmlspecialchars($student['address'] ?? '') ?>"
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
        </div>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Grade Level</label>
            <input name="grade_level" class="form-control" value="<?= htmlspecialchars($student['grade_level'] ?? '') ?>"
                   style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px;">
        </div>

        <h5 style="margin-top:20px; color:#2c3e50;">Medical Information</h5>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Allergies</label>
            <textarea name="allergies" class="form-control"
                      style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px; min-height:60px;"><?= htmlspecialchars($student['allergies'] ?? '') ?></textarea>
        </div>
        <div class="mb-3" style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Immunizations</label>
            <textarea name="immunizations" class="form-control"
                      style="border-radius:8px; border:1px solid #ced4da; padding:10px; width:100%; font-size:14px; min-height:60px;"><?= htmlspecialchars($student['immunizations'] ?? '') ?></textarea>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-success" style="background:#0a5517ff; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer;">Update Student</button>
            <a href="students.php" class="btn btn-danger" style="background:#dc3545; color:white; padding:10px 20px; border-radius:6px; text-decoration:none; margin-left:10px;">Cancel</a>
        </div>
    </form>
</div>

<?php require 'footer.php'; ?>
