# Ticketing System

A simple PHP-based ticketing system with user authentication, ticket management, and admin panel.

## Features

- **User Authentication**: Secure login system with password hashing
- **User Registration**: Sign up page for new users
- **Dashboard**: Clean landing page with navigation cards
- **Create Tickets**: Submit support tickets with priority levels
- **Ticket History**: View all submitted tickets with status tracking
- **Admin Panel**: Admin and developer access to view and manage all tickets
- **Status Management**: Update ticket status (Open, In Progress, Completed, Closed)
- **Role-Based Access**: Different access levels for users, admins, and developers

## Setup Instructions

### 1. Database Setup

**For New Installation:**
1. Open phpMyAdmin or your MySQL client
2. Import the `database.sql` file to create the database and tables
3. Default admin credentials:
   - Username: `admin`
   - Password: `admin123`
   - Role: `admin`

**For Existing Installation (Upgrade):**
1. If you already have the database, run `migration.sql` to add the role column and update status options

### 2. Configuration

- Edit `config.php` if needed to match your database settings:
  - DB_HOST: `localhost`
  - DB_NAME: `ticketing_system`
  - DB_USER: `root`
  - DB_PASS: `` (empty for XAMPP default)

### 3. Running the Application

1. Start XAMPP (Apache and MySQL)
2. Place this folder in `htdocs`
3. Access the application at: `http://localhost/Ticketing-system/loginform.php`

## User Roles

### Regular User
- Can create tickets
- Can view their own ticket history
- Default role for new signups

### Admin / Developer
- Can access admin panel
- Can view all customer tickets
- Can update ticket status
- Has all user permissions

## File Structure

- `loginform.php` - Login page with modern design
- `signup.php` - User registration page
- `loginprocess.php` - Handles authentication
- `home.php` - Dashboard/landing page
- `create_ticket.php` - Ticket submission form
- `ticket_history.php` - View user's own tickets
- `admin_tickets.php` - Admin panel for managing all tickets
- `logout.php` - Logout handler
- `config.php` - Database configuration
- `database.sql` - Database schema
- `migration.sql` - Migration script for existing databases

## Usage

### For Regular Users:
1. Sign up at `signup.php` or use existing credentials
2. Login at `loginform.php`
3. From the dashboard:
   - **Create Ticket**: Submit a new support request
   - **Ticket History**: View all your submitted tickets

### For Admins/Developers:
1. Login with admin credentials (username: admin, password: admin123)
2. From the dashboard, access the **Admin Panel**
3. View statistics: Total, Open, In Progress, Completed, and Closed tickets
4. See all tickets from all users with customer information
5. Update ticket status using the dropdown and "Update" button

## Ticket Status Options

- **Open**: Newly created tickets
- **In Progress**: Tickets being worked on
- **Completed**: Issues resolved, awaiting closure
- **Closed**: Tickets finalized and closed

## Creating Admin Users

To make an existing user an admin or developer:

1. Open phpMyAdmin
2. Navigate to the `users` table
3. Edit the user record
4. Change the `role` field to either `admin` or `developer`

Or run this SQL query:
```sql
UPDATE users SET role = 'admin' WHERE username = 'your_username';
```

## Security Notes

- Passwords are hashed using PHP's `password_hash()` function
- Prepared statements are used to prevent SQL injection
- Session management for user authentication
- Role-based access control for admin features
- Admin pages check user role before allowing access
