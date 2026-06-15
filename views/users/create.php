<?php

require_once '../../controllers/UserController.php';

$controller = new UserController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $controller->store($_POST);

    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Create User</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Create User</h1>

    <form method="POST">

        <label>Name</label>

        <input type="text" name="name" required>

        <label>Email</label>

        <input type="email" name="email" required>

        <label>Password</label>

        <input type="password" name="password" required>

        <label>Role</label>

        <select name="role">

            <option value="admin">
                Admin
            </option>

            <option value="user">
                User
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Create User
        </button>

    </form>

</div>

</body>
</html>