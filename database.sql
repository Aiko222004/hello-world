-- Ticketing System Database Schema

CREATE DATABASE IF NOT EXISTS ticketing_system;
USE ticketing_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('user', 'admin', 'developer') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets table
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Open', 'In Progress', 'Closed', 'Completed') DEFAULT 'Open',
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample user (username: admin, password: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'Admin User', 'admin');

-- Add role column to existing users table (run this if upgrading existing database)
-- ALTER TABLE users ADD COLUMN role ENUM('user', 'admin', 'developer') DEFAULT 'user' AFTER full_name;

-- Update existing admin user to have admin role (run this if upgrading existing database)
-- UPDATE users SET role = 'admin' WHERE username = 'admin';
