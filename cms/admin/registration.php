<?php
$registration_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'database.php';

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $upload_dir = 'uploads/';
    $file_path = $upload_dir . basename($_FILES['image']['name']);
    $upload_ok = 1;

    $check = getimagesize($_FILES['image']['tmp_name']);
    if($check !== false) {
        $upload_ok = 1;
    } else {
        $upload_ok = 0;
        $registration_error = 'File is not an image.';
    }

    if (file_exists($file_path)) {
        $upload_ok = 0;
        $registration_error = 'Sorry, file already exists.';
    }

    if ($upload_ok == 1) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            // File is uploaded successfully
        } else {
            $registration_error = 'Sorry, there was an error uploading your file.';
        }
    }

    if (empty($registration_error) && (empty($username) || empty($email) || empty($password))) {
        $registration_error = 'All fields are required.';
    } else {
        if (empty($registration_error)) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $passwordHash, $file_path);

            if ($stmt->execute()) {
                header('Location: login.php');
                exit();
            } else {
                $registration_error = 'Error: ' . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width: 400px;">
    <?php if ($registration_error): ?>
        <div class="alert alert-danger"><?php echo $registration_error; ?></div>
    <?php endif; ?>
    <form action="registration.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
</body>
</html>
