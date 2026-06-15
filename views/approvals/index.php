<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];
if ($user['role'] !== 'admin' && $user['role'] !== 'lecturer') {
    die("Access denied. Only lecturers and admins can approve bookings.");
}

require_once '../../controllers/ApprovalController.php';
require_once '../../controllers/BookingController.php';

$approvalController = new ApprovalController();
$bookingController = new BookingController();

$message = "";
$error = "";

// Handle approval/rejection request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $booking_id = (int)$_POST['booking_id'];
    $action = $_POST['action']; // 'approved' or 'rejected'
    $note = $_POST['note'];

    $result = $approvalController->approve([
        'booking_id' => $booking_id,
        'approved_by' => $user['id'],
        'status' => $action,
        'note' => $note
    ]);

    if ($result === true) {
        $message = "Booking #$booking_id has been successfully " . ($action === 'approved' ? 'approved' : 'rejected') . ".";
    } else {
        $error = "Error processing approval: " . $result;
    }
}

// Fetch all bookings (to filter pending ones in the UI)
$bookings = $bookingController->index();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Booking Approvals</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .approval-form {
            display: inline-block;
            margin: 0;
        }
        .note-input {
            width: 150px;
            padding: 5px;
            margin-right: 10px;
            margin-top: 0;
            margin-bottom: 0;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h1>Pending Approvals Queue</h1>
        <div>
            <a href="../../public/dashboard.php">
                <button class="btn btn-edit">Dashboard</button>
            </a>
            <a href="../../public/logout.php">
                <button class="btn btn-delete">Logout</button>
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Resource</th>
            <th>Time Slot</th>
            <th>Date</th>
            <th>Current Status</th>
            <th>Approve/Reject Notes</th>
            <th>Actions</th>
        </tr>

        <?php 
        $pending_count = 0;
        while($row = $bookings->fetch_assoc()): 
            if ($row['status'] !== 'pending') continue;
            $pending_count++;
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td>Resource #<?= $row['resource_id'] ?></td>
            <td>Slot #<?= $row['time_slot_id'] ?></td>
            <td><?= $row['booking_date'] ?></td>
            <td>
                <span class="status pending">Pending</span>
            </td>
            <td style="width: 300px;">
                <form method="POST" id="form-<?= $row['id'] ?>">
                    <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                    <input type="text" name="note" placeholder="Enter notes..." class="note-input" required>
                </form>
            </td>
            <td>
                <button type="submit" name="action" value="approved" form="form-<?= $row['id'] ?>" class="btn btn-create" style="padding: 6px 12px; font-size: 14px;">
                    Approve
                </button>
                <button type="submit" name="action" value="rejected" form="form-<?= $row['id'] ?>" class="btn btn-delete" style="padding: 6px 12px; font-size: 14px;">
                    Reject
                </button>
            </td>
        </tr>
        <?php endwhile; ?>

        <?php if ($pending_count === 0): ?>
        <tr>
            <td colspan="8">No pending bookings require approval at this time.</td>
        </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
