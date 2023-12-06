<?php

require_once "./src/config.php";

$dbConn = db();


$title = "Search Result";
require_once "./header.php";

$searchTerm = null;
$data = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = cleanedData($_GET['search']);
    $sql = "SELECT * FROM posts WHERE title LIKE '%{$searchTerm}%'";
    $result = $dbConn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result;
    }
}
?>
<div class="container mt-5 d-flex flex-column justify-content-center align-items-center p-3" style="min-height: 70vh; margin-top: 4.7rem;">
    <div class="row">
        <?php 
        if($data !== null){
        ?>
            <h1 class="my-5">Search Result: <?= $searchTerm ?></h1>
        <?php
        foreach($data as $row){ 
        ?>
        <div class="col-xs-12 col-sm-12 col-md-4 my-2 align-self-center">
            <a href="./post_detail.php?id=<?= $row['pid'] ?>" class="text-decoration-none text-dark">
                <div class="card">
                    <img style="height: 14rem;" src="./src/admin/uploads/<?= $row['imgurl'] ?>" alt="" class="card-img-top">
                    <div class="card-body">
                        <h5>
                            <?php
                            if (strlen($row['title']) > 50) {
                                echo substr_replace($row['title'], '...', 50);
                            } else {
                                echo $row['title'];
                            }
                            ?>
                        </h5>
                        <div class="card-text">
                            <?php
                            if (strlen($row['description']) > 100) {
                                echo substr_replace($row['description'], '...', 100);
                            } else {
                                echo $row['description'];
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php }} else { ?>
            <h1>No Record Found!</h1>
        <?php } ?>
    </div>
</div>
<?php
require_once "./footer.php";
