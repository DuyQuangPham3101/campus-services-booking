<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();
$id = $_GET['id'];
$timeslot = $controller->show($id);

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $controller->update($id, $_POST);

    if ($result) {
        $message = "Time slot updated successfully!";
        $class = "success";
        $timeslot = $controller->show($id);
    } else {
        $message = "Failed to update time slot!";
        $class = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Time Slot - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Edit Time Slot #<?= htmlspecialchars($id) ?></h2>
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
                <input type="text" name="slot_name" value="<?= htmlspecialchars($timeslot['slot_name']) ?>" required>

                <!-- Start Time -->
                <label>Start Time</label>
                <input type="time" name="start_time" value="<?= htmlspecialchars($timeslot['start_time']) ?>" required>

                <!-- End Time -->
                <label>End Time</label>
                <input type="time" name="end_time" value="<?= htmlspecialchars($timeslot['end_time']) ?>" required>

                <!-- Day of Week (Integer value fix) -->
                <label>Day of Week</label>
                <select name="day_of_week" required>
                    <option value="1" <?= $timeslot['day_of_week'] == 1 ? 'selected' : '' ?>>Monday</option>
                    <option value="2" <?= $timeslot['day_of_week'] == 2 ? 'selected' : '' ?>>Tuesday</option>
                    <option value="3" <?= $timeslot['day_of_week'] == 3 ? 'selected' : '' ?>>Wednesday</option>
                    <option value="4" <?= $timeslot['day_of_week'] == 4 ? 'selected' : '' ?>>Thursday</option>
                    <option value="5" <?= $timeslot['day_of_week'] == 5 ? 'selected' : '' ?>>Friday</option>
                    <option value="6" <?= $timeslot['day_of_week'] == 6 ? 'selected' : '' ?>>Saturday</option>
                    <option value="7" <?= $timeslot['day_of_week'] == 7 ? 'selected' : '' ?>>Sunday</option>
                </select>

                <!-- Peak Hour -->
                <label>Peak Hour</label>
                <select name="is_peak_hour">
                    <option value="0" <?= $timeslot['is_peak_hour'] == 0 ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= $timeslot['is_peak_hour'] == 1 ? 'selected' : '' ?>>Yes</option>
                </select>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Update Time Slot</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>