<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Magazin</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/bootstrap4-neon-glow.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel='stylesheet' href='//cdn.jsdelivr.net/font-hack/2.020/css/hack.min.css'>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .dropdown-menu {
            min-width: 150px;
        }
        .dropdown-item i {
            margin-right: 8px;
        }
    </style>
</head>

<body>
<?php if (isset($_SESSION['user_id'])): ?>
<div class="container-fluid">
    <div class="d-flex justify-content-end mt-3">
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="../assets/images/avatar.jpg" alt="Avatar" class="avatar-img">
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="../profile.php">
                    <i class="fa fa-user"></i> Thông tin cá nhân
                </a>
                <a class="dropdown-item" href="../logout.php" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                    <i class="fa fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>