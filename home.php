<?php
require_once "./src/config.php";

$con = db();



$title = "Home";
require_once "./header.php";
?>
<section>
    <div id="Home" class="container-fluid d-flex flex-column justify-content-center" style="min-height: 90vh;">
        <div class="container p-3 text-light card-hover-shadow headlines my-3">
            <h1 class="display-1">TOP HEADLINES</h1>


            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
            <?php
                $selectSql = "SELECT * FROM posts WHERE headline = 1 ORDER BY pid DESC LIMIT 10";
                $result = $con->query($selectSql);
                if($result->num_rows > 0){
                    foreach($result as $i => $row){
            ?>
                    <div class="carousel-item <?php if($i === 0) echo 'active' ?>">
                        <img src="./src/admin/uploads/<?= $row['imgurl'] ?>" class="img-thumbnail w-100 rounded-0" style="height: 22rem;" />
                        <h1>
                        <?php if(strlen($row['title']) > 50){ echo substr_replace($row['title'], '...', 50); } else { echo $row['title']; } ?>    
                        </h1>
                        <p>
                        <?php if(strlen($row['description']) > 300){ echo substr_replace($row['description'], '...', 300); } else { echo $row['description']; } ?>
                        </p>
                        <a href="./post_detail.php?id=<?= $row['pid'] ?>" class="d-block w-25 btn btn-primary ms-auto px-3">Read Full Story</a>
                    </div>
            <?php
                    }}
            ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div id="News-Section" class="container-fluid d-flex align-items-center py-3" style="min-height: 90vh;">
        <div class="container p-3">
            <h1>Top Categories</h1>
            <div class="row">
                <?php
                    $sqlSel = "SELECT * FROM categories";
                    $result = $con->query($sqlSel);
                    if($result->num_rows > 0){
                        while($row = $result->fetch_object()){
                ?>
                <div class="col-6 col-sm-6 col-md-4 my-2">
                    <a href="./post.php?category=<?= $row->category ?>" class="text-decoration-none text-dark">
                        <div class="card rounded-0">
                        <img src="./src/static/images/cat-thumbnails/<?= $row->img ?>" alt="" class="card-img-top rounded-0" style="height: 12rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?= strlen($row->category) > 20? substr_replace($row->category, '...', 20) : $row->category ?></h5>
                                <p class="card-text" style="font-size: .9rem;">No of Posts - 
                                <?= strlen($row->posts_no) > 100? substr_replace($row->posts_no, '...', 100) : $row->posts_no ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php }} else { ?>
                    <div class="display-4 d-block lead text-center m-auto">There's no news to show!</div>
                <?php } ?>
                
            </div>
        </div>
    </div>
</section>

<section id="Services">
    <div class="container-fluid position-relative" id="Services">
        <div class="container">
            <div class="row align-content-center text-light py-3" style="min-height: 90vh;">
                <div class="col-8">
                    <h1>Services We Provide</h1>
                    <p class="lead">
                    At News Blogger, we make sure you never miss a beat. From up-to-the-minute news headlines to comprehensive coverage of every major story, we’re your go-to source for everything you need to know. 5. Contact us message: Got a tip or a story to share? Drop us a line and let us know what’s on your mind. 6. Our mission: We believe in the power of news to inform, enlighten, and inspire. Our mission is to bring you the very best of what’s happening in the world, every single day.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require_once "./footer.php";
?>