<?php
// Kết nối database
include("database.php");

// Xử lý form đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash mật khẩu
    $email = trim($_POST['email']);
    $errors = [];

    // Kiểm tra dữ liệu
    if (empty($username) || empty($password) || empty($email)) {
        $errors[] = "Vui lòng điền đầy đủ các trường.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    }

    // Kiểm tra username đã tồn tại
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Username đã tồn tại.";
    }

    // Nếu không có lỗi, lưu vào database
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'password' => $password,
                'email' => $email
            ]);
            //$success = "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: login.php");
            exit(); // Dừng script sau khi redirect
        } catch (PDOException $e) {
            $errors[] = "Lỗi khi lưu dữ liệu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .panel {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="panel">
                <div class="panel-body">
                    <h3 class="text-center">Đăng ký tài khoản</h3>
                    <?php
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                        }
                    }
                    if (isset($success)) {
                        echo '<p class="success">' . htmlspecialchars($success) . '</p>';
                    }
                    ?>
                    <form name="registerForm" method="post" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
                        </div>
                        <div class="mar-top clearfix">
                            <button class="btn btn-primary btn-shadow pull-right" type="submit">Đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    let username = document.forms["registerForm"]["username"].value;
    let password = document.forms["registerForm"]["password"].value;
    let email = document.forms["registerForm"]["email"].value;
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (username.length < 3) {
        alert("Tên đăng nhập phải có ít nhất 3 ký tự.");
        return false;
    }
    if (password.length < 6) {
        alert("Mật khẩu phải có ít nhất 6 ký tự.");
        return false;
    }
    if (!emailPattern.test(email)) {
        alert("Vui lòng nhập email hợp lệ.");
        return false;
    }
    return true;
}
</script>
</body>
</html>