<?php

require_once '../../controllers/BookingController.php';

$controller = new BookingController();

$id = $_GET['id'];

$controller->deleteBooking($id);

header("Location: index.php");
?>