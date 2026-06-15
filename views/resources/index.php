<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/ResourceController.php';

$controller = new ResourceController();

$resources = $controller->index();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Resource List</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <div class="top-bar">

        <h1>Resource List</h1>

        <div>

            <a href="create.php">

                <button class="btn btn-create">
                    + Create Resource
                </button>

            </a>

            <a href="../../views/bookings/index.php">

                <button class="btn btn-edit">
                    Bookings
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
            <th>Category ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Capacity</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $resources->fetch_assoc()): ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['category_id'] ?></td>

            <td><?= $row['name'] ?></td>

            <td><?= $row['location'] ?></td>

            <td><?= $row['capacity'] ?></td>

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

                <a href="delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Delete this resource?')">

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