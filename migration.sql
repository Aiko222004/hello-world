-- Migration script for existing databases
-- Run this ONLY if you already have the database created without the role column

-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin', 'developer') DEFAULT 'user' AFTER full_name;

-- Update existing admin user to have admin role
UPDATE users SET role = 'admin' WHERE username = 'admin';

-- Update ticket status enum to include 'Completed' instead of 'Resolved'
ALTER TABLE tickets MODIFY COLUMN status ENUM('Open', 'In Progress', 'Closed', 'Completed') DEFAULT 'Open';
