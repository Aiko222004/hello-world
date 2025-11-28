<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ticketing System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Deep blue to black gradient */
            background: linear-gradient(135deg, #0d47a1 0%, #000000 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #111;
        }
        
        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            /* Subtle gold glow */
            box-shadow: 0 12px 28px rgba(13, 71, 161, 0.35), inset 0 0 0 1px #e0e0e0;
            width: 100%;
            max-width: 420px;
            border: 2px solid #d4af37; /* gold border */
        }
        
        .login-container h2 {
            text-align: center;
            color: #0d47a1; /* deep blue */
            margin-bottom: 24px;
            font-size: 28px;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #222; /* black tone */
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ccd7ea; /* soft blue-gray */
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.25s, box-shadow 0.25s;
            background: #fafafa; /* white-ish */
            color: #111;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0d47a1; /* deep blue */
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.15);
            background: #ffffff;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 60%, #d4af37 100%); /* blue to gold */
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s, filter 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 71, 161, 0.35);
            filter: brightness(1.05);
        }
        
        .error-message {
            background: #fff5f5;
            color: #b00020; /* dark red */
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }
        
        .success-message {
            background: #eaf7ea;
            color: #1b5e20; /* dark green */
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            text-align: center;
            border: 1px solid #c8e6c9;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 18px;
            color: #333;
            font-size: 14px;
        }
        
        .signup-link a {
            color: #d4af37; /* gold */
            text-decoration: none;
            font-weight: 700;
            border-bottom: 2px solid transparent;
        }
        
        .signup-link a:hover {
            border-bottom-color: #d4af37;
        }
        
        /* Thin gold line accent above the form */
        .accent-bar {
            height: 4px;
            width: 60px;
            background: #d4af37;
            margin: 0 auto 16px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="accent-bar"></div>
        <h2>🎫 Ticketing System</h2>
        <h2> Testing Merge Error </h2>
        
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
        
        <form action="loginprocess.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" name="login" class="btn-login">Login</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>
</body>
</html>
