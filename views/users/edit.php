<?php

require_once '../../controllers/UserController.php';

$controller = new UserController();

$id = $_GET['id'];

$user = $controller->show($id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $controller->update($id, $_POST);

    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit User</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Edit User</h1>

    <form method="POST">

        <label>Name</label>

        <input
            type="text"
            name="name"
            value="<?= $user['name'] ?>"
            required
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
            value="<?= $user['email'] ?>"
            required
        >

        <label>Role</label>

        <select name="role">

            <option value="admin"
                <?= $user['role'] == 'admin' ? 'selected' : '' ?>>
                Admin
            </option>

            <option value="user"
                <?= $user['role'] == 'user' ? 'selected' : '' ?>>
                User
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Update User
        </button>

    </form>

</div>

</body>
</html>