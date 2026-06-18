<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/BookingPolicyController.php';

$controller = new BookingPolicyController();

$id = $_GET['id'] ?? 0;

if ($id) {
    $controller->deletePolicy($id);
}

header("Location: index.php");
exit();
?>
