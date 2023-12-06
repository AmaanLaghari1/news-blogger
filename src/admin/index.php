<?php
session_start();
// If user logged in - redirect to users page
if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: ./home.php");
    }
}

require_once "../config.php";

$con = db();

$errMsg = null;
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])){
    $username = cleanedData($_POST['uname']);
    $password = cleanedData(sha1($_POST['password']));
    $errMsg = "";

    $sql = "SELECT user_id, username, role FROM users WHERE username = '{$username}' AND password = '{$password}'";
    // die(); 
    $result = $con->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_object()){
            if($row->role === 'admin'){
                // session_start();
                $_SESSION['username'] = $row->username;
                $_SESSION['role'] = $row->role;
                $_SESSION['user_id'] = $row->user_id;
                header('Location: ./home.php');
            }
            else {
                $errMsg = "Only admins are allowed access!";
            }
        }
    }
    else {
        $errMsg = "Invalid credintials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Login</title>
    <link rel="stylesheet" href="../static/css/all.css">
    <link rel="stylesheet" href="../static/css/bootstrap.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <style>
        #Login {
            height: 100vh;
            /* background-color: #000; */
        }
    </style>
</head>
<body>
    <section id="Login" class="container-fluid d-flex flex-column justify-content-center align-items-center text-ligh">
            <h1 class="display-1 p-3 bg-dark text-light w-50 text-center">NEWS BLOGGER</h1>
            <h1>Admin Login</h1>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" class="w-50" method="post">
                <?php if(isset($_POST['login'])){ ?>
                <div class="alert alert-danger"><?= $errMsg ?></div>
                <?php } ?>
                <div class="form-group my-2">
                    <label for="uname" class="form-label">Username</label>
                    <input type="text" class="form-control" name="uname" id="uname">
                </div>
                <div class="form-group my-2">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
                <button type="submit" name="login" class="btn btn-primary bg-gradient w-100 my-2">LOGIN</button>
            </form>
    </section>
</body>
<script src="../static/js/popper.js"></script>
<script src="../static/js/bootstrap.js"></script>
</html>