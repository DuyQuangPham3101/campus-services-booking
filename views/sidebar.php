<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
if (!$user) {
    // Determine path prefix for redirect
    $script = $_SERVER['SCRIPT_NAME'];
    $redirect_prefix = './';
    if (strpos($script, '/public/') !== false) {
        $redirect_prefix = '../';
    } elseif (strpos($script, '/views/') !== false) {
        $redirect_prefix = '../../';
    }
    header("Location: " . $redirect_prefix . "public/login.php");
    exit();
}

// Auto-detect path prefix if not set
if (!isset($path_prefix)) {
    $script = $_SERVER['SCRIPT_NAME'];
    if (strpos($script, '/public/') !== false) {
        $path_prefix = '../';
    } elseif (strpos($script, '/views/') !== false) {
        $path_prefix = '../../';
    } else {
        $path_prefix = './';
    }
}

$current_uri = $_SERVER['SCRIPT_NAME'];
?>
<div class="sidebar">
    <div class="sidebar-header">
        <?php if ($user['role'] === 'admin'): ?>
            Admin Control
        <?php else: ?>
            Campus Services
        <?php endif; ?>
    </div>
    
    <div class="sidebar-menu">
        <!-- Dashboard -->
        <a href="<?= $path_prefix ?>public/dashboard.php" class="menu-item <?= (strpos($current_uri, 'dashboard.php') !== false) ? 'active' : '' ?>">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
            </svg>
            <span class="menu-label">Dashboard</span>
        </a>

        <!-- Bookings (Manage My Bookings for student, Manage All Bookings for staff) -->
        <a href="<?= $path_prefix ?>views/bookings/index.php" class="menu-item <?= (strpos($current_uri, 'bookings/') !== false) ? 'active' : '' ?>">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="menu-label">Bookings</span>
        </a>

        <!-- Resources (Lecturer & Admin only) -->
        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
            <a href="<?= $path_prefix ?>views/resources/index.php" class="menu-item <?= (strpos($current_uri, 'resources/') !== false) ? 'active' : '' ?>">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span class="menu-label">Resources</span>
            </a>
        <?php endif; ?>

        <!-- Time Slots (Admin only) -->
        <?php if ($user['role'] === 'admin'): ?>
            <a href="<?= $path_prefix ?>views/time_slots/index.php" class="menu-item <?= (strpos($current_uri, 'time_slots/') !== false) ? 'active' : '' ?>">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="menu-label">Time Slots</span>
            </a>
        <?php endif; ?>

        <!-- Users (Admin only) -->
        <?php if ($user['role'] === 'admin'): ?>
            <a href="<?= $path_prefix ?>views/users/index.php" class="menu-item <?= (strpos($current_uri, 'users/') !== false) ? 'active' : '' ?>">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="menu-label">Users</span>
            </a>
        <?php endif; ?>

        <!-- Approvals Queue (Lecturer & Admin only) -->
        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
            <a href="<?= $path_prefix ?>views/approvals/index.php" class="menu-item <?= (strpos($current_uri, 'approvals/') !== false) ? 'active' : '' ?>">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="menu-label">Approvals</span>
            </a>
        <?php endif; ?>

        <!-- Usage Reports (Lecturer & Admin only) -->
        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
            <a href="<?= $path_prefix ?>views/reports/index.php" class="menu-item <?= (strpos($current_uri, 'reports/') !== false) ? 'active' : '' ?>">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="menu-label">Usage Reports</span>
            </a>
        <?php endif; ?>
    </div>

    <a href="<?= $path_prefix ?>public/logout.php" class="logout-btn">
        Logout
    </a>
</div>
