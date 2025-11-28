<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: loginform.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all tickets for the logged-in user
$query = $conn->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
$query->execute([$user_id]);
$tickets = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket History - Ticketing System</title>
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
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h2 {
            color: #333;
        }
        
        .btn-new-ticket {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: transform 0.2s;
        }
        
        .btn-new-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .tickets-grid {
            display: grid;
            gap: 20px;
        }
        
        .ticket-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #667eea;
        }
        
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .ticket-title {
            color: #333;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .ticket-id {
            color: #999;
            font-size: 14px;
        }
        
        .ticket-badges {
            display: flex;
            gap: 10px;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-status {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-status.open {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .badge-status.in-progress {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .badge-status.resolved {
            background: #e0f2f1;
            color: #00796b;
        }
        
        .badge-status.closed {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-priority {
            background: #e0e0e0;
            color: #616161;
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
        
        .ticket-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .ticket-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #999;
        }
        
        .no-tickets {
            background: white;
            padding: 60px 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .no-tickets h3 {
            color: #666;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🎫 Ticketing System</h1>
        <div class="nav-links">
            <a href="home.php">Dashboard</a>
            <a href="create_ticket.php">Create Ticket</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <div class="page-header">
            <h2>Your Tickets</h2>
            <a href="create_ticket.php" class="btn-new-ticket">+ New Ticket</a>
        </div>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($tickets) > 0): ?>
            <div class="tickets-grid">
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <div>
                                <div class="ticket-title"><?php echo htmlspecialchars($ticket['title']); ?></div>
                                <div class="ticket-id">Ticket #<?php echo $ticket['id']; ?></div>
                            </div>
                            <div class="ticket-badges">
                                <span class="badge badge-status <?php echo strtolower(str_replace(' ', '-', $ticket['status'])); ?>">
                                    <?php echo $ticket['status']; ?>
                                </span>
                                <span class="badge badge-priority <?php echo strtolower($ticket['priority']); ?>">
                                    <?php echo $ticket['priority']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="ticket-description">
                            <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
                        </div>
                        
                        <div class="ticket-footer">
                            <span>Created: <?php echo date('M d, Y - h:i A', strtotime($ticket['created_at'])); ?></span>
                            <span>Updated: <?php echo date('M d, Y - h:i A', strtotime($ticket['updated_at'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-tickets">
                <h3>No tickets found</h3>
                <p>You haven't created any tickets yet. Click "New Ticket" to create your first support request.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
