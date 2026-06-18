<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $controller->store($_POST);

    if ($result === true) {
        $message = "Time slot created successfully!";
        $class = "success";
    } else {
        $message = "Failed to create time slot!";
        $class = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Time Slot - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Create New Time Slot</h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <?php if($message): ?>
                <div class="message <?= $class ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- Slot Name -->
                <label>Slot Name</label>
                <input type="text" name="slot_name" id="slot_name" placeholder="e.g. Slot 1 (Mon)" required>

                <!-- Start Time -->
                <label>Start Time</label>
                <input type="time" name="start_time" id="start_time" required>

                <!-- End Time -->
                <label>End Time</label>
                <input type="time" name="end_time" id="end_time" required>

                <!-- Day of Week (Integer value fix) -->
                <label>Day of Week</label>
                <select name="day_of_week" required>
                    <option value="1">Monday</option>
                    <option value="2">Tuesday</option>
                    <option value="3">Wednesday</option>
                    <option value="4">Thursday</option>
                    <option value="5">Friday</option>
                    <option value="6">Saturday</option>
                    <option value="7">Sunday</option>
                </select>

                <!-- Peak Hour -->
                <label>Peak Hour</label>
                <select name="is_peak_hour">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Create Time Slot</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    let slot_name = document.getElementById('slot_name').value.trim();
    let start_time = document.getElementById('start_time').value;
    let end_time = document.getElementById('end_time').value;
    
    if (!slot_name) {
        e.preventDefault();
        alert('Slot Name cannot be empty.');
        return;
    }

    if (start_time && end_time) {
        if (end_time <= start_time) {
            e.preventDefault();
            alert('End time must be after start time.');
            return;
        }
    }
});
</script>

</body>
</html>