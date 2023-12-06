<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Blogger - Admin Panel</title>
    <link rel="stylesheet" href="../static/css/all.css">
    <link rel="stylesheet" href="../static/css/bootstrap.css">
    <link rel="stylesheet" href="./admin.css">
    <link rel="stylesheet" href="../static/css/mediaq.css">
</head>
<body>
    <div id="Home" style="height: 100vh;" class="container-fluid d-flex flex-column justify-content-center p-0">
        <div class="mx-auto col-10 mt-4 py-5 px-3 text-light box-shadow heading">
            <h1 class="display-1 fw-bolder text-center fst-italic">
                News Blogger - Admin
            </h1>
        </div>
        <div class="d-flex m-3 align-items-center justify-content-center flex-wrap">
            <a href="./users.php" class="text-decoration-none col-xs-12 col-sm-12 col-md-5">
                <div class="card border-0 m-2 p-3 box-shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Users</h3>
                    </div>
                </div>
            </a>
            <a href="./posts.php" class="text-decoration-none col-xs-12 col-sm-12 col-md-5">
                <div class="card border-0 m-2 p-3 box-shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Posts</h3>
                    </div>
                </div>
            </a>
            <a href="./categories.php" class="text-decoration-none col-xs-12 col-sm-12 col-md-5">
                <div class="card border-0 m-2 p-3 box-shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Categories</h3>
                    </div>
                </div>
            </a>
            <a href="./users.php" class="text-decoration-none col-xs-12 col-sm-12 col-md-5">
                <div class="card border-0 m-2 p-3 box-shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Post Comments</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>