<?php
session_start();
include "config.php";

// AuthZ: only admin/developer
if (!isset($_SESSION['user_id'])) { header("Location: loginform.php"); exit(); }
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','developer'])) {
    $_SESSION['error'] = 'Access denied. Admin or Developer privileges required.'; header("Location: home.php"); exit();
}

//HAHAHAHAHAHAHA MAMAMO MAMAKO MAMIJUNISIA

$success = null; $errors = [];

// Create or update user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? 'user';

    if ($action === 'create') {
        $password = trim($_POST['password'] ?? '');
        if (!$username || !$email || !$full_name || !$password) { $errors[] = 'All fields are required.'; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Invalid email.'; }
        if (strlen($password) < 6) { $errors[] = 'Password must be at least 6 characters.'; }
        if (!in_array($role, ['user','admin','developer'])) { $errors[] = 'Invalid role.'; }

        if (!$errors) {
            // uniqueness checks
            $q = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $q->execute([$username, $email]);
            if ($q->fetch()) { $errors[] = 'Username or email already exists.'; }
        }
        if (!$errors) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare('INSERT INTO users (username, password, email, full_name, role) VALUES (?,?,?,?,?)');
            if ($ins->execute([$username, $hash, $email, $full_name, $role])) { $success = 'User created successfully.'; } else { $errors[] = 'Failed to create user.'; }
        }
    }

    if ($action === 'promote') {
        if (!$username) { $errors[] = 'Username is required to change role.'; }
        if (!in_array($role, ['user','admin','developer'])) { $errors[] = 'Invalid role.'; }
        if (!$errors) {
            $upd = $conn->prepare('UPDATE users SET role = ? WHERE username = ?');
            if ($upd->execute([$role, $username])) { $success = 'Role updated successfully.'; } else { $errors[] = 'Failed to update role.'; }
        }
    }

    if ($action === 'reset_password') {
        $password = trim($_POST['password'] ?? '');
        if (!$username || !$password) { $errors[] = 'Username and new password are required.'; }
        if (strlen($password) < 6) { $errors[] = 'Password must be at least 6 characters.'; }
        if (!$errors) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conn->prepare('UPDATE users SET password = ? WHERE username = ?');
            if ($upd->execute([$hash, $username])) { $success = 'Password reset successfully.'; } else { $errors[] = 'Failed to reset password.'; }
        }
    }
}

// List users
$list = $conn->query('SELECT id, username, email, full_name, role, created_at FROM users ORDER BY created_at DESC');
$users = $list->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users - Admin</title>
<style>
body{font-family:Segoe UI,Tahoma,Verdana,sans-serif;background:#f5f7fa}
.navbar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:15px 30px;display:flex;justify-content:space-between;align-items:center}
.navbar a{color:#fff;text-decoration:none;padding:8px 15px;border-radius:5px}
.container{max-width:1200px;margin:30px auto;padding:0 20px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.card{background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.success{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:15px}
.error{background:#fee;color:#c33;padding:10px;border-radius:5px;margin-bottom:15px}
.input,select{width:100%;padding:10px;border:2px solid #e0e0e0;border-radius:5px;margin-top:6px}
.label{font-weight:600;color:#555}
.btn{background:#667eea;color:#fff;border:none;border-radius:5px;padding:10px 16px;margin-top:10px;cursor:pointer}
.table{width:100%;border-collapse:collapse;margin-top:10px}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
.role{padding:5px 10px;border-radius:20px;background:#eee;font-size:12px}
</style></head><body>
<nav class="navbar">
    <div>⚙️ Manage Users</div>
    <div>
        <a href="admin_tickets.php">Tickets</a>
        <a href="home.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>
<div class="container">
    <?php if($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
    <?php if(!empty($errors)): ?><div class="error"><?php foreach($errors as $e){ echo '<div>'.$e.'</div>'; } ?></div><?php endif; ?>
    <div class="grid">
        <div class="card">
            <h3>Create User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <label class="label">Full Name</label>
                <input class="input" name="full_name" required>
                <label class="label">Email</label>
                <input class="input" type="email" name="email" required>
                <label class="label">Username</label>
                <input class="input" name="username" required>
                <label class="label">Password</label>
                <input class="input" type="password" name="password" required>
                <label class="label">Role</label>
                <select name="role" class="input">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="developer">Developer</option>
                </select>
                <button class="btn" type="submit">Create</button>
            </form>
        </div>
        <div class="card">
            <h3>Change Role</h3>
            <form method="POST">
                <input type="hidden" name="action" value="promote">
                <label class="label">Username</label>
                <input class="input" name="username" required>
                <label class="label">New Role</label>
                <select name="role" class="input">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="developer">Developer</option>
                </select>
                <button class="btn" type="submit">Update Role</button>
            </form>
            <h3 style="margin-top:20px">Reset Password</h3>
            <form method="POST">
                <input type="hidden" name="action" value="reset_password">
                <label class="label">Username</label>
                <input class="input" name="username" required>
                <label class="label">New Password</label>
                <input class="input" type="password" name="password" required>
                <button class="btn" type="submit">Reset Password</button>
            </form>
        </div>
    </div>
    <div class="card" style="margin-top:20px">
        <h3>All Users</h3>
        <table class="table">
            <thead><tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>
            <tbody>
                <?php foreach($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="role"><?php echo strtoupper($u['role']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body></html>
