<?php

require_once '../../controllers/UserController.php';

$controller = new UserController();

$id = $_GET['id'];

$controller->delete($id);

header("Location: index.php");
?>