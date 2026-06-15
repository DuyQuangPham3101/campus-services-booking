-- SQL Script for Campus Services Booking (CSB) Database
-- Clean previous tables if they exist to start fresh (Reverse order of dependencies)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS usage_reports;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS maintenance_schedules;
DROP TABLE IF EXISTS cancellations;
DROP TABLE IF EXISTS approvals;
DROP TABLE IF EXISTS booking_logs;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS time_slots;
DROP TABLE IF EXISTS resource_images;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS booking_policies;
DROP TABLE IF EXISTS resource_categories;
DROP TABLE IF EXISTS user_profiles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Departments table
CREATE TABLE departments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Users table (Authentication)
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'student', -- 'student', 'lecturer', 'admin'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. User Profiles table
CREATE TABLE user_profiles (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    student_code VARCHAR(50) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    department_id INT(11) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Resource Categories table
CREATE TABLE resource_categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- Room, Lab, Stadium, Studio
    description TEXT,
    location VARCHAR(100) DEFAULT NULL,
    max_capacity INT(11) DEFAULT 1,
    requires_approval TINYINT(1) DEFAULT 0,
    max_booking_per_week INT(11) DEFAULT 5,
    open_time TIME DEFAULT '08:00:00',
    close_time TIME DEFAULT '21:00:00',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Booking Policies table
CREATE TABLE booking_policies (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) NOT NULL,
    rule_type VARCHAR(100) NOT NULL, -- e.g., 'max_peak_slots_per_week', 'requires_advisor'
    value INT(11) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES resource_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Resources table (Specific resources)
CREATE TABLE resources (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- e.g., 'Self-study Room A1'
    category_id INT(11) NOT NULL,
    capacity INT(11) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'available', -- 'available', 'maintenance', 'unavailable'
    location VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES resource_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Time Slots table
CREATE TABLE time_slots (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_peak_hour TINYINT(1) DEFAULT 0,
    day_of_week INT(11) NOT NULL, -- 1 = Mon, 7 = Sun
    slot_name VARCHAR(50) NOT NULL -- e.g., 'Slot 1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Bookings table
CREATE TABLE bookings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    resource_id INT(11) NOT NULL,
    time_slot_id INT(11) NOT NULL,
    booking_date DATE NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending', -- 'pending', 'approved', 'rejected', 'cancelled'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Approvals table
CREATE TABLE approvals (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    booking_id INT(11) NOT NULL,
    approved_by INT(11) NOT NULL, -- Lecturer/Admin ID
    status VARCHAR(50) NOT NULL, -- 'approved', 'rejected'
    note TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Cancellations table (MODIFIED: added cancelled_by)
CREATE TABLE cancellations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    booking_id INT(11) NOT NULL,
    reason TEXT NOT NULL,
    cancelled_by INT(11) NOT NULL, -- The user who cancelled the booking (student or admin)
    cancelled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Maintenance Schedules table
CREATE TABLE maintenance_schedules (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    resource_id INT(11) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status VARCHAR(50) NOT NULL DEFAULT 'scheduled', -- 'scheduled', 'ongoing', 'completed'
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Notifications table
CREATE TABLE notifications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- SAMPLE DATA INSERTION (For testing)
-- ==========================================

-- Insert Departments
INSERT INTO departments (name, description) VALUES
('Information Technology', 'Faculty of Information Technology'),
('Business Administration', 'Faculty of Business Administration');

-- Insert Users
-- Default passwords are hashed using bcrypt/PASSWORD_DEFAULT for safety. Here we use 'password123' as plaintext password hash.
-- Note: 'password123' hashed: $2y$10$sbM6ED4aaL/blrOkmhfTMu6q5NL6Y/Ldi43Hm8J7gikuNSbuR.8Jq
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@campus.edu.vn', '$2y$10$sbM6ED4aaL/blrOkmhfTMu6q5NL6Y/Ldi43Hm8J7gikuNSbuR.8Jq', 'admin'),
('Lecturer Huy', 'huy.nguyen@campus.edu.vn', '$2y$10$sbM6ED4aaL/blrOkmhfTMu6q5NL6Y/Ldi43Hm8J7gikuNSbuR.8Jq', 'lecturer'),
('Student Quang', 'quang.pham@student.edu.vn', '$2y$10$sbM6ED4aaL/blrOkmhfTMu6q5NL6Y/Ldi43Hm8J7gikuNSbuR.8Jq', 'student'),
('Lecturer A', 'lecturer.a@campus.edu.vn', '$2y$10$sbM6ED4aaL/blrOkmhfTMu6q5NL6Y/Ldi43Hm8J7gikuNSbuR.8Jq', 'lecturer'),
('Student A', 'student.a@campus.edu.vn', '$2y$10$sbM6ED4aaL/blrOkmhfTMu6q5NL6Y/Ldi43Hm8J7gikuNSbuR.8Jq', 'student');

-- Insert User Profiles
INSERT INTO user_profiles (user_id, student_code, phone, address, department_id) VALUES
(3, 'SV123456', '0912345678', 'Hanoi, Vietnam', 1),
(5, 'SV654321', '0987654321', 'Hanoi, Vietnam', 1);

-- Insert Resource Categories
INSERT INTO resource_categories (name, description, max_capacity, requires_approval, max_booking_per_week) VALUES
('Self-Study Room', 'Group study rooms for students', 10, 0, 5),
('Specialized Lab', 'Computer labs with specific software. Requires lecturer approval.', 40, 1, 2),
('Media Studio', 'Audio and Video recording studio', 5, 1, 2);

-- Insert Resources
INSERT INTO resources (name, category_id, capacity, status, location) VALUES
('Study Room A1', 1, 6, 'available', 'Building A - Floor 1'),
('Lab IT 301', 2, 30, 'available', 'Building B - Floor 3'),
('Studio Media 105', 3, 4, 'available', 'Building C - Floor 1');

-- Insert Time Slots
INSERT INTO time_slots (start_time, end_time, is_peak_hour, day_of_week, slot_name) VALUES
('08:00:00', '10:00:00', 0, 1, 'Slot 1 (Mon)'),
('10:00:00', '12:00:00', 0, 1, 'Slot 2 (Mon)'),
('13:00:00', '15:00:00', 1, 1, 'Slot 3 (Mon - Peak)'),
('15:00:00', '17:00:00', 1, 1, 'Slot 4 (Mon - Peak)');

-- Insert Booking Policies
INSERT INTO booking_policies (category_id, rule_type, value) VALUES
(1, 'max_peak_slots_per_week', 2),
(2, 'requires_lecturer_approval', 1);
