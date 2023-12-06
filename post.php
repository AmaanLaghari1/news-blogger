<?php
require_once "./src/config.php";

$con = db();

$category = $_GET['category'];

$title = "{$category} ";
require_once "./header.php";
?>
<div id="Post" class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center position-relative p-0" style="height: 22rem;">
        <?php
            // Pagination Query
            $limit = 12;
            $pg = isset($_GET['pg']) ? $_GET['pg'] : 1;
            $offset = ($pg - 1) * $limit;
             $sqlSel = "SELECT p.pid, p.title, p.description, p.imgurl, c.posts_no, c.img FROM posts p INNER JOIN categories c ON p.category = c.cid WHERE c.category = '{$category}' AND p.approved = 1 ORDER BY p.pid DESC LIMIT {$offset}, {$limit}";
            //  die();
            $result = $con->query($sqlSel);

            // data in this category
            $catData = $result->fetch_assoc();
            
            if($result->num_rows > 0){
        ?>
        <img src="./src/static/images/cat-thumbnails/<?= $catData['img'] ?>" alt="" style="z-index: -1;" class="w-100 h-100 position-absolute">
        <h1 class="display-2 bg-dark text-light px-5"><?= $category ?></h1>
        <h4 class="bg-dark p-3 text-light"> Posts - <?php echo $catData['posts_no'] ?></h4>
    </div>
    <div class="container position-relative my-3">
        <div class="row">
                <?php
                foreach($result as $row){
                ?>
                <div class="col-xs-6 col-sm-6 col-md-4 my-2 align-self-center">
                    <a href="./post_detail.php?id=<?= $row['pid'] ?>" class="text-decoration-none text-dark">
                        <div class="card card-hover-shadow">
                            <img style="height: 14rem;" src="./src/admin/uploads/<?= $row['imgurl'] ?>" alt="" class="card-img-top">
                            <div class="card-body">
                                <h5>
                                <?php
                                    if(strlen($row['title']) > 50){ 
                                        echo substr_replace($row['title'], '...', 50);
                                    }
                                    else {
                                        echo $row['title'];
                                    }
                                ?>
                                </h5>
                                <div class="card-text">
                                    <?php
                                        if(strlen($row['description']) > 100){
                                            echo substr_replace($row['description'], '...', 100);
                                        }
                                        else {
                                            echo $row['description'];
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
            <!-- Pagination Controls -->
            <div class="row">
                <div class="d-flex justify-content-center my-3">
                <?php
                $totalRecords = $catData['posts_no'];
                $limit = 12;
                $totalPgs = ceil($totalRecords / $limit);
                if($pg > 1){
                ?>
                    <a href="./post.php?category=<?= $category ?>&pg=<?= $pg-1 ?>" class="btn btn-sm btn-primary mx-2">Prev</a>
                <?php
                }
                for($i = 1; $i <= $totalPgs; $i++){
                    $activePg = $i == $pg? "bg-dark border-0" : "";
                ?>
                    <a href="./post.php?category=<?= $category ?>&pg=<?= $i ?>" class="btn btn-sm btn-primary mx-2 <?= $activePg ?>"><?= $i ?></a>
                    <?php
                }
                if ($totalPgs > $pg){
                    ?>
                    <a href="./post.php?category=<?= $category ?>&pg=<?= $pg+1 ?>" class="btn btn-sm btn-primary mx-2">Next</a>
                <?php } ?>
                </div>
            </div>
        </div>
     
    
</div>
        <?php } else { ?>
            <div style="margin-top: 4.7rem;" class="m-auto d-flex flex-column justify-content-center align-items-center w-100">
                <h4>No news in this category!</h4>
            </div>
        <?php } 
        ?>
</div>
<?php
require_once "./footer.php"
?>
