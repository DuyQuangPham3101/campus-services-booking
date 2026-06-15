<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/BookingController.php';

$controller = new BookingController();

$id = $_GET['id'];

$booking = $controller->getBooking($id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $controller->updateBooking($id, $_POST);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Booking</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Edit Booking</h1>

    <form method="POST">

        <label>User ID</label>

        <input
            type="number"
            name="user_id"
            value="<?= $booking['user_id'] ?>"
            required
        >

        <label>Resource ID</label>

        <input
            type="number"
            name="resource_id"
            value="<?= $booking['resource_id'] ?>"
            required
        >

        <label>Time Slot ID</label>

        <input
            type="number"
            name="time_slot_id"
            value="<?= $booking['time_slot_id'] ?>"
            required
        >

        <label>Booking Date</label>

        <input
            type="date"
            name="booking_date"
            value="<?= $booking['booking_date'] ?>"
            required
        >

        <label>Status</label>

        <select name="status">

            <option value="pending"
                <?= $booking['status'] == 'pending' ? 'selected' : '' ?>>
                Pending
            </option>

            <option value="approved"
                <?= $booking['status'] == 'approved' ? 'selected' : '' ?>>
                Approved
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Update Booking
        </button>

    </form>

    <br>

    <a href="index.php">
        Back to List
    </a>

</div>

</body>
</html>