<?php
session_start();

if(isset($_SESSION['user_id'])){
    header('Location: ./home.php');
}

require_once "./src/config.php";
$con = db();

$sqlSel = "SELECT posts.pid, posts.title, posts.description, posts.category, posts.imgurl, posts.user, posts.date_created FROM posts LEFT JOIN categories ON posts.category = categories.cid LEFT JOIN users ON posts.user = users.user_id ORDER BY posts.pid DESC";

?>
<!DOCTYPE html>
<html>

<head>
    <title>News Bloggger</title>
    <link rel="stylesheet" href="./src/static/css/all.css">
    <link rel="stylesheet" href="./src/static/css/bootstrap.css">
    <!-- <link rel="stylesheet" href="./src/static/css/style.css"> -->
    <link rel="stylesheet" href="./src/static/css/mediaq.css">

    <style>
        #Auth {
    background: linear-gradient(rgba(0, 0, 0, .6), rgba(0, 0, 0, .6)), url('./src/static/images/newsbg.jpg');
    background-repeat: no-repeat;
    background-size: cover;
}

#Login .modal-content, #Signup .modal-content {
    background: rgba(250, 250, 250, 0.7);
    max-width: 200%;
    color: #000;
}
    </style>
</head>

<body>

    <section>
        <div class="container-fluid" style="height: 100vh;" id="Auth">
            <nav class="navbar navbar-lg-expand navbar-dark p-3">
                <div class="container-fluid">
                    <a href="./index.php" class="navbar-brand fw-bolder">NEWS BLOGGER</a>
                </div>
            </nav>
            <div class="container h-75 d-flex flex-column justify-content-center text-light">
                <h1 class="display-1 fw-bolder">NEWS BLOGGER</h1>
                <p class="lead">
                Get all the latest news in one place. From breaking news to in-depth reporting on everything you care about, our news headlines section has got you covered.
                </p>
                <button type="button" class="btn btn-outline-danger border border-3 text-light rounded-0 w-25 p-2" data-bs-toggle="modal" data-bs-target="#Login">Get Started</button>

                <!-- Login Modal -->
                <div class="modal modal-lg fade text-dark" id="Login" data-bs-backdrop="static" aria-hidden="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h1 class="text-center modal-title">
                                    Login
                                </h1>
                                <form action="" method="post">
                                    <div class="form-group my-3">
                                        <div class="alert alert-danger d-none" id="login-msg"></div>
                                        <label for="uname_log" class="form-label fw-bold">Username</label>
                                        <input type="text" class="form-control p-2" name="uname_log" id="uname_log">
                                    </div>
                                    <div class="form-group my-3">
                                        <label for="password_log" class="form-label fw-bold">Password</label>
                                        <input type="password" class="form-control p-2" name="password_log" id="password_log">
                                    </div>
                                    <button type="button" onclick="login()" class="btn btn-primary w-100">LOGIN</button>
                                    <p class="my-2">Don't have an account? <a type="button" class="link bg-none" data-bs-toggle="modal" data-bs-target="#Signup">Signup</a></p>
                                </form>
                            </div>
                            <div class="modal-footer border-0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signup Modal -->
                <div class="modal modal-lg fade" id="Signup" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h1 class="text-center">Sign Up</h1>
                                <form action="" method="post" id="LoginForm">
                                    <div class="alert alert-danger d-none" id="signup-msg"></div>
                                    <div class="form-group my-3">
                                        <label for="fname" class="form-label fw-bold">First Name</label>
                                        <input type="text" class="form-control p-2" name="fname" id="fname">
                                    </div>
                                    <div class="form-group my-3">
                                        <label for="lname" class="form-label fw-bold">Last Name</label>
                                        <input type="text" class="form-control p-2" name="lname" id="lname">
                                    </div>
                                    <div class="form-group my-3">
                                        <label for="uname" class="form-label fw-bold">Username</label>
                                        <input type="text" class="form-control p-2" name="uname" id="uname">
                                    </div>
                                    <div class="form-group my-3">
                                        <label for="password" class="form-label fw-bold">Password</label>
                                        <input type="password" class="form-control p-2" name="password" id="password">
                                    </div>
                                    <button type="button" name="signup" onclick="save()" class="btn btn-primary w-100">SIGNUP</button>
                                    <p class="my-2">Already have an account? <a type="button" class="link bg-none" data-bs-toggle="modal" data-bs-target="#Login">Login</a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script src="./src/static/js/popper.js"></script>
<script src="./src/static/js/bootstrap.js"></script>
<script>
    const xhttp = new XMLHttpRequest();

    function save() {
        let formData = new FormData(document.getElementById('LoginForm'));
        formData.append('auth', 'signup');

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let response = JSON.parse(this.responseText);
                // console.log(response);
                // window.location = this.responseText;
                if (response.error) {
                    let msg = document.getElementById('signup-msg');
                    msg.innerHTML = response.error;
                    msg.classList.remove('d-none');
                }
                if (response.success) {
                    let msg = document.getElementById('signup-msg');
                    msg.innerHTML = response.success;
                    msg.classList.remove('d-none');
                    msg.classList.remove('alert-danger');
                    msg.classList.add('alert-success');
                }
            }
        }

        xhttp.open('POST', 'auth.php');
        xhttp.send(formData);
    }

    function login() {
        let username = document.getElementById('uname_log').value;
        let password = document.getElementById('password_log').value;
        let formdata = new FormData();
        formdata.append('username', username);
        formdata.append('password', password);
        formdata.append('auth', 'login');

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if(this.responseText == './home.php'){
                    window.location = this.responseText;
                }
                else if(this.responseText == './src/admin/'){
                    window.location = this.responseText;
                }
                else {
                    let msg = document.getElementById('login-msg');
                    msg.classList.remove('d-none');
                    msg.innerHTML = this.responseText;
                }
            }
        }

        xhttp.open('POST', 'auth.php');
        xhttp.send(formdata);
    }
</script>

</html>