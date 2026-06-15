<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../../config/database.php';
require_once '../../controllers/CancellationController.php';

$cancellationController = new CancellationController();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

// Fetch detailed booking information with joins
$sql = "SELECT b.*, r.name as resource_name, t.slot_name 
        FROM bookings b
        JOIN resources r ON b.resource_id = r.id
        JOIN time_slots t ON b.time_slot_id = t.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Booking not found.");
}

// Security Check: Only the student who booked it or an admin can cancel
if ($booking['user_id'] !== $user['id'] && $user['role'] !== 'admin') {
    die("Access denied. You can only cancel your own bookings.");
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason']);
    if (empty($reason)) {
        $error = "Please provide a reason for cancelling.";
    } else {
        $result = $cancellationController->cancel([
            'booking_id' => $booking_id,
            'reason' => $reason,
            'cancelled_by' => $user['id']
        ]);

        if ($result === true) {
            $success = "Booking cancelled successfully.";
            header("refresh:2;url=../bookings/index.php");
        } else {
            $error = "Error during cancellation: " . $result;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cancel Booking - CSB System</title>
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
            <h2>Cancel Booking #<?= htmlspecialchars($booking_id) ?></h2>
        </div>

        <div class="content-card" style="max-width: 600px;">
            <div style="margin-bottom: 20px; font-size: 15px; border-bottom: 1px dashed var(--border-color); padding-bottom: 15px;">
                <p style="margin-bottom: 8px;"><strong>Resource Name:</strong> <span style="color: var(--primary); font-weight: 600;"><?= htmlspecialchars($booking['resource_name']) ?></span></p>
                <p style="margin-bottom: 8px;"><strong>Time Slot:</strong> <?= htmlspecialchars($booking['slot_name']) ?></p>
                <p style="margin-bottom: 8px;"><strong>Booking Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
                <p><strong>Status:</strong> <span class="status <?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span></p>
            </div>

            <?php if ($success): ?>
                <div class="message success"><?= htmlspecialchars($success) ?>. Redirecting to bookings list...</div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST">
                <label for="reason">Reason for Cancellation</label>
                <textarea name="reason" id="reason" rows="4" placeholder="Type the reason why you are cancelling this booking..." required></textarea>
                
                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button type="submit" class="btn btn-delete" style="flex: 1;">Confirm Cancellation</button>
                    <a href="../bookings/index.php" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
