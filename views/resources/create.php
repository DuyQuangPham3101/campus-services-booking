<?php

require_once '../../controllers/ResourceController.php';

$controller = new ResourceController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $controller->store($_POST);

    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Create Resource</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Create Resource</h1>

    <form method="POST">

        <label>Category ID</label>

        <input type="number" name="category_id" required>

        <label>Name</label>

        <input type="text" name="name" required>

        <label>Location</label>

        <input type="text" name="location" required>

        <label>Capacity</label>

        <input type="number" name="capacity" required>

        <label>Status</label>

        <select name="status">

            <option value="available">
                Available
            </option>

            <option value="unavailable">
                Unavailable
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Create Resource
        </button>

    </form>

</div>

</body>
</html>