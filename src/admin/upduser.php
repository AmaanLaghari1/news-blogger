<?php
session_start();
// If admin not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
    header("Location: ./index.php");
}

require_once "../config.php";

$con = db();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])){
    $firstname = cleanedData($_POST['fname']);
    $lastname = cleanedData($_POST['lname']);
    $username = cleanedData($_POST['uname']);
    $password = cleanedData(sha1($_POST['password']));
    $role = cleanedData($_POST['role']);

    $sql = "UPDATE users SET firstname = '". $firstname ."' , lastname = '". $lastname ."', username = '". $username ."', password = '". $password ."', role = '". $role ."' WHERE user_id = '". $_POST['user_id'] ."'";
    
    $result = $con->query($sql);
    if($result){
        echo "user updated...";
        header("Location: ./users.php");
    }
    else {
        header("Location: ./upduser.php");
    }
}

$title = "Update User";
require_once "./header.php"
?>

<div class="container">
    <h1 class="text-light">Update User</h1>
    <?php
        $id = $_GET['id']? $_GET['id'] : "";
        $sql = "SELECT * FROM users WHERE user_id = '". $id ."'";
        $result = $con->query($sql);
        if($result->num_rows > 0){
            while($user = $result->fetch_object()){
    ?>
    <form class="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
        <div class="form-group my-2">
        <label for="fname" class="form-label">First Name<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="fname" id="fname" value="<?= $user->firstname ?>">
    </div>
    <div class="form-group my-2">
        <label for="lname" class="form-label">Last Name<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="lname" id="lname" value="<?= $user->lastname ?>">
    </div>
    <div class="form-group my-2">
        <label for="uname" class="form-label">Username<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="uname" id="uname" value="<?= $user->username ?>">
    </div>
    <div class="form-group my-2">
        <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
        <input type="password" class="form-control" name="password" id="password" value="<?= $user->password ?>">
    </div>
    <div class="form-group my-2">
        <label for="role" class="form-label">User Role<span class="text-danger">*</span></label>
        <select class="form-control" name="role">
            <?php if($user->role === 'primary'){ ?>
                <option value="primary" selected>Primary</option>
                <option value="admin">Admin</option>
            <?php } elseif($user->role === 'admin'){ ?>
                <option value="primary" selected>Primary</option>
                <option value="admin" selected>Admin</option>
            <?php } ?>
        </select>
    </div>
    <button type="submit" name="update" class="btn btn-primary my-2 px-5">UPDATE</button>
    </form>
    <?php }} ?>
</div>

<?php
require_once "./footer.php"
?>