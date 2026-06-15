<?php

session_start();

require_once '../config/database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    // Debug logging to help identify why login is failing
    $debug_log = "Login attempt - Email: '$email'\n";

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();
        $debug_log .= "User found: yes, ID: " . $user['id'] . ", Role: " . $user['role'] . "\n";
        $debug_log .= "Hash in DB: " . $user['password'] . "\n";

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $debug_log .= "Password verification: SUCCESS\n";
            file_put_contents('login_debug.log', $debug_log, FILE_APPEND);
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $debug_log .= "Password verification: FAILED\n";
            $message = "Invalid email or password";
        }

    } else {
        $debug_log .= "User found: no\n";
        $message = "Invalid email or password";
    }

    file_put_contents('login_debug.log', $debug_log, FILE_APPEND);
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Login</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

<div class="container" style="max-width: 450px; margin-top: 80px; box-sizing: border-box;">

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
            style="width: 100%; box-sizing: border-box;"
        >

        <label>Password</label>

        <div style="position: relative; width: 100%; box-sizing: border-box;">
            <input
                type="password"
                name="password"
                id="password"
                required
                style="width: 100%; box-sizing: border-box; padding-right: 40px; margin-top: 8px; margin-bottom: 15px;"
            >
            <span id="togglePassword" style="position: absolute; right: 12px; top: 18px; cursor: pointer; color: #666; display: flex; align-items: center; user-select: none;">
                <!-- Basic SVG Open Eye Icon -->
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <!-- Basic SVG Closed Eye Icon -->
                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            </span>
        </div>

        <button class="btn btn-submit" type="submit" style="margin-top: 10px; width: 100%; box-sizing: border-box;">
            Login
        </button>

    </form>

</div>

<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');
const eyeOpen = document.querySelector('#eyeOpen');
const eyeClosed = document.querySelector('#eyeClosed');

togglePassword.addEventListener('click', function (e) {
    const isPassword = password.getAttribute('type') === 'password';
    password.setAttribute('type', isPassword ? 'text' : 'password');
    
    if (isPassword) {
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
    } else {
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
    }
});
</script>

</body>
</html>