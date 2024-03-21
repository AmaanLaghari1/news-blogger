<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Amaanullah Khan Laghari">
    <meta name="description" content="News blogger application which allows users to add their news and create blogs of the news.">
    <meta name="keywords" content="news and blogs, news blogger, akl, news headlines, news blogger application">
    <meta http-equiv="refresh" content="30">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../static/font-6/css/all.css">
    <link rel="stylesheet" href="../static/css/bootstrap.css">
    <link rel="stylesheet" href="./admin.css">
    <link rel="stylesheet" href="../static/css/mediaq.css">

</head>
<body>
<nav id="navbar" class="navbar navbar-expand-lg navbar-dark p-3 w-100">
            <div class="container-fluid">
                <a href="./home.php" class="navbar-brand fw-bolder">NEWS BLOGGER - <span class="fw-normal fst-italic">Admin</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navlist" aria-controls="navlist" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse flex-sm-column flex-md-row align-items-sm-center" id="navlist">
                    <ul class="navbar-nav d-flex justify-content-center align-items-center me-auto">
                        <li class="nav-item mx-3"><a href="./home.php" class="nav-link p-2">Home</a></li>
                        <li class="nav-item mx-3"><a href="./users.php" class="nav-link p-2">Users</a></li>
                        <li class="nav-item mx-3"><a href="./posts.php" class="nav-link p-2">Posts</a></li>
                        <li class="nav-item mx-3"><a href="./categories.php" class="nav-link p-2">Categories</a></li>
                    </ul>

                    <div class="dropdown mx-3 d-flex justify-content-center align-items-center">
                            <div class="btn-group">
                                <span class="text-light mx-1">
                                    <?= $_SESSION['username'] ?>
                                </span>
                                <a href="#" class="dropdown-item text-light dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-user-large"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end p-0">
                                    <li>
                                        <a href="../../home.php" class="dropdown-item">User End</a>
                                    </li>
                                    <li>
                                        <a href="./logout.php" class="dropdown-item">Logout</a>
                                    </li>
                                </ul>
                            </div>
                    </div>
            </div>
        </nav>
    