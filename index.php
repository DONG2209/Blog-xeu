<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

include("./views/header.php");
include("database.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

if (!isset($_COOKIE['lang'])) {
    setcookie("lang", "en", time() + (86400 * 30), "/");
    die(header("Location: /"));
}
try {
    $row = select_one("select value from config where name = \"lang_path\"");
    $lang_path = $row["value"];
    // default config: lang_path = "./lang/"
    include($lang_path . $_COOKIE['lang'] . '.html');
} catch (PDOException $e) {
    die($e);
}

if (isset($_GET['id'])) {
    try {
        $data = select_one("select image from comments where id = " . $_GET['id']);
        $show_image = $data['image'];
    } catch (PDOException $e) {
        die($e);
    }
}

if (isset($_POST['comment'])) {
    try {
        $comment = trim($_POST['comment']);
        $file_content = "";
        $errors = [];

        if (!empty($_FILES["file"]["name"])) {
            $extensions = ['jpg', 'png'];
            $file_extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            if (!in_array($file_extension, $extensions)) {
                $errors[] = "File extension is not allowed";
            } else {
                $file_content = base64_encode(file_get_contents($_FILES["file"]["tmp_name"]));
            }
        } elseif (!empty($comment)) {
            $file_content = base64_encode($comment);
        } else {
            $errors[] = "Vui lòng nhập nội dung comment.";
        }

        if (empty($errors)) {
            insert_one(
                "INSERT INTO comments(display_name, comment, image) VALUES (?, ?, ?)",
                $_SESSION['username'],
                $comment,
                $file_content
            );
            header("Location: index.php");
            exit();
        } else {
            foreach ($errors as $error) {
                echo "<p class='text-danger'>$error</p>";
            }
        }
    } catch (PDOException $e) {
        die("Lỗi: " . $e->getMessage());
    }
}

include("./views/comments.php");
?>

<!-- Trigger the Modal -->
<div class="row">
    <div class="col-md-12">
        <!-- The Modal -->
        <div id="myModal" class="modal-w3">
            <!-- The Close Button -->
            <span class="close-w3">&times;</span>
            <!-- Modal Content (The Image) -->
            <img class="modal-w3-content" id="img01">
            <!-- Modal Caption (Image Text) -->
            <div id="caption"></div>
        </div>
    </div>
</div>
<script>
    <?php if ($show_image) { ?>
        // Get the modal
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");
        modal.style.display = "block";
        modalImg.src = "data:image/png;base64,<?php echo $show_image ?>";
        var span = document.getElementsByClassName("close-w3")[0];
        span.onclick = function() {
            modal.style.display = "none";
            window.location.href = "/index.php";
        }
    <?php } ?>
</script>

<?php include("./views/footer.php"); ?>