<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();

$timeslots = $controller->index();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Time Slot List</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Time Slot List</h1>

    <div class="top-actions">

        <a href="create.php">
            <button class="btn btn-create">
                + Create Time Slot
            </button>
        </a>

        <a href="../../public/dashboard.php">
            <button class="btn btn-dashboard">
                Dashboard
            </button>
        </a>

    </div>

    <table>

        <tr>
            <th>ID</th>
            <th>Slot Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Day</th>
            <th>Peak Hour</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $timeslots->fetch_assoc()): ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['slot_name'] ?></td>

            <td><?= $row['start_time'] ?></td>

            <td><?= $row['end_time'] ?></td>

            <td><?= $row['day_of_week'] ?></td>

            <td>
                <?= $row['is_peak_hour'] ? 'Yes' : 'No' ?>
            </td>

            <td>

                <a href="edit.php?id=<?= $row['id'] ?>">
                    <button class="btn btn-edit">
                        Edit
                    </button>
                </a>

                <a href="delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Delete this time slot?')">

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