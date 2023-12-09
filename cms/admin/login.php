<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'database.php';

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $login_error = 'Both username and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                header('Location: index.php');
                exit();
            } else {
                $login_error = 'Invalid username or password.';
            }
        } else {
            $login_error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width: 400px;">
    <?php if ($login_error): ?>
        <div class="alert alert-danger"><?php echo $login_error; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
