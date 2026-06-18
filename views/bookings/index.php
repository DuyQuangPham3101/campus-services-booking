<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];
require_once '../../controllers/BookingController.php';

$controller = new BookingController();
$user_id = ($user['role'] === 'student') ? $user['id'] : null;
$bookings = $controller->index($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bookings - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2><?= ($user['role'] === 'student') ? 'My Bookings' : 'All Bookings Management' ?></h2>
        </div>

        <div class="content-card">
            <div class="top-actions" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Book New Appointment
                </a>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="statusFilter" style="margin: 0; font-weight: 500;">Filter Status:</label>
                    <select id="statusFilter" onchange="filterBookings()" style="margin: 0; padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Resource</th>
                        <th>Time Slot</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    <?php while($row = $bookings->fetch_assoc()): ?>
                    <tr data-status="<?= htmlspecialchars($row['status']) ?>">
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['user_name'] ?? 'User ID: ' . $row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['resource_name'] ?? 'Resource ID: ' . $row['resource_id']) ?></td>
                        <td><?= htmlspecialchars($row['slot_name'] ?? 'Slot ID: ' . $row['time_slot_id']) ?></td>
                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                        <td>
                            <span class="status <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            <?php endif; ?>

                            <?php if ($row['status'] !== 'cancelled'): ?>
                                <a href="../cancellations/create.php?booking_id=<?= $row['id'] ?>" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px; background-color: var(--danger);">
                                    Cancel
                                </a>
                            <?php endif; ?>

                            <?php if ($user['role'] === 'admin'): ?>
                                <button onclick="ajaxDelete(<?= $row['id'] ?>, this)" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px; border: none; cursor: pointer;">
                                    Delete
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($bookings->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-light); padding: 30px;">
                                No bookings found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterBookings() {
    let status = document.getElementById('statusFilter').value;
    let rows = document.querySelectorAll('#bookingsTableBody tr[data-status]');
    let visibleCount = 0;

    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    let noDataRow = document.getElementById('noDataRow');
    if (visibleCount === 0 && rows.length > 0) {
        if (!noDataRow) {
            noDataRow = document.createElement('tr');
            noDataRow.id = 'noDataRow';
            noDataRow.innerHTML = '<td colspan="7" style="text-align: center; color: var(--text-light); padding: 30px;">No bookings found for this status.</td>';
            document.getElementById('bookingsTableBody').appendChild(noDataRow);
        } else {
            noDataRow.style.display = '';
        }
    } else if (noDataRow) {
        noDataRow.style.display = 'none';
    }
}

function ajaxDelete(id, btn) {
    if (!confirm('Delete this booking?')) return;
    
    // Disable button to prevent double clicks
    btn.disabled = true;
    let originalText = btn.innerHTML;
    btn.innerHTML = 'Deleting...';

    fetch('../../public/api_bookings.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            let row = btn.closest('tr');
            row.remove();
            
            // Check if table is empty now
            let rows = document.querySelectorAll('#bookingsTableBody tr[data-status]');
            if (rows.length === 0) {
                let tbody = document.getElementById('bookingsTableBody');
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--text-light); padding: 30px;">No bookings found.</td></tr>';
            }
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred while deleting the booking.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>

</body>
</html>