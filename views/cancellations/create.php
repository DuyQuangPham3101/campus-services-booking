<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../../controllers/CancellationController.php';
require_once '../../controllers/BookingController.php';

$cancellationController = new CancellationController();
$bookingController = new BookingController();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$booking = $bookingController->getBooking($booking_id);

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
            // Redirect after 2 seconds
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
    <title>Cancel Booking</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<div class="container" style="width: 500px; margin-top: 50px;">
    <h2>Cancel Booking #<?= $booking_id ?></h2>
    <p><strong>Resource ID:</strong> <?= $booking['resource_id'] ?></p>
    <p><strong>Booking Date:</strong> <?= $booking['booking_date'] ?></p>
    <p><strong>Current Status:</strong> <span class="status <?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span></p>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?>. Redirecting...</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <label for="reason">Reason for Cancellation</label>
        <textarea name="reason" id="reason" rows="4" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 10px;" required placeholder="Type the reason why you are cancelling this booking..."></textarea>
        
        <div style="margin-top: 20px; display: flex; justify-content: space-between;">
            <button type="submit" class="btn btn-delete" style="width: 45%;">Confirm Cancel</button>
            <a href="../bookings/index.php" class="btn btn-edit" style="width: 45%; text-align: center; line-height: 20px;">Back to List</a>
        </div>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
