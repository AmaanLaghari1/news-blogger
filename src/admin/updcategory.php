<?php
session_start();
// If admin not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
    header("Location: ./index.php");
}

require_once "../config.php";

$con = db();

if (empty($_FILES['new-cat-img']['name'])) {
    $fileName = isset($_POST['prev-img']) ? $_POST['prev-img'] : "";
} else {
    $errors = array();

    $fileName = $_FILES['new-cat-img']['name'];
    $fileSize = $_FILES['new-cat-img']['size'];
    $fileTmp = $_FILES['new-cat-img']['tmp_name'];
    $fileType = $_FILES['new-cat-img']['type'];
    $temp = explode('.', $fileName);
    $fileExt = end($temp);
    $extensions = array('jpg', 'jpeg', 'png', 'avif', 'webp', 'PNG');

    if (in_array($fileExt, $extensions) === false) {
        $errors[] = "Must be a jpg or png file format!";
    }
    if ($fileSize > 2097152) { // 2097152 bytes = 2MB
        $errors[] = "File size must be lower than 2MB!";
    }
    if (empty($errors)) {
        move_uploaded_file($fileTmp, "../static/images/cat-thumbnails/" . $fileName);
    } else {
        print_r($errors);
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $category = cleanedData($_POST['category']);

    $sql = "UPDATE categories SET category = '{$category}', img = '{$fileName}' WHERE cid = {$_POST['cid']}";
    // die();
    $result = $con->query($sql);
    if ($result) {
        header("Location: ./categories.php");
    } else {
        header("Location: ./updcategory.php");
    }
}

$title = "Update User";
require_once "./header.php"
?>

<div class="container">
    <h1 class="text-light">Update Category</h1>
    <?php
    $id = $_GET['id'] ? $_GET['id'] : "";
    $sql = "SELECT * FROM categories WHERE cid = '" . $id . "'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
    ?>
            <form class="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="cid" value="<?= $row->cid ?>">
                <div class="form-group my-2">
                    <label for="category" class="form-label">Category Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="category" id="category" value="<?= $row->category ?>">
                </div>
                <div class="form-group my-2">
                    <input type="hidden" name="prev-img" value="<?= $row->img ?>">
                    <label for="new-cat-img" class="form-label">Image<span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="new-cat-img" id="new-cat-img">
                    <img style="height: 8rem;" src="../static/images/cat-thumbnails/<?= $row->img ?>" class="img-thumbnail w-25" alt="current post image">
                </div>

                <button type="submit" name="update" class="btn btn-primary my-2 px-5">UPDATE</button>
            </form>
    <?php }
    } ?>
</div>

<?php
require_once "./footer.php"
?>