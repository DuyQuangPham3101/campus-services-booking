<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

if ($user['role'] !== 'admin' && $user['role'] !== 'lecturer') {
    die("Access denied. Only admins and lecturers can view cancellation history.");
}

require_once '../../controllers/CancellationController.php';

$controller = new CancellationController();
$cancellations = $controller->index();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cancellation History - CSB System</title>
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
            <h2>Cancellation History</h2>
        </div>

        <div class="content-card">
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Booking ID</th>
                            <th>Booking Date</th>
                            <th>Reason</th>
                            <th>Cancelled By</th>
                            <th>Cancelled At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $cancellations->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><a href="../bookings/index.php?highlight=<?= $row['booking_id'] ?>">#<?= htmlspecialchars($row['booking_id']) ?></a></td>
                            <td><?= htmlspecialchars($row['booking_date']) ?></td>
                            <td><strong><?= htmlspecialchars($row['reason']) ?></strong></td>
                            <td><?= htmlspecialchars($row['cancelled_by_name'] ?? 'User ID: ' . $row['cancelled_by']) ?></td>
                            <td><?= htmlspecialchars($row['cancelled_at']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($cancellations->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-light); padding: 30px;">
                                    No cancellations found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
