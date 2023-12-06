<?php
session_start();

// If admin not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
    header("Location: ./index.php");
}

require_once "../config.php";

$con = db();

if(isset($_POST['save']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $firstname = cleanedData($_POST['fname']);
    $lastname = cleanedData($_POST['lname']);
    $username = cleanedData($_POST['uname']);
    $password = cleanedData(md5($_POST['password'], true));
    $role = cleanedData($_POST['role']);

    $sql = "SELECT * FROM users WHERE username = '{$username}'";
    $result = $con->query($sql);
    if($result->num_rows > 0){
        echo "User already exist!";
    }
    else{
        $sql2 = $con->prepare("INSERT INTO users (firstname, lastname, username, password, role) VALUES (?, ?, ?, ?, ?)");
        $sql2->bind_param('sssss', $firstname, $lastname, $username, $password, $role);

        $result = $sql2->execute();
        if($result){
            echo "new user added...";
            header("Location: ./users.php");
        }
        else {
            echo "some error occured!";
        }
    }
}

$title = "Add User";
require "./header.php";
?>
<div class="container p-3">

    <h1 class="text-light">Add New User</h1>
    <form class="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="form-group my-2">
            <label for="fname" class="form-label">First Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="fname" id="fname">
        </div>
        <div class="form-group my-2">
            <label for="lname" class="form-label">Last Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="lname" id="lname">
        </div>
        <div class="form-group my-2">
            <label for="uname" class="form-label">Username<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="uname" id="uname">
        </div>
        <div class="form-group my-2">
            <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <div class="form-group my-2">
            <label for="role" class="form-label">User Role<span class="text-danger">*</span></label>
            <select class="form-control" name="role">
                <option value="primary">Primary</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="save" class="btn btn-primary mb-2 px-5">Save</button>
    </form>
</div>
<?php
require "./footer.php"
?>