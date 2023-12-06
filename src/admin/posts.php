<?php
session_start();

// If user not logged in - redirect to login page
if($_SESSION['role'] !== 'admin'){
  header("Location: ./index.php");
}

require_once "../config.php";

$con = db();


$title = "Posts";

if(isset($_POST['dlt'])){
  // To delete the post image from the folder
  $sqlSel = "SELECT * FROM posts WHERE pid = {$_POST['pid']}";
  $selResult = $con->query($sqlSel);
  $row = $selResult->fetch_object();

  if(file_exists('uploads/'. $row->imgurl)){
    unlink('uploads/'. $row->imgurl);
  }

  // Delete the entire post
  $dlt = "DELETE FROM posts WHERE pid = '".$_POST['pid']."'";
  // Update no of post in categories TABLE
  if($row->approved == 1){
    $sqlUpd = "UPDATE categories c INNER JOIN posts p ON p.category = c.cid SET posts_no = posts_no - 1 WHERE p.approved != 0 AND c.cid = '".$_POST['cid']."'";
    $sqlUpdResult = $con->query($sqlUpd);
  }
  $result = $con->query($dlt);
}
require_once "./header.php";
?>
<div id="Posts" class="container">
  <div class="row">
    <div class="table-responsive-sm">
      <div class="container-fluid d-flex jusitfy-content-around p-3">
          <h1>All Posts</h1>
        <a href="./addpost.php" class="btn btn-primary ms-auto my-auto">Add New</a>
      </div>
      <div class="table-scroll">

        <table class="table table-striped table-light table-hover table-bordered mb-0">
          <thead>
            <tr>
              <th>id</th>
              <th>Title</th>
              <th>Category</th>
              <th>Description</th>
              <th>Image</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Pagination Logic
            $limit = 10;
            $page = isset($_GET['page'])? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $sql = "SELECT * FROM posts ORDER BY pid DESC LIMIT {$offset}, {$limit}";
            $result = $con->query($sql);     
            if($result->num_rows > 0){
                while($post = $result->fetch_object()){
            ?>
            <tr>
                <th class=""><?= $post->pid ?></th>
                <td class="">
                  <?php 
                  if(strlen($post->title) > 30){ echo substr_replace($post->title, '...', 30);} else {
                    echo $post->title;
                  }
                  ?>  
                </td>
                <td class=""><?= $post->category ?></td>
                <td class="text-wrap "><?php
                 if(strlen($post->description) > 30){ echo substr_replace($post->description, '...', 30);} else {
                      echo $post->description;
                 }
                ?>
                </td>
                <td class="">
                  <?php
                  if(strlen($post->imgurl) > 15){ echo substr_replace($post->imgurl, '...', 15);} else {
                    echo $post->imgurl;
                  }
                  ?>
                </td>
                <td class="small"><?= $post->date_created ?></td>
                <td><?php 
                if($post->approved == 1){
                  echo 'Active';
                }
                else {
                  echo 'Pending';
                }
                ?></td>
                <td class="">
                  <div class="d-flex">
                    <a href="./updpost.php?id=<?= $post->pid ?>" class="btn btn-sm btn-outline-success mx-1">EDIT</a>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dltuser<?= $post->pid ?>">DELETE</button>
                    <div class="modal fade" id="dltuser<?= $post->pid ?>" aria-hidden="true" data-bs-backdrop="static">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body text-dark">
                            Are you sure you want to delete this?
                          </div>
                          <div class="modal-footer">
                            
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                              <input type="hidden" class="d-none" name="cid" value="<?= $post->category ?>">
                              <input type="hidden" class="d-none" name="pid" value="<?= $post->pid ?>">
                              <button class="btn btn-sm btn-danger" type="submit" name="dlt">DELETE</button>
                            </form>
  
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
            </tr>
            <?php }}
              else {
                ?>
            <tr>
              <td class="text-center" colspan="7">Nothing to show!</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
      <?php
      $sql1 = "SELECT * FROM posts";
      $result1 = $con->query($sql1);
      if($result1->num_rows > 0){
        $totalRecords = $result1->num_rows;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit);
      ?>
      <div class="d-flex justify-content-center my-2">
        <ul class="list-unstyled d-flex">
          <?php
            if($page > 1){
          ?>
            <li><a href="./posts.php?page=<?= $page - 1 ?>" class="btn btn-sm btn-primary mx-1">Prev</a></li>
            <?php
            }
            for($i = 1; $i <= $totalPages; $i++){
              $activePage = $i == $page? "bg-dark border-0" : "";
            ?>
            <li>
              <a href="./posts.php?page=<?= $i ?>" class="btn btn-sm btn-primary mx-1 <?= $activePage ?>"><?= $i ?></a>
            </li>
            <?php
            }
            if($totalPages > $page){
            ?>
            <li><a href="./posts.php?page=<?= $page + 1 ?>" class="btn btn-sm btn-primary mx-1">Next</a></li>

          <?php
            }
          ?>
        </ul>
      <?php
      }
      ?>
      </div>
  </div>
</div>
<?php
require "./footer.php"
?>