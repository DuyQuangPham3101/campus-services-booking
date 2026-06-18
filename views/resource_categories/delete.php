<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/ResourceCategoryController.php';

$controller = new ResourceCategoryController();

$id = $_GET['id'] ?? 0;

if ($id) {
    $controller->deleteCategory($id);
}

header("Location: index.php");
exit();
?>
