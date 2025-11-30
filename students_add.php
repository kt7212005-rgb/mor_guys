<?php
require 'config.php'; // Load database first
require 'header.php'; // Load header next
$log = $pdo->prepare("
    INSERT INTO activity_log (activity, user, status) 
    VALUES (?, ?, ?)
");
$log->execute([
    "New student registered: ",
    "Admin",
    "Completed"
]);

echo <<<HTML
<!DOCTYPE html>
<html>
<body>

<div class="form-card" 
     style="
        margin-left: 280px; 
        max-width: calc(100% - 280px); 
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        margin-top: 20px;
     ">

    <div class="form-card-header"
         style="
            font-size: 22px; 
            font-weight: bold; 
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0a5517ff;
            color: #0a5517ff;
         ">
        Add Student
    </div>

    <div class="form-card-body" style="font-size: 15px;">

        <form action="students_save.php" method="post" enctype="multipart/form-data">

            <label class="form-label" style="font-weight: bold;">Student No</label>
            <input name="student_no" class="form-control" required
                   style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc;">

            <div class="row" style="display: flex; gap: 20px;">

                <div class="col" style="flex: 1;">
                    <label class="form-label" style="font-weight: bold;">First Name</label>
                    <input name="first_name" class="form-control" required
                           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px;">
                </div>

                <div class="col" style="flex: 1;">
                    <label class="form-label" style="font-weight: bold;">Last Name</label>
                    <input name="last_name" class="form-control" required
                           style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px;">
                </div>

            </div>

            <label class="form-label" style="font-weight: bold;">Student Photo</label>
            <input type="file" name="profile_image" accept="image/*"
                   style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc;">

            <label class="form-label" style="font-weight: bold;">Grade Level</label>
            <textarea name="immunizations" 
                      style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; height: 60px; margin-bottom: 15px;"></textarea>

            <label class="form-label" style="font-weight: bold;">Contact</label>
            <input name="contact"
                   style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px;">

            <label class="form-label" style="font-weight: bold;">Address</label>
            <input name="address"
                   style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px;">

            <h5 style="margin-top: 25px; color: #0a5517ff;">Medical Information</h5>

            <label class="form-label" style="font-weight: bold;">Allergies</label>
            <textarea name="allergies"
                      style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; height: 80px; margin-bottom: 15px;"></textarea>

            <label class="form-label" style="font-weight: bold;">Contact of Guardian/Parent</label>
            <textarea name="guardian_contact"
                      style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; height: 60px; margin-bottom: 15px;"></textarea>

            <label class="form-label" style="font-weight: bold;">If condition triggered - What to do?</label>
            <textarea name="emergency_action"
                      style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; height: 80px; margin-bottom: 25px;"></textarea>

            <button type="submit" 
                    style="
                        background: #0a5517ff; 
                        color: white; 
                        padding: 10px 20px; 
                        border: none;
                        border-radius: 6px;
                        cursor: pointer;
                    ">
                Save Student
            </button>

            <a href="students.php" 
               style="
                    background: #dc3545; 
                    color: white; 
                    padding: 10px 20px; 
                    border-radius: 6px; 
                    text-decoration: none;
                    margin-left: 10px;
               ">
               Cancel
            </a>

        </form>

    </div>
</div>

</body>
</html>
HTML;
?>
