<?php

require_once __DIR__ . '/../config/database.php';

class ReportController
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // GET GENERAL STATS
    public function getGeneralStats()
    {
        $stats = [];

        // Total bookings
        $res = $this->conn->query("SELECT COUNT(*) as count FROM bookings");
        $stats['total'] = $res->fetch_assoc()['count'];

        // Approved
        $res = $this->conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'approved'");
        $stats['approved'] = $res->fetch_assoc()['count'];

        // Pending
        $res = $this->conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
        $stats['pending'] = $res->fetch_assoc()['count'];

        // Cancelled
        $res = $this->conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'cancelled'");
        $stats['cancelled'] = $res->fetch_assoc()['count'];

        return $stats;
    }

    // GET RESOURCE USAGE FREQUENCY
    public function getResourceUsageFrequency()
    {
        $sql = "SELECT r.id, r.name, rc.name as category_name, COUNT(b.id) as booking_count, r.location 
                FROM resources r
                JOIN resource_categories rc ON r.category_id = rc.id
                LEFT JOIN bookings b ON r.id = b.resource_id AND b.status = 'approved'
                GROUP BY r.id
                ORDER BY booking_count DESC";
        return $this->conn->query($sql);
    }

    // GET PEAK HOUR USAGE FREQUENCY
    public function getPeakHourUsage()
    {
        $sql = "SELECT ts.slot_name, ts.start_time, ts.end_time, COUNT(b.id) as usage_count 
                FROM time_slots ts
                LEFT JOIN bookings b ON ts.id = b.time_slot_id AND b.status = 'approved'
                WHERE ts.is_peak_hour = 1
                GROUP BY ts.id
                ORDER BY usage_count DESC";
        return $this->conn->query($sql);
    }
    // GET WEEKLY BOOKING STATS (last 4 weeks)
    public function getWeeklyStats() {
        $sql = "SELECT 
                    YEARWEEK(booking_date, 1) as week_num,
                    MIN(booking_date) as week_start,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM bookings 
                WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
                GROUP BY YEARWEEK(booking_date, 1)
                ORDER BY week_num ASC";
        return $this->conn->query($sql);
    }

    // GET TOP USERS BY BOOKING COUNT
    public function getTopUsers() {
        $sql = "SELECT u.id, u.name, u.email, u.role, COUNT(b.id) as booking_count
                FROM users u
                LEFT JOIN bookings b ON u.id = b.user_id
                GROUP BY u.id
                HAVING booking_count > 0
                ORDER BY booking_count DESC
                LIMIT 10";
        return $this->conn->query($sql);
    }

    // GET MONTHLY TREND
    public function getMonthlyTrend() {
        $sql = "SELECT 
                    DATE_FORMAT(booking_date, '%Y-%m') as month,
                    DATE_FORMAT(booking_date, '%M %Y') as month_label,
                    COUNT(*) as total
                FROM bookings
                GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
                ORDER BY month ASC
                LIMIT 12";
        return $this->conn->query($sql);
    }
}
