<?php
// admin/login.php — Admin credential login (separate from SAML)

session_start();

// Already logged in as admin?
if (isset($_SESSION['admin_user'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Use prepared statement — prevents SQL injection
        $sql  = 'SELECT username FROM admins WHERE username = ? AND password = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, hash('sha256', $password));
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            // Regenerate session ID on privilege escalation
            session_regenerate_id(true);
            $_SESSION['admin_user'] = $row['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            // Intentionally vague error to avoid username enumeration
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — UIndy Grab-N-Go</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">

    <div class="login-logo">🔐</div>
    <h1>Admin Portal</h1>
    <p class="subtitle">Grab-N-Go Order Management</p>

    <?php if ($error): ?>
      <div class="error-msg" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text"
               id="username"
               name="username"
               autocomplete="username"
               required
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password"
               id="password"
               name="password"
               autocomplete="current-password"
               required>
      </div>
      <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <p class="login-note" style="margin-top:1.25rem;">
      Admin accounts are managed by IT.<br>
      Accounts cannot be created through this interface.
    </p>

    <p style="margin-top:1rem;">
      <a href="../index.php" style="font-size:.8rem;color:#6b7280;">← Back to student portal</a>
    </p>
  </div>
</div>
</body>
</html>
