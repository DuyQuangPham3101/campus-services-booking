<?php
require_once __DIR__ . '/../config/database.php';
$hash = password_hash('password123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email IN ('admin@campus.edu.vn', 'lecturer.a@campus.edu.vn', 'quang.pham@student.edu.vn')");
$stmt->bind_param("s", $hash);
if ($stmt->execute()) {
    echo "Updated successfully! Hash used: " . $hash . "\n";
} else {
    echo "Update failed: " . $stmt->error . "\n";
}
