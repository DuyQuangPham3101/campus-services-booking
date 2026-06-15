<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../../controllers/BookingController.php';

$controller = new BookingController();
$users = $controller->getUsers();
$resources = $controller->getResources();
$timeSlots = $controller->getTimeSlots();

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If student, force their own user_id
    if ($user['role'] === 'student') {
        $_POST['user_id'] = $user['id'];
    }
    
    // Default status if not provided or student
    if (!isset($_POST['status']) || $user['role'] === 'student') {
        $_POST['status'] = 'pending';
    }

    $result = $controller->store($_POST);

    if ($result === true) {
        $message = "Booking created successfully!";
        $class = "success";
    } else {
        $message = $result;
        $class = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Booking - CSB System</title>
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
            <h2>Book New Appointment</h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <?php if($message): ?>
                <div class="message <?= $class ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- User Field (Hidden for students, selector for staff) -->
                <?php if ($user['role'] === 'student'): ?>
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <?php else: ?>
                    <label>Select User</label>
                    <select name="user_id" required>
                        <option value="">Choose a user...</option>
                        <?php while($u = $users->fetch_assoc()): ?>
                            <option value="<?= $u['id'] ?>" <?= (isset($_POST['user_id']) && $_POST['user_id'] == $u['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                <?php endif; ?>

                <!-- Resource Field -->
                <label>Select Resource</label>
                <select name="resource_id" required>
                    <option value="">Choose a room, lab, or studio...</option>
                    <?php while($resource = $resources->fetch_assoc()): ?>
                        <option value="<?= $resource['id'] ?>" <?= (isset($_POST['resource_id']) && $_POST['resource_id'] == $resource['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($resource['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Time Slot Field -->
                <label>Select Time Slot</label>
                <select name="time_slot_id" required>
                    <option value="">Choose a time slot...</option>
                    <?php while($slot = $timeSlots->fetch_assoc()): ?>
                        <option value="<?= $slot['id'] ?>" <?= (isset($_POST['time_slot_id']) && $_POST['time_slot_id'] == $slot['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($slot['slot_name']) ?> (<?= date("H:i", strtotime($slot['start_time'])) ?> - <?= date("H:i", strtotime($slot['end_time'])) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Date Field -->
                <label>Booking Date</label>
                <input type="date" name="booking_date" value="<?= $_POST['booking_date'] ?? '' ?>" required>

                <!-- Status Field (Hidden for students, visible for admins/lecturers) -->
                <?php if ($user['role'] !== 'student'): ?>
                    <label>Status</label>
                    <select name="status">
                        <option value="pending" <?= (isset($_POST['status']) && $_POST['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= (isset($_POST['status']) && $_POST['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                    </select>
                <?php endif; ?>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Confirm Booking</button>
                    <a href="index.php" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>