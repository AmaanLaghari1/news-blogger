<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: ./index.php');
}
// echo $_SESSION['user_id'];
require_once "./src/config.php";
$con = db();

$sqlSel = "SELECT * FROM users WHERE user_id = {$_SESSION['user_id']}";
$user = null;
if($con->query($sqlSel)){
    $user = $con->query($sqlSel)->fetch_object();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Amaanullah Khan Laghari">
    <meta name="description" content="News blogger application which allows users to add their news and create blogs of the news.">
    <meta name="keywords" content="news and blogs, news blogger, akl, news headlines, news blogger application">
    <meta http-equiv="refresh" content="30">
    <title>News Blogger - <?= $title ?></title>
    <link rel="stylesheet" href="./src/static/font-6/css/all.css">
    <link rel="stylesheet" href="./src/static/css/bootstrap.css">
    <link rel="stylesheet" href="./src/static/css/style.css">
    <link rel="stylesheet" href="./src/static/css/mediaq.css">
        
</head>

<body>
    <header class="position-relative">
        <nav id="navbar" class="navbar navbar-expand-lg navbar-dark p-3 w-100">
            <div class="container-fluid">
                <a href="./index.php" class="navbar-brand fw-bolder">NEWS BLOGGER</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navlist" aria-controls="navlist" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse flex-sm-column flex-md-row align-items-sm-center" id="navlist">
                    <ul class="navbar-nav d-flex justify-content-center align-items-center m-auto">
                        <li class="nav-item mx-3"><a href="./home.php" class="nav-link p-2">Home</a></li>
                        <?php if($title === 'Home'){ ?>
                        <li class="nav-item mx-3"><a href="#Services" class="nav-link p-2">Services</a></li>
                        <?php } ?>
                        <li class="nav-item dropdown mx-3"><a href="#" class="nav-link dropdown-toggle p-2" data-bs-toggle="dropdown">Categories</a>
                            <ul class="dropdown-menu bg-dark">
                                <?php
                                    $dbConn = db();
                                    $sql = "SELECT * FROM categories";
                                    $result = $dbConn->query($sql);
                                    if($result->num_rows > 0){
                                        while($row = $result->fetch_object()){
                                ?>
                                    <li class="nav-item"><a href="./post.php?category=<?= $row->category ?>" class="nav-link"><?= $row->category ?></a></li>
                                <?php }} ?>
                            </ul>
                        </li>
                        
                        
                    </ul>

                    <form class="search-box mx-auto d-flex w-75 px-3 my-2" action="./search.php" method="get">
                            <input name="search" type="text" class="form-control rounded-0" placeholder="What's on your mind...">
                            <button type="submit" class="btn btn-outline-success rounded-0"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>

                    <div class="dropdown mx-3 d-flex justify-content-center align-items-center">
                            <div class="btn-group">
                                <span class="text-light mx-1">
                                    <?= $_SESSION['username'] ?>
                                </span>
                                <a href="#" class="dropdown-item text-light dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-user-large"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end p-0">
                                    <?php if($user->role === 'admin'){ ?>
                                    <li>
                                        <a href="./src/admin/index.php" class="dropdown-item"> Admin</a>
                                    </li>
                                    <?php } ?>
                                    <li>
                                        <a href="#" role="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addPost">Add post</a>
                                    </li>
                                    <li>
                                        <a href="./profile.php" class="dropdown-item">Profile</a>
                                    </li>
                                    <li>
                                        <a href="./logout.php" class="dropdown-item">Logout</a>
                                    </li>
                                </ul>
                            </div>
                    </div>
            </div>
        </nav>
    </header>

    <!-- Add Post Modal -->
    <div class="modal modal-lg fade text-dark" id="addPost" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <button type="button" class="btn-close ms-auto my-2 me-2" data-bs-dismiss="modal"></button>
                    <form class="p-2" method="post" enctype="multipart/form-data">
                        <h1 class="my-3">Add New Post</h1>
                        <div id="addpost-msgs"></div>


                        <div class="form-group my-2">
                            <label for="title" class="form-label">Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="title">
                        </div>
                        <div class="form-group my-2">
                            <label for="category" class="form-label">Category<span class="text-danger">*</span></label>
                            <select class="form-control" name="category" id="category">
                                <option value="">Select</option>
                                <?php
                                $sql1 = "SELECT * FROM categories";
                                $result1 = $con->query($sql1);
                                if ($result1->num_rows > 0) {
                                    while ($cat = $result1->fetch_object()) {
                                ?>
                                        <option value="<?= $cat->cid ?>"><?= $cat->category ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group my-2">
                            <label for="desc" class="form-label">Description<span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control" name="desc" id="desc"></textarea>
                        </div>

                        <div class="form-group my-2">
                            <label for="fileToUpload" class="form-label">Image<span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                        </div>

                        <div class="form-group my-2 d-none">
                            <label for="approve" class="form-check-label">
                                <input type="hidden" class="form-check-input" name="approve" id="approve" value="0">
                                Approve
                            </label>
                        </div>

                        <div class="form-group my-2 d-none">
                            <label for="headline" class="form-check-label" value="Yes">
                                <input type="hidden" class="form-check-input" name="headline" id="headline" value="0">
                                Headline
                            </label>
                        </div>

                        <button type="button" onclick="addPost()" name="save" class="btn btn-outline-primary mb-2 px-4">Save</button>





                    </form>
                </div>
            </div>
        </div>
        <!-- Add Post Modal End -->

        <script>
                const xhttp = new XMLHttpRequest();
        </script>
