<?php
session_start();
include "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginform.php");
    exit();
}

// Check if user is admin or developer
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'developer')) {
    $_SESSION['error'] = 'Access denied. Admin or Developer privileges required.';
    header("Location: home.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['status'];
    
    $update_query = $conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    if ($update_query->execute([$new_status, $ticket_id])) {
        $_SESSION['success'] = 'Ticket status updated successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update ticket status.';
    }
    header("Location: admin_tickets.php");
    exit();
}

// Fetch all tickets with user information
$query = $conn->prepare("
    SELECT t.*, u.username, u.full_name, u.email 
    FROM tickets t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC
");
$query->execute();
$tickets = $query->fetchAll(PDO::FETCH_ASSOC);

// Count tickets by status
$stats_query = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) as open,
        SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as closed,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM tickets
");
$stats = $stats_query->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Ticketing System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 {
            font-size: 24px;
        }
        
        .navbar .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .navbar a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .navbar .role-badge {
            background: rgba(255,255,255,0.3);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .page-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #666;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }
        
        .tickets-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .ticket-id {
            font-weight: 600;
            color: #667eea;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-priority.high,
        .badge-priority.urgent {
            background: #ffebee;
            color: #c62828;
        }
        
        .badge-priority.medium {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .badge-priority.low {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .status-select {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .status-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-update {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-update:hover {
            background: #5568d3;
        }
        
        .no-tickets {
            padding: 60px 30px;
            text-align: center;
            color: #666;
        }
        
        .user-info-cell {
            font-size: 13px;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
        }
        
        .user-email {
            color: #999;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>⚙️ Admin Panel</h1>
        <div class="nav-links">
            <span class="role-badge"><?php echo strtoupper($_SESSION['role']); ?></span>
            <a href="home.php">Dashboard</a>
            <a href="ticket_history.php">My Tickets</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <div class="page-header">
            <h2>Ticket Management</h2>
            <p>View and manage all customer support tickets</p>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="number"><?php echo $stats['total']; ?></div>
                <div class="label">Total Tickets</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['open']; ?></div>
                <div class="label">Open</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['in_progress']; ?></div>
                <div class="label">In Progress</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['completed']; ?></div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['closed']; ?></div>
                <div class="label">Closed</div>
            </div>
        </div>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="tickets-table">
            <?php if (count($tickets) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td class="ticket-id">#<?php echo $ticket['id']; ?></td>
                                <td class="user-info-cell">
                                    <div class="user-name"><?php echo htmlspecialchars($ticket['full_name']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($ticket['email']); ?></div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($ticket['title']); ?></strong><br>
                                    <small style="color: #666;"><?php echo htmlspecialchars(substr($ticket['description'], 0, 80)) . '...'; ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-priority <?php echo strtolower($ticket['priority']); ?>">
                                        <?php echo $ticket['priority']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="" class="status-form">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                        <select name="status" class="status-select">
                                            <option value="Open" <?php echo $ticket['status'] == 'Open' ? 'selected' : ''; ?>>Open</option>
                                            <option value="In Progress" <?php echo $ticket['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="Completed" <?php echo $ticket['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Closed" <?php echo $ticket['status'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-update">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-tickets">
                    <h3>No tickets found</h3>
                    <p>There are currently no tickets in the system.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
