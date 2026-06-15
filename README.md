# CSB System (Booking Management System)

A simple booking and scheduling management system built with PHP and MySQL.

## Features

- **User Authentication**: Secure login and logout session management.
- **Bookings Management**: View, create, and manage bookings.
- **Resource Management**: Manage assets or resources available for booking.
- **Time Slots Management**: Configure flexible time slots for bookings.
- **User Management**: Add and manage system users.
- **Dashboard**: Simple stats counters (Bookings, Resources, Time slots, Users).

## Tech Stack

- **Backend**: PHP (Object Oriented / Procedural mix)
- **Database**: MySQL (using mysqli)
- **Frontend**: HTML5, CSS3

## Installation & Setup

1. **Prerequisites**: Ensure you have XAMPP, WampServer, or a local PHP and MySQL stack running.
2. **Database Configuration**:
   - Create a database named `csb` in MySQL.
   - Update your connection settings in [config/database.php](file:///c:/xampp/htdocs/csb/config/database.php) if your database credentials differ.
3. **Execution**:
   - Place this project directory under your webserver root (e.g. `c:/xampp/htdocs/csb`).
   - Access the login page at `http://localhost/csb/public/login.php`.
