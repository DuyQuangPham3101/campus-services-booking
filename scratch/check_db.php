<?php
require_once __DIR__ . '/../config/database.php';
$res = $conn->query("SELECT id, name, email, password, role FROM users");
while ($row = $res->fetch_assoc()) {
    $ver = password_verify('password123', $row['password']) ? 'VALID' : 'INVALID';
    echo "Email: " . $row['email'] . " | Hash: " . $row['password'] . " | Password 'password123' is " . $ver . "\n";
}
