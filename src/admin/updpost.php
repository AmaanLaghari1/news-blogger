<?php
session_start();
// If admin not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
    header("Location: ./index.php");
}

require_once "../config.php";

$con = db();

if (empty($_FILES['new-img']['name'])) {
    $fileName = isset($_POST['prev-img']) ? $_POST['prev-img'] : "";
} else {
    $errors = array();

    $fileName = $_FILES['new-img']['name'];
    $fileSize = $_FILES['new-img']['size'];
    $fileTmp = $_FILES['new-img']['tmp_name'];
    $fileType = $_FILES['new-img']['type'];
    $temp = explode('.', $fileName);
    $fileExt = end($temp);
    $extensions = array('jpg', 'jpeg', 'png', 'avif', 'webp');

    if (in_array($fileExt, $extensions) === false) {
        $errors[] = "Must be a jpg or png file format!";
    }
    if ($fileSize > 2097152) { // 2097152 bytes = 2MB
        $errors[] = "File size must be lower than 2MB!";
    }
    if (empty($errors)) {
        move_uploaded_file($fileTmp, "uploads/" . $fileName);
    } else {
        print_r($errors);
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $title = cleanedData($_POST['title']);
    $desc = cleanedData($_POST['desc']);
    $category = cleanedData($_POST['category']);
    $approve = isset($_POST['approve'])? cleanedData($_POST['approve']) : 0;
    $headline = isset($_POST['headline'])? cleanedData($_POST['headline']) : 0;
    $user = $_SESSION['user_id'];
    $date = date('Y-m-d H:i:s');

    $sql = "UPDATE posts SET date_created = '" . $date . "', title = '" . $title . "' , description = '" . $desc . "', imgurl = '" . $fileName . "', user = '" . $user . "', category = " . $category . ", approved = ". $approve .", headline = ". $headline ." WHERE pid = " . $_POST['post_id'] . "";
    // die();
    $sqlUpd = "UPDATE categories c INNER JOIN posts p SET c.posts_no = c.posts_no + 1 WHERE c.cid = {$category} AND p.approved = 1";
    $resultUpdate = $con->query($sqlUpd);
    $result = $con->query($sql);
    if ($result && $resultUpdate) {
        header("Location: ./posts.php");
    } else {
        header("Location: ./updpost.php");
    }
}

$title = "Update User";
require_once "./header.php"
?>

<div class="container">
    <h1 class="text-light">Update Post</h1>
    <?php
    $id = $_GET['id'] ? $_GET['id'] : "";
    $sql = "SELECT * FROM posts WHERE pid = '" . $id . "'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($post = $result->fetch_object()) {
    ?>
            <form class="form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?= $post->pid ?>">
                <div class="form-group my-2">
                    <label for="title" class="form-label">Title<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" id="title" value="<?= $post->title ?>">
                </div>
                <div class="form-group my-2">
                    <label for="category" class="form-label">Category<span class="text-danger">*</span></label>
                    <select class="form-control" name="category" id="category">
                        <?php
                        $sql = "SELECT * FROM categories";
                        $result = $con->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_object()) {
                                $IsSelected = $row->cid == $post->category ? "selected" : null;
                        ?>
                                <option value="<?= $row->cid ?>" <?= $IsSelected ?>><?= $row->category ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group my-2">
                    <label for="desc" class="form-label">Description<span class="text-danger">*</span></label>
                    <textarea class="form-control" name="desc" id="desc" value="<?= $post->description ?>"><?= $post->description ?></textarea>
                </div>
                <div class="form-group my-2">
                    <input type="hidden" name="prev-img" value="<?= $post->imgurl ?>">
                    <label for="new-img" class="form-label">Image<span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="new-img" id="new-img">
                    <img style="height: 8rem;" src="./uploads/<?= $post->imgurl ?>" class="img-thumbnail w-25" alt="current post image">
                </div>

                <div class="form-group my-2">
                    <label for="approve" class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="approve" id="approve" value="1"
                        <?= $post->approved == 1? 'checked' : '' ?> />
                        Approve
                    </label>
                </div>

                <div class="form-group my-2">
                    <label for="headline" class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="headline" id="headline" value="1" 
                        <?= $post->headline == 1? 'checked' : '' ?> />
                        Headline
                    </label>
                </div>

                <button type="submit" name="update" class="btn btn-primary my-2 px-5">UPDATE</button>
            </form>
    <?php }
    } ?>
</div>

<?php
require_once "./footer.php"
?>