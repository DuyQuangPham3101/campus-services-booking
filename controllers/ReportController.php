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
}
