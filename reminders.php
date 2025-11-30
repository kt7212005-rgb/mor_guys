<?php
require 'config.php';
require 'header.php';

// Handle Add Reminder
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']=='add'){
    $stmt = $pdo->prepare("INSERT INTO reminders (title, description, reminder_date, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['reminder_date'],
        'pending'
    ]);
    header("Location: reminders.php");
    exit;
}

// Handle Delete Reminder
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM reminders WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: reminders.php");
    exit;
}

// Handle Mark Completed
if(isset($_GET['complete'])){
    $stmt = $pdo->prepare("UPDATE reminders SET status='completed' WHERE id=?");
    $stmt->execute([$_GET['complete']]);
    header("Location: reminders.php");
    exit;
}

// Fetch all reminders
$reminders = $pdo->query("SELECT * FROM reminders ORDER BY reminder_date ASC")->fetchAll();
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
    <h2>Reminders</h2>

    <!-- Add Reminder Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>Add New Reminder</h5>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Reminder Date & Time</label>
                    <input type="datetime-local" name="reminder_date" class="form-control" required>
                </div>
                <button class="btn btn-success">Add Reminder</button>
            </form>
        </div>
    </div>

    <!-- List of Reminders -->
    <div class="card">
        <div class="card-body">
            <h5>Upcoming Reminders</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($reminders)>0): ?>
                        <?php foreach($reminders as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['title']) ?></td>
                                <td><?= htmlspecialchars($r['description']) ?></td>
                                <td><?= date('M d, Y g:i A', strtotime($r['reminder_date'])) ?></td>
                                <td>
                                    <span class="badge <?= $r['status']=='pending' ? 'bg-warning' : 'bg-success' ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($r['status']=='pending'): ?>
                                        <a href="?complete=<?= $r['id'] ?>" class="btn btn-sm btn-success">Complete</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this reminder?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No reminders yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
