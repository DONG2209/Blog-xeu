<div class="container">
    <div class="row">
        <div class="col-md-12 bootstrap snippets">
            <div class="panel">
                <div class="panel-body">
                    <form name="myForm" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <textarea class="form-control" rows="2" placeholder="What are you thinking?" id="comment" name="comment"></textarea>
                        <div class="mar-top clearfix">
                            <button class="btn btn-light btn-shadow pull-right" type="submit"> Submit</button>
                            <label for="file"><a class="btn btn-trans btn-icon fa fa-camera add-tooltip"></a></label>
                            <input type="file" id="file" name="file" style="display: none;">
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel">
                <!-- COUNT COMMENTS -->
                <!--===================================================-->
                <?php
                try {
                    $sql = "select id, display_name, comment, image from comments";
                    $sth = $conn->prepare($sql);
                    $sth->execute();
                    $sth->setFetchMode(PDO::FETCH_ASSOC);
                ?>
                    <div class="panel-body" <?php if ($sth->rowCount() == 0) echo "style=\"display: none;\"" ?>>
                        <!-- GET COMMENTS -->
                        <!--===================================================-->
                        <?php
                        while ($row = $sth->fetch()) {
                        ?>
                            <div class="media-block">
                                <a class="media-left" href="#"><img class="img-circle img-sm" alt="Profile Picture" src="/assets/images/avatar1.png"></a>
                                <div class="media-body">
                                    <div class="mar-btm">
                                        <a href="#" class="btn-link text-semibold media-heading box-inline"><?php echo $row["display_name"] ?></a>
                                        <p class="text-muted text-sm"><i class="fa fa-globe fa-lg"></i> - From Website</p>
                                    </div>
                                    <p style="margin: 0 0 10px;"><?php echo htmlspecialchars($row["comment"]) ?></p>
                                    <?php
                                    if (!empty($row["image"]))
                                        echo "<a href=\"/index.php?id=" . $row["id"] . "\" class=\"ht-tm-element btn btn-danger btn-shadow text-mono\"><span class=\"fa mr-2\" ></span>View</a>"
                                    ?>
                                    <hr>
                                </div>
                            </div>
                    <?php
                        }
                    } catch (PDOException $e) {
                        die($e);
                    }
                    ?>
                    </div>
            </div>
        </div>
    </div>
</div>