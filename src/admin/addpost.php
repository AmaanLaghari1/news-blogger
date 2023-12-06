<?php
session_start();

// If admin not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
    header("Location: ./index.php");
}

require_once "../config.php";

$con = db();

if(isset($_FILES['fileToUpload'])){
    $errors = array();

    $fileName = $_FILES['fileToUpload']['name'];
    $fileSize = $_FILES['fileToUpload']['size'];
    $fileTmp = $_FILES['fileToUpload']['tmp_name'];
    $fileType = $_FILES['fileToUpload']['type'];
    $temp = explode('.', $fileName);
    $fileExt = end($temp);
    $extensions = array('jpg', 'jpeg', 'png', 'avif', 'webp');

    if(in_array($fileExt, $extensions) === false){
        $errors[] = "Must be a jpg or png file format!";
    }
    if($fileSize > 2097152){ // 2097152 bytes = 2MB
        $errors[] = "File size must be lower than 2MB!";
    }
    if(empty($errors)){
        move_uploaded_file($fileTmp, "uploads/". $fileName);
    }
    else {
        print_r($errors);
        die();
    }
}

if(isset($_POST['save']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = cleanedData($_POST['title']);
    $category = cleanedData($_POST['category']);
    $description = addslashes(htmlspecialchars($_POST['desc']));
    $approve = cleanedData($_POST['approve']);
    $headline = isset($_POST['headline']) ? cleanedData($_POST['headline']) : 0;
    $user = $_SESSION['user_id'];
    $date = date('Y-m-d H:i:s');

    $sqlIns = "INSERT INTO posts (title, description, imgurl, user, category, date_created, headline, approved) VALUES ('{$title}', '{$description}', '{$fileName}', {$user}, {$category}, '{$date}', {$headline}, {$approve})";
    // exit();
    $sqlUpd = "UPDATE categories SET posts_no = posts_no + 1 WHERE cid = {$category}";
    // die();
    $resultInsert = $con->query($sqlIns);
    $resultUpdate = $con->query($sqlUpd);
    if($resultInsert && $resultUpdate){
        header('Location: ./posts.php');
    }
}

$title = "Add Post";
require "./header.php";
?>
<div class="container">

    <h1 class="text-light">Add Posts</h1>
    <form class="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group my-2">
            <label for="title" class="form-label">Title<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" id="title">
        </div>
        <div class="form-group my-2">
            <label for="category" class="form-label">Category<span class="text-danger">*</span></label>
            <select class="form-control" name="category">
                <option value="">Select</option>
            <?php
                $sql1 = "SELECT * FROM categories";
                $result1 = $con->query($sql1);
                if($result1->num_rows > 0){
                    while($cat = $result1->fetch_object()){
            ?>
                <option value="<?= $cat->cid ?>"><?= $cat->category ?></option>
            <?php
                    }}
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

        <div class="form-group my-2">
            <label for="approve" class="form-check-label">
                <input type="checkbox" class="form-check-input" name="approve" id="approve" value="1">
                Approve
            </label>
        </div>

        <div class="form-group my-2">
            <label for="headline" class="form-check-label" value="Yes">
                <input type="checkbox" class="form-check-input" name="headline" id="headline" value="1">
                Headline
            </label>
        </div>

        <button type="submit" name="save" class="btn btn-primary mb-2 px-5">Save</button>
    </form>
</div>
<?php
require "./footer.php"
?>