<?php
session_start();
include("database.php");
include("./views/header.php");

if ($_SERVER['REMOTE_ADDR'] === "127.0.0.1") {
    if (isset($_GET['name']) && isset($_GET['value'])) {
        try {
            $sql = "UPDATE config SET value = ? where name = ?";
            $sth = $conn->prepare($sql);
            $sth->bindParam(1, $_GET['value']);
            $sth->bindParam(2, $_GET['name']);
            $sth->execute();
        } catch (PDOException $e) {
            die($e);
        }
    }
?>


    <div class="jumbotron bg-transparent">
        <div class="container">
            <div class="row">
                <div class="col-md-10">
                    <h1 class="display-2">Admin<span class="vim-caret">n</span></h1>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php
                try {
                    $sql = "select name, value from config";
                    $sth = $conn->prepare($sql);
                    $sth->execute();
                    $sth->setFetchMode(PDO::FETCH_ASSOC);
                    $i = 0;
                    while ($row = $sth->fetch()) {
                ?>
                        <form method="get">
                            <input type="hidden" id="name" name="name" value="<?php echo $row['name'] ?>">
                            <input type="text" id="value" name="value" placeholder="<?php echo $row['value'] ?>">
                            <button class="ht-tm-element btn btn-dark btn-shadow" type="submit" value="Submit">Submit
                            </button>
                        </form>
                <?php
                    }
                } catch (PDOException $e) {
                    die($e);
                }
                ?>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="jumbotron bg-transparent">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="display-2">ACCESS DENIED<span class="vim-caret">D</span></h1>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php }
include("./views/footer.php"); ?>