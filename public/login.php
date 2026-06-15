<?php
session_start();
require_once '../config/database.php';

$message = "";
$email = "";
$selected_role = "student";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $selected_role = isset($_POST['role']) ? trim($_POST['role']) : 'student';

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Role check
        if ($user['role'] !== $selected_role) {
            $message = "Access denied: Account role mismatch (Expected '{$user['role']}', Selected '{$selected_role}').";
        } else if (password_verify($password, $user['password'])) {
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
    <title>Login - CSB System</title>
    <link rel="stylesheet" href="../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="login-body">

<div class="login-card">
    <!-- LEFT PANEL: Illustration -->
    <div class="login-left">
        <!-- Premium SVG Booking Graphic -->
        <svg width="220" height="220" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="50" width="200" height="150" rx="16" fill="#e0e7ff"/>
            <rect x="40" y="30" width="160" height="180" rx="12" fill="#ffffff" stroke="#5850ec" stroke-width="6"/>
            <!-- Header bar -->
            <path d="M40 70H200" stroke="#5850ec" stroke-width="4"/>
            <circle cx="65" cy="50" r="8" fill="#5850ec"/>
            <circle cx="95" cy="50" r="8" fill="#5850ec"/>
            <circle cx="125" cy="50" r="8" fill="#5850ec"/>
            <!-- Calendar grid lines -->
            <line x1="70" y1="100" x2="70" y2="180" stroke="#cbd5e1" stroke-width="4" stroke-dasharray="4 4"/>
            <line x1="120" y1="100" x2="120" y2="180" stroke="#cbd5e1" stroke-width="4" stroke-dasharray="4 4"/>
            <line x1="170" y1="100" x2="170" y2="180" stroke="#cbd5e1" stroke-width="4" stroke-dasharray="4 4"/>
            <line x1="50" y1="125" x2="190" y2="125" stroke="#cbd5e1" stroke-width="4"/>
            <line x1="50" y1="155" x2="190" y2="155" stroke="#cbd5e1" stroke-width="4"/>
            <!-- Booking checkmark highlight card -->
            <rect x="105" y="110" x2="165" y2="150" rx="6" fill="#34d399" transform="translate(10, 10)" width="50" height="30"/>
            <path d="M125 130L132 137L145 124" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="login-left-title">Campus Booking</div>
        <div class="login-left-desc">Hệ thống đặt lịch phòng học, phòng thực hành & studio tiện lợi và nhanh chóng.</div>
    </div>

    <!-- RIGHT PANEL: Login Form -->
    <div class="login-right">
        <div class="login-right-title">Welcome Back!</div>
        <div class="login-right-subtitle">Please enter your details to sign in.</div>

        <?php if($message): ?>
            <div class="message error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <!-- Hidden Role Input -->
            <input type="hidden" name="role" id="role_input" value="<?= htmlspecialchars($selected_role) ?>">

            <!-- Role Selector Options -->
            <div class="role-select-title">Select Role</div>
            <div class="role-options">
                <div class="role-option <?= $selected_role === 'student' ? 'active' : '' ?>" data-role="student" onclick="selectRole('student')">
                    <!-- Student Icon -->
                    <svg class="role-option-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                    <span class="role-option-label">Student</span>
                </div>
                <div class="role-option <?= $selected_role === 'lecturer' ? 'active' : '' ?>" data-role="lecturer" onclick="selectRole('lecturer')">
                    <!-- Lecturer Icon -->
                    <svg class="role-option-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2m-2 3h.01M5.5 8.5L8 11.5L11.5 8.5"></path>
                    </svg>
                    <span class="role-option-label">Lecturer</span>
                </div>
                <div class="role-option <?= $selected_role === 'admin' ? 'active' : '' ?>" data-role="admin" onclick="selectRole('admin')">
                    <!-- Admin Icon -->
                    <svg class="role-option-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="role-option-label">Admin</span>
                </div>
            </div>

            <!-- Email Input -->
            <label>Email</label>
            <div class="input-group">
                <!-- User Icon -->
                <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <input type="email" name="email" placeholder="email@campus.edu.vn" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <!-- Password Input -->
            <label style="margin-top: 15px;">Password</label>
            <div class="input-group">
                <!-- Lock Icon -->
                <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
                <span class="eye-icon" id="togglePassword">
                    <!-- Eye Icon -->
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </span>
            </div>

            <!-- Submit Button -->
            <button class="btn btn-submit" type="submit" style="margin-top: 25px;">Sign In</button>
        </form>

        <div class="login-bottom-link">
            Don't have an account? <a href="register.php">Sign Up</a>
        </div>
    </div>
</div>

<script>
// Select Role Interaction
function selectRole(role) {
    document.getElementById('role_input').value = role;
    
    // Remove active state from all options
    document.querySelectorAll('.role-option').forEach(el => {
        el.classList.remove('active');
    });
    
    // Add active state to selected option
    document.querySelector(`.role-option[data-role="${role}"]`).classList.add('active');
}

// Show/Hide Password
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');
const eyeOpen = document.querySelector('#eyeOpen');
const eyeClosed = document.querySelector('#eyeClosed');

togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    if (type === 'password') {
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
    } else {
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
    }
});
</script>

</body>
</html>