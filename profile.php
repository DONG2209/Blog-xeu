<?php
session_start();
include("./views/header.php");
include("database.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng
try {
    $sql = "SELECT username, email, role FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("Người dùng không tồn tại.");
    }
} catch (PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}

// Xử lý chỉnh sửa email
$errors = [];
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_email'])) {
    $new_email = trim($_POST['email']);
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    } else {
        try {
            $sql = "UPDATE users SET email = :email WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['email' => $new_email, 'id' => $_SESSION['user_id']]);
            $success = "Cập nhật email thành công!";
            $user['email'] = $new_email; // Cập nhật email hiển thị
        } catch (PDOException $e) {
            $errors[] = "Lỗi khi cập nhật email: " . $e->getMessage();
        }
    }
}

// Xử lý đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra mật khẩu
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "Vui lòng điền đầy đủ các trường.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
    } else {
        // Kiểm tra mật khẩu cũ
        $sql = "SELECT password FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($old_password, $current_user['password'])) {
            // Cập nhật mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            try {
                $sql = "UPDATE users SET password = :password WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['password' => $hashed_password, 'id' => $_SESSION['user_id']]);
                $success = "Đổi mật khẩu thành công!";
            } catch (PDOException $e) {
                $errors[] = "Lỗi khi đổi mật khẩu: " . $e->getMessage();
            }
        } else {
            $errors[] = "Mật khẩu cũ không đúng.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="panel mt-5">
                <div class="panel-body">
                    <h3>Thông tin cá nhân</h3>
                    <?php if (!empty($errors)): ?>
                        <?php foreach ($errors as $error): ?>
                            <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <p class="text-success"><?php echo htmlspecialchars($success); ?></p>
                    <?php endif; ?>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Vai trò:</strong> <?php echo $user['role'] == 1 ? 'Admin' : 'Người dùng'; ?></p>

                    <!-- Form chỉnh sửa email -->
                    <h4 class="mt-4">Update Email</h4>
                    <form method="post" onsubmit="return validateEmailForm()">
                        <div class="form-group">
                            <label for="email">Email mới</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <button type="submit" name="update_email" class="btn btn-primary btn-shadow">
                            <i class="fa fa-save mr-1"></i> Cập nhật Email
                        </button>
                    </form>

                    <!-- Form đổi mật khẩu -->
                    <h4 class="mt-4">Đổi mật khẩu</h4>
                    <form method="post" onsubmit="return validatePasswordForm()">
                        <div class="form-group">
                            <label for="old_password">Mật khẩu cũ</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary btn-shadow">
                            <i class="fa fa-lock mr-1"></i> Đổi mật khẩu
                        </button>
                    </form>

                    <a href="index.php" class="btn btn-light btn-shadow mt-3">
                        <i class="fa fa-arrow-left mr-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateEmailForm() {
    let email = document.forms[0]["email"].value;
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Vui lòng nhập email hợp lệ.");
        return false;
    }
    return true;
}

function validatePasswordForm() {
    let oldPassword = document.forms[1]["old_password"].value;
    let newPassword = document.forms[1]["new_password"].value;
    let confirmPassword = document.forms[1]["confirm_password"].value;
    if (oldPassword === "" || newPassword === "" || confirmPassword === "") {
        alert("Vui lòng điền đầy đủ các trường.");
        return false;
    }
    if (newPassword.length < 6) {
        alert("Mật khẩu mới phải có ít nhất 6 ký tự.");
        return false;
    }
    if (newPassword !== confirmPassword) {
        alert("Mật khẩu mới và xác nhận mật khẩu không khớp.");
        return false;
    }
    return true;
}
</script>

<?php include("./views/footer.php"); ?>