<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: loginform.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ticketing System</title>
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
        
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .navbar .username {
            font-weight: 500;
        }
        
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border: 1px solid white;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: white;
            color: #667eea;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .welcome-section h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            color: #666;
            font-size: 16px;
        }
        
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #667eea;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 22px;
        }
        
        .card p {
            color: #666;
            line-height: 1.6;
        }
        
        .card.history {
            border-left-color: #764ba2;
        }
        
        .card.admin {
            border-left-color: #f57c00;
        }
        
        .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🎫 Ticketing System</h1>
        <div class="user-info">
            <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <div class="welcome-section">
            <h2>Dashboard</h2>
            <p>Manage your support tickets efficiently. Create new tickets or view your ticket history.</p>
        </div>
        
        <div class="cards-container">
            <a href="create_ticket.php" class="card">
                <div class="icon">📝</div>
                <h3>Create Ticket</h3>
                <p>Submit a new support ticket. Describe your issue and we'll get back to you as soon as possible.</p>
            </a>
            
            <a href="ticket_history.php" class="card history">
                <div class="icon">📋</div>
                <h3>Ticket History</h3>
                <p>View all your submitted tickets, check their status, and track progress on your support requests.</p>
            </a>
            
            <?php if(isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'developer')): ?>
            <a href="admin_tickets.php" class="card admin">
                <div class="icon">⚙️</div>
                <h3>Admin Panel</h3>
                <p>View all customer tickets and update their status. Manage support requests from all users.</p>
            </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
