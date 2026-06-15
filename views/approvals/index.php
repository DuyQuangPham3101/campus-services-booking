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
    $note = trim($_POST['note']);

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

// Fetch bookings. For lecturers/admins, $bookingController->index(null) will return all bookings with joins.
$bookings = $bookingController->index(null);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approvals Queue - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
    <style>
        .approval-form-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        .note-input {
            margin: 0;
            padding: 8px 12px;
            font-size: 13px;
        }
    </style>
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Pending Approvals Queue</h2>
        </div>

        <div class="content-card">
            <?php if ($message): ?>
                <div class="message success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Resource</th>
                        <th>Time Slot</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                        <th>Approval Note</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $pending_count = 0;
                    while($row = $bookings->fetch_assoc()): 
                        if ($row['status'] !== 'pending') continue;
                        $pending_count++;
                    ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['user_name'] ?? 'User ID: ' . $row['user_id']) ?></strong></td>
                        <td><?= htmlspecialchars($row['resource_name'] ?? 'Resource ID: ' . $row['resource_id']) ?></td>
                        <td><?= htmlspecialchars($row['slot_name'] ?? 'Slot ID: ' . $row['time_slot_id']) ?></td>
                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                        <td>
                            <span class="status pending">Pending</span>
                        </td>
                        <td style="width: 250px;">
                            <form method="POST" id="form-<?= $row['id'] ?>" class="approval-form-row">
                                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                <input type="text" name="note" placeholder="Write a note..." class="note-input" required>
                            </form>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <button type="submit" name="action" value="approved" form="form-<?= $row['id'] ?>" class="btn btn-create" style="padding: 6px 12px; font-size: 13px;">
                                    Approve
                                </button>
                                <button type="submit" name="action" value="rejected" form="form-<?= $row['id'] ?>" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if ($pending_count === 0): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-light); padding: 30px;">
                            No pending bookings require approval at this time.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
