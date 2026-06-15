<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];
require_once '../../controllers/BookingController.php';

$controller = new BookingController();
$user_id = ($user['role'] === 'student') ? $user['id'] : null;
$bookings = $controller->index($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bookings - CSB System</title>
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
            <h2><?= ($user['role'] === 'student') ? 'My Bookings' : 'All Bookings Management' ?></h2>
        </div>

        <div class="content-card">
            <div class="top-actions">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Book New Appointment
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Resource</th>
                        <th>Time Slot</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['user_name'] ?? 'User ID: ' . $row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['resource_name'] ?? 'Resource ID: ' . $row['resource_id']) ?></td>
                        <td><?= htmlspecialchars($row['slot_name'] ?? 'Slot ID: ' . $row['time_slot_id']) ?></td>
                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                        <td>
                            <span class="status <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            <?php endif; ?>

                            <?php if ($row['status'] !== 'cancelled'): ?>
                                <a href="../cancellations/create.php?booking_id=<?= $row['id'] ?>" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px; background-color: var(--danger);">
                                    Cancel
                                </a>
                            <?php endif; ?>

                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this booking?')" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                    Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($bookings->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-light); padding: 30px;">
                                No bookings found.
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