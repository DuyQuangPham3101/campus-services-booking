<?php

require_once '../../controllers/ResourceController.php';

$controller = new ResourceController();

$id = $_GET['id'];

$resource = $controller->getResource($id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $controller->updateResource($id, $_POST);

    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Resource</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Edit Resource</h1>

    <form method="POST">

        <label>Category ID</label>

        <input
            type="number"
            name="category_id"
            value="<?= $resource['category_id'] ?>"
            required
        >

        <label>Name</label>

        <input
            type="text"
            name="name"
            value="<?= $resource['name'] ?>"
            required
        >

        <label>Location</label>

        <input
            type="text"
            name="location"
            value="<?= $resource['location'] ?>"
            required
        >

        <label>Capacity</label>

        <input
            type="number"
            name="capacity"
            value="<?= $resource['capacity'] ?>"
            required
        >

        <label>Status</label>

        <select name="status">

            <option value="available"
                <?= $resource['status'] == 'available' ? 'selected' : '' ?>>
                Available
            </option>

            <option value="unavailable"
                <?= $resource['status'] == 'unavailable' ? 'selected' : '' ?>>
                Unavailable
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Update Resource
        </button>

    </form>

</div>

</body>
</html>