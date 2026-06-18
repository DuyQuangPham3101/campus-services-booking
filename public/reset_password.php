<?php
// Script to update all user passwords to '123456' with proper bcrypt hash
require_once '../config/database.php';

$new_password = '123456';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "Generated hash: " . $hashed_password . "<br><br>";

// Update all users
$sql = "UPDATE users SET password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "✅ All user passwords have been updated to: <strong>123456</strong><br><br>";
    
    // Show all users
    $result = $conn->query("SELECT id, name, email, role FROM users");
    echo "<h3>You can now login with these accounts:</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['role']}</td>";
        echo "<td>123456</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><br><a href='login.php'>👉 Go to Login Page</a>";
    echo "<br><br><strong style='color:red;'>⚠️ DELETE this file after use! (reset_password.php)</strong>";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
