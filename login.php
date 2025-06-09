<?php
session_start(); // Bắt đầu session để lưu thông tin đăng nhập

// Kết nối database
include("database.php");

// Xử lý form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $errors = [];

    // Kiểm tra dữ liệu
    if (empty($username) || empty($password)) {
        $errors[] = "Vui lòng điền đầy đủ tên đăng nhập và mật khẩu.";
    }

    // Kiểm tra thông tin đăng nhập
    if (empty($errors)) {
        try {
            $sql = "SELECT id, username, password FROM users WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công, lưu thông tin vào session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Redirect sang trang chính 
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Tên đăng nhập hoặc mật khẩu không đúng.";
            }
        } catch (PDOException $e) {
            $errors[] = "Lỗi khi kiểm tra dữ liệu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
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
                    <h3 class="text-center">Đăng nhập</h3>
                    <?php
                    // Hiển thị lỗi nếu có
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                        }
                    }
                    ?>
                    <form name="loginForm" method="post" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="mar-top clearfix">
                            <button class="btn btn-primary btn-shadow pull-right" type="submit">Đăng nhập</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    let username = document.forms["loginForm"]["username"].value;
    let password = document.forms["loginForm"]["password"].value;

    if (username.length < 3) {
        alert("Tên đăng nhập phải có ít nhất 3 ký tự.");
        return false;
    }
    if (password.length < 6) {
        alert("Mật khẩu phải có ít nhất 6 ký tự.");
        return false;
    }
    return true;
}
</script>
</body>
</html>