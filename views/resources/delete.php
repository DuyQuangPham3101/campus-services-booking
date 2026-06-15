<?php

require_once '../../controllers/ResourceController.php';

$controller = new ResourceController();

$id = $_GET['id'];

$controller->deleteResource($id);

header("Location: index.php");

?>