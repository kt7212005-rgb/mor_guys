
<?php
require 'config.php';


// Original finance queries (unchanged)
$totalIncome = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM reports WHERE type='Income'")->fetchColumn();
$totalExpense = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM reports WHERE type='Expense'")->fetchColumn();
$balance = $totalIncome - $totalExpense;

// Handle form submissions for new entries (basic insert logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (isset($_POST['add_work_minute'])) {
        $stmt = $pdo->prepare("INSERT INTO work_minutes (employee_name, work_date, hours_worked, task_description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['employee_name'], $_POST['work_date'], $_POST['hours_worked'], $_POST['task_description']]);
    } elseif (isset($_POST['add_meeting'])) {
        $stmt = $pdo->prepare("INSERT INTO meetings (meeting_date, title, attendees, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['meeting_date'], $_POST['title'], $_POST['attendees'], $_POST['notes']]);
    } elseif (isset($_POST['add_note'])) {
        $stmt = $pdo->prepare("INSERT INTO notes (note_date, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['note_date'], $_POST['title'], $_POST['content']]);
    } elseif (isset($_POST['add_feedback'])) {
        $stmt = $pdo->prepare("INSERT INTO feedbacks_complaints (submission_date, type, name, email, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['submission_date'], $_POST['type'], $_POST['name'], $_POST['email'], $_POST['message']]);
    } elseif (isset($_POST['add_faculty'])) {
        $stmt = $pdo->prepare("INSERT INTO faculty_management (name, department, position, hire_date, contact_info) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['department'], $_POST['position'], $_POST['hire_date'], $_POST['contact_info']]);
    } elseif (isset($_POST['add_medical_record'])) {
        $stmt = $pdo->prepare("INSERT INTO student_medical_records (student_name, student_id, record_date, medical_condition, pwd_status, pwd_details, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['student_name'], $_POST['student_id'], $_POST['record_date'], $_POST['medical_condition'], $_POST['pwd_status'], $_POST['pwd_details'], $_POST['notes']]);
    }
    // Redirect to avoid resubmission on refresh
    header("Location: reports.php");
    exit;
}
require 'header.php';
?>

<link rel="stylesheet" href="assets/css/reports.css">

<style>
.reports-wrapper {
    margin-left: 280px;
    max-width: calc(100% - 280px);
    padding: 20px;
}
</style>

  <?php
    $stmt = $pdo->query("SELECT * FROM reports ORDER BY entry_date DESC LIMIT 20");
    foreach ($stmt->fetchAll() as $f) {
      echo "<tr><td>{$f['entry_date']}</td><td>{$f['type']}</td><td>" . htmlspecialchars($f['category']) . "</td><td>" . number_format($f['amount'], 2) . "</td><td>" . htmlspecialchars($f['description']) . "</td></tr>";
    }
  ?>
  </tbody>
</table>

<hr>

<!-- Tabbed Interface for All Sections -->
<div class="reports-wrapper">
  <div class="reports-header">
    <h1><i class="bi bi-graph-up me-2"></i>Reports & Analytics</h1>
    <button class="btn-print" onclick="window.print()">
      <i class="bi bi-printer me-2"></i>Print Reports
    </button>
  </div>
  
  <h4>Reports Sections</h4>
  <ul class="nav nav-tabs bg-light p-2" id="reportsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="work-minutes-tab" data-bs-toggle="tab" data-bs-target="#work-minutes" type="button">Work Minutes</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="meetings-tab" data-bs-toggle="tab" data-bs-target="#meetings" type="button">Meetings</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button">Notes</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="feedbacks-tab" data-bs-toggle="tab" data-bs-target="#feedbacks" type="button">Feedbacks & Complaints</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="faculty-tab" data-bs-toggle="tab" data-bs-target="#faculty" type="button">Faculty Management</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button">Student Medical Records</button>
    </li>
</ul>

  <div class="tab-content" id="reportsTabsContent">
    <!-- Work Minutes Tab -->
    <div class="tab-pane fade show active" id="work-minutes" role="tabpanel" aria-labelledby="work-minutes-tab">
      <h5>Employee Work Tracking</h5>
      <form method="POST" class="mb-3">
        <div class="row">
          <div class="col-md-3"><input type="text" name="employee_name" class="form-control" placeholder="Employee Name" required></div>
          <div class="col-md-3"><input type="date" name="work_date" class="form-control" required></div>
          <div class="col-md-2"><input type="number" step="0.01" name="hours_worked" class="form-control" placeholder="Hours" required></div>
          <div class="col-md-4"><input type="text" name="task_description" class="form-control" placeholder="Task Description"></div>
        </div>
        <button type="submit" name="add_work_minute" class="btn btn-primary mt-2">Add Work Minute</button>
      </form>
      <table class="table table-sm">
        <thead><tr><th>Employee</th><th>Date</th><th>Hours</th><th>Task</th></tr></thead>
        <tbody>
        <?php
          $stmt = $pdo->query("SELECT * FROM work_minutes ORDER BY work_date DESC LIMIT 20");
          foreach ($stmt->fetchAll() as $wm) {
            echo "<tr><td>" . htmlspecialchars($wm['employee_name']) . "</td><td>{$wm['work_date']}</td><td>{$wm['hours_worked']}</td><td>" . htmlspecialchars($wm['task_description']) . "</td></tr>";
          }
        ?>
        </tbody>
      </table>
    </div>

    <!-- Meetings Tab -->
    <div class="tab-pane fade" id="meetings" role="tabpanel" aria-labelledby="meetings-tab">
      <h5>Meeting Records</h5>
      <form method="POST" class="mb-3">
        <div class="row">
          <div class="col-md-3"><input type="date" name="meeting_date" class="form-control" required></div>
          <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Meeting Title" required></div>
          <div class="col-md-3"><input type="text" name="attendees" class="form-control" placeholder="Attendees"></div>
          <div class="col-md-3"><textarea name="notes" class="form-control" placeholder="Notes"></textarea></div>
        </div>
        <button type="submit" name="add_meeting" class="btn btn-primary mt-2">Add Meeting</button>
      </form>
      <table class="table table-sm">
        <thead><tr><th>Date</th><th>Title</th><th>Attendees</th><th>Notes</th></tr></thead>
        <tbody>
        <?php
          $stmt = $pdo->query("SELECT * FROM meetings ORDER BY meeting_date DESC LIMIT 20");
          foreach ($stmt->fetchAll() as $m) {
            echo "<tr><td>{$m['meeting_date']}</td><td>" . htmlspecialchars($m['title']) . "</td><td>" . htmlspecialchars($m['attendees']) . "</td><td>" . htmlspecialchars($m['notes']) . "</td></tr>";
          }
        ?>
        </tbody>
      </table>
    </div>

    <!-- Notes Tab -->
    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
      <h5>General Notes</h5>
      <form method="POST" class="mb-3">
        <div class="row">
          <div class="col-md-3"><input type="date" name="note_date" class="form-control" required></div>
          <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Note Title" required></div>
          <div class="col-md-6"><textarea name="content" class="form-control" placeholder="Note Content"></textarea></div>
        </div>
        <button type="submit" name="add_note" class="btn btn-primary mt-2">Add Note</button>
      </form>
      <table class="table table-sm">
        <thead><tr><th>Date</th><th>Title</th><th>Content</th></tr></thead>
        <tbody>
        <?php
          $stmt = $pdo->query("SELECT * FROM notes ORDER BY note_date DESC LIMIT 20");
          foreach ($stmt->fetchAll() as $n) {
            echo "<tr><td>{$n['note_date']}</td><td>" . htmlspecialchars($n['title']) . "</td><td>" . htmlspecialchars($n['content']) . "</td></tr>";
          }
        ?>
        </tbody>
      </table>
    </div>

    <!-- Feedbacks and Complaints Tab -->
    <div class="tab-pane fade" id="feedbacks" role="tabpanel" aria-labelledby="feedbacks-tab">
      <h5>Feedbacks and Complaints</h5>
      <form method="POST" class="mb-3">
        <div class="row">
          <div class="col-md-2"><input type="date" name="submission_date" class="form-control" required></div>
          <div class="col-md-2"><select name="type" class="form-control" required><option value="Feedback">Feedback</option><option value="Complaint">Complaint</option></select></div>
          <div class="col-md-2"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
          <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email"></div>
          <div class="col-md-3"><textarea name="message" class="form-control" placeholder="Message" required></textarea></div>
        </div>
        <button type="submit" name="add_feedback" class="btn btn-primary mt-2">Add Feedback/Complaint</button>
      </form>
      <table class="table table-sm">
        <thead><tr><th>Date</th><th>Type</th><th>Name</th><th>Email</th><th>Message</th></tr></thead>
        <tbody>
        <?php
          $stmt = $pdo->query("SELECT * FROM feedbacks_complaints ORDER BY submission_date DESC LIMIT 20");
          foreach ($stmt->fetchAll() as $fb) {
            echo "<tr><td>{$fb['submission_date']}</td><td>{$fb['type']}</td><td>" . htmlspecialchars($fb['name']) . "</td><td>" . htmlspecialchars($fb['email']) . "</td><td>" . htmlspecialchars($fb['message']) . "</td></tr>";
          }
        ?>
        </tbody>
      </table>
    </div>

    <!-- Faculty Management Tab -->
    <div class="tab-pane fade" id="faculty" role="tabpanel" aria-labelledby="faculty-tab">
      <h5>Faculty Management</h5>
      <form method="POST" class="mb-3">
        <div class="row">
          <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
          <div class="col-md-3"><input type="text" name="department" class="form-control" placeholder="Department"></div>
          <div class="col-md-2"><input type="text" name="position" class="form-control" placeholder="Position"></div>
          <div class="col-md-2"><input type="date" name="hire_date" class="form-control"></div>
          <div class="col-md-2"><textarea name="contact_info" class="form-control" placeholder="Contact Info"></textarea></div>
        </div>
        <button type="submit" name="add_faculty" class="btn btn-primary mt-2">Add Faculty</button>
      </form>
      <table class="table table-sm">
        <thead><tr><th>Name</th><th>Department</th><th>Position</th><th>Hire Date</th><th>Contact</th></tr></thead>
        <tbody>
        <?php
          $stmt = $pdo->query("SELECT * FROM faculty_management ORDER BY hire_date DESC LIMIT 20");
          foreach ($stmt->fetchAll() as $fac) {
            echo "<tr><td>" . htmlspecialchars($fac['name']) . "</td><td>" . htmlspecialchars($fac['department']) . "</td><td>" . htmlspecialchars($fac['position']) . "</td><td>{$fac['hire_date']}</td><td>" . htmlspecialchars($fac['contact_info']) . "</td></tr>";
          }
        ?>
        </tbody>
      </table>
    </div>

<!-- Student Medical Records Tab -->
<div class="tab-pane fade" id="medical" role="tabpanel" aria-labelledby="medical-tab">
  <h5>Student Medical Records (PWD Tracking)</h5>
  <form method="POST" class="mb-3">
    <div class="row">
      <div class="col-md-2"><input type="text" name="student_name" class="form-control" placeholder="Student Name" required></div>
      <div class="col-md-2"><input type="text" name="student_id" class="form-control" placeholder="Student ID" required></div>
      <div class="col-md-2"><input type="date" name="record_date" class="form-control" required></div>
      <div class="col-md-2"><input type="text" name="medical_condition" class="form-control" placeholder="Medical Condition"></div>
      <div class="col-md-1"><select name="pwd_status" class="form-control" required><option value="No">No</option><option value="Yes">Yes</option></select></div>
      <div class="col-md-2"><textarea name="pwd_details" class="form-control" placeholder="PWD Details"></textarea></div>
      <div class="col-md-1"><textarea name="notes" class="form-control" placeholder="Notes"></textarea></div>
    </div>
    <button type="submit" name="add_medical_record" class="btn btn-primary mt-2">Add Medical Record</button>
  </form>
  <table class="table table-sm">
    <thead><tr><th>Student Name</th><th>Student ID</th><th>Date</th><th>Condition</th><th>PWD Status</th><th>PWD Details</th><th>Notes</th></tr></thead>
    <tbody>
    <?php
      $stmt = $pdo->query("SELECT * FROM student_medical_records ORDER BY record_date DESC LIMIT 20");
      foreach ($stmt->fetchAll() as $med) {
        echo "<tr><td>" . htmlspecialchars($med['student_name']) . "</td><td>" . htmlspecialchars($med['student_id']) . "</td><td>{$med['record_date']}</td><td>" . htmlspecialchars($med['medical_condition']) . "</td><td>{$med['pwd_status']}</td><td>" . htmlspecialchars($med['pwd_details']) . "</td><td>" . htmlspecialchars($med['notes']) . "</td></tr>";
      }
    ?>
    </tbody>
  </table>
</div>
  </div>
</div>

<?php require 'footer.php'; ?>