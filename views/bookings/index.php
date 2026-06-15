<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/BookingController.php';

$controller = new BookingController();

$bookings = $controller->index();
?>

<!DOCTYPE html>
<html>

<head>

    <title>Booking List</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <div class="top-bar">

        <h1>Booking List</h1>

        <div>

            <a href="create.php">
                <button class="btn btn-create">
                    + Create Booking
                </button>
            </a>

            <a href="../../public/logout.php">
                <button class="btn btn-delete">
                    Logout
                </button>
            </a>

        </div>

    </div>

    <table>

        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Resource ID</th>
            <th>Time Slot ID</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $bookings->fetch_assoc()): ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['user_id'] ?></td>

            <td><?= $row['resource_id'] ?></td>

            <td><?= $row['time_slot_id'] ?></td>

            <td><?= $row['booking_date'] ?></td>

            <td>
                <span class="status <?= $row['status'] ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </td>

            <td>

                <a href="edit.php?id=<?= $row['id'] ?>">

                    <button class="btn btn-edit">
                        Edit
                    </button>

                </a>

                <?php if ($row['status'] !== 'cancelled'): ?>
                <a href="../cancellations/create.php?booking_id=<?= $row['id'] ?>">
                    <button class="btn btn-edit" style="background: #e0a800;">
                        Cancel
                    </button>
                </a>
                <?php endif; ?>

                <a href="delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Delete this booking?')">

                    <button class="btn btn-delete">
                        Delete
                    </button>

                </a>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>