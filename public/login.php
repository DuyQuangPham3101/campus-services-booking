<?php

session_start();

require_once '../config/database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fix bug: Select user by email only, then verify password hash in PHP
    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid email or password";
        }

    } else {

        $message = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Login</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

<div class="container" style="max-width: 450px; margin-top: 80px;">

    <h1>Login</h1>

    <?php if($message): ?>

        <div class="message error">
            <?= $message ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Password</label>

        <div style="position: relative;">
            <input
                type="password"
                name="password"
                id="password"
                required
                style="padding-right: 40px;"
            >
            <span id="togglePassword" style="position: absolute; right: 15px; top: 18px; cursor: pointer; font-size: 18px; user-select: none;">
                👁️
            </span>
        </div>

        <button class="btn btn-submit" type="submit" style="margin-top: 10px;">
            Login
        </button>

    </form>

</div>

<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', function (e) {
    // Toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // Toggle the icon
    this.textContent = type === 'password' ? '👁️' : '🙈';
});
</script>

</body>
</html>