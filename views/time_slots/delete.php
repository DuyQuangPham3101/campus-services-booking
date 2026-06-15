<?php

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();

$id = $_GET['id'];

$controller->delete($id);

header("Location: index.php");

exit();

?>