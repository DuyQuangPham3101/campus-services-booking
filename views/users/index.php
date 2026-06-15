<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/UserController.php';

$controller = new UserController();

$users = $controller->index();

?>

<!DOCTYPE html>
<html>

<head>

    <title>User List</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>User List</h1>

    <div class="top-actions">

        <a href="create.php">
            <button class="btn btn-create">
                + Create User
            </button>
        </a>

        <a href="../../public/dashboard.php">
            <button class="btn btn-edit">
                Dashboard
            </button>
        </a>

    </div>

    <table>

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $users->fetch_assoc()): ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['name'] ?></td>

            <td><?= $row['email'] ?></td>

            <td><?= $row['role'] ?></td>

            <td>

                <a href="edit.php?id=<?= $row['id'] ?>">
                    <button class="btn btn-edit">
                        Edit
                    </button>
                </a>

                <a href="delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Delete this user?')">

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