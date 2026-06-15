<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../../controllers/BookingController.php';

$controller = new BookingController();
$id = $_GET['id'];
$booking = $controller->getBooking($id);
$users = $controller->getUsers();
$resources = $controller->getResources();
$timeSlots = $controller->getTimeSlots();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->updateBooking($id, $_POST);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking - CSB System</title>
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
            <h2>Edit Booking #<?= htmlspecialchars($id) ?></h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <form method="POST">
                <!-- Select User -->
                <label>Select User</label>
                <select name="user_id" required>
                    <option value="">Choose a user...</option>
                    <?php while($u = $users->fetch_assoc()): ?>
                        <option value="<?= $u['id'] ?>" <?= ($booking['user_id'] == $u['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Select Resource -->
                <label>Select Resource</label>
                <select name="resource_id" required>
                    <option value="">Choose a room, lab, or studio...</option>
                    <?php while($resource = $resources->fetch_assoc()): ?>
                        <option value="<?= $resource['id'] ?>" <?= ($booking['resource_id'] == $resource['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($resource['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Select Time Slot -->
                <label>Select Time Slot</label>
                <select name="time_slot_id" required>
                    <option value="">Choose a time slot...</option>
                    <?php while($slot = $timeSlots->fetch_assoc()): ?>
                        <option value="<?= $slot['id'] ?>" <?= ($booking['time_slot_id'] == $slot['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($slot['slot_name']) ?> (<?= date("H:i", strtotime($slot['start_time'])) ?> - <?= date("H:i", strtotime($slot['end_time'])) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Booking Date -->
                <label>Booking Date</label>
                <input type="date" name="booking_date" value="<?= htmlspecialchars($booking['booking_date']) ?>" required>

                <!-- Status -->
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?= ($booking['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($booking['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($booking['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                    <option value="cancelled" <?= ($booking['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Update Booking</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>