<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user = $_SESSION['user'];
require_once '../config/database.php';
require_once '../controllers/BookingController.php';

$controller = new BookingController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'delete':
        if ($user['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit();
        }
        $id = (int)($_POST['id'] ?? 0);
        $result = $controller->deleteBooking($id);
        echo json_encode(['success' => $result === true, 'message' => $result === true ? 'Deleted successfully' : 'Delete failed']);
        break;
        
    case 'filter':
        $status = $_GET['status'] ?? 'all';
        $user_id = ($user['role'] === 'student') ? $user['id'] : null;
        $bookings = $controller->index($user_id);
        $rows = [];
        while ($row = $bookings->fetch_assoc()) {
            if ($status !== 'all' && $row['status'] !== $status) continue;
            $rows[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $rows]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
