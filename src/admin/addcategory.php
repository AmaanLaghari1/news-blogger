<?php
session_start();

// If admin not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
    header("Location: ./index.php");
}

require_once "../config.php";

$con = db();

$fileName = '';
if(isset($_FILES['cat-img'])){
    $errors = array();

    $fileName = $_FILES['cat-img']['name'];
    $fileSize = $_FILES['cat-img']['size'];
    $fileTmp = $_FILES['cat-img']['tmp_name'];
    $fileType = $_FILES['cat-img']['type'];
    $temp = explode('.', $fileName);
    $fileExt = end($temp);
    $extensions = array('jpg', 'jpeg', 'png', 'avif', 'webp', 'PNG');

    if(in_array($fileExt, $extensions) === false){
        $errors[] = "Must be a jpg or png file format!";
    }
    if($fileSize > 2097152){ // 2097152 bytes = 2MB
        $errors[] = "File size must be lower than 2MB!";
    }
    if(empty($errors)){
        move_uploaded_file($fileTmp, "../static/images/cat-thumbnails/". $fileName);
    }
    else {
        print_r($errors);
        die();
    }
}

if(isset($_POST['save']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $category = cleanedData($_POST['category']);

    $sql = "SELECT * FROM categories WHERE category = '{$category}'";
    $result = $con->query($sql);
    if($result->num_rows > 0){
        echo "Category already exist!";
    }
    else{
        $sql2 = $con->prepare("INSERT INTO categories (category, img) VALUES (?, ?)");
        $sql2->bind_param('ss', $category, $fileName);

        $result = $sql2->execute();
        if($result){
            echo "new category added...";
            header("Location: ./categories.php");
        }
        else {
            echo "some error occured!";
        }
    }
}

$title = "Add Category";
require "./header.php";
?>
<div class="container p-3">

    <h1 class="text-light">Add New Category</h1>
    <form class="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group my-2">
            <label for="category" class="form-label">Category Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="category" id="category">
        </div>
        <div class="form-group my-2">
            <label for="cat-img" class="form-label">Thumbnail Image<span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="cat-img" id="cat-img">
        </div>
        <button type="submit" name="save" class="btn btn-primary mb-2 px-5">Save</button>
    </form>
</div>
<?php
require "./footer.php"
?>