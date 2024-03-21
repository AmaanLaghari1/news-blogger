<?php
session_start();

// If user not logged in - redirect to login page
if ($_SESSION['role'] !== 'admin') {
  header("Location: ./index.php");
}

require_once "../config.php";

$con = db();


$title = "Posts";

if (isset($_POST['dlt'])) {
  // To delete the post image from the folder
  $sqlSel = "SELECT * FROM posts WHERE pid = {$_POST['pid']}";
  $selResult = $con->query($sqlSel);
  $row = $selResult->fetch_object();

  if (file_exists('uploads/' . $row->imgurl)) {
    unlink('uploads/' . $row->imgurl);
  }

  // Delete the entire post
  $dlt = "DELETE FROM posts WHERE pid = '" . $_POST['pid'] . "'";
  // Update no of post in categories TABLE
  if ($row->approved == 1) {
    $sqlUpd = "UPDATE categories c INNER JOIN posts p ON p.category = c.cid SET posts_no = posts_no - 1 WHERE p.approved != 0 AND c.cid = '" . $_POST['cid'] . "'";
    $sqlUpdResult = $con->query($sqlUpd);
  }
  $result = $con->query($dlt);
}
require_once "./header.php";
?>
<div id="Posts" class="container">
  <div class="row">
    <div class="table-responsive-sm">
      <div class="container-fluid d-flex jusitfy-content-around my-2">
        <div class="">
          <h1>Posts</h1>
        </div>
        <div class="w-75">
          <form class="search-box mx-auto d-flex w-100 px-3 my-2" action="./search.php" method="get">
            <input id="search-post" name="search" type="text" onkeyup="searchKey()" class="form-control rounded-0" placeholder="Search Posts">
          </form>
        </div>
        <div class="w-25 d-flex align-items-center justify-content-end">
          <a href="./addpost.php" class="btn btn-primary ms-auto my-auto">Add New</a>
        </div>
      </div>
      <div class="table-scroll">

        <table class="table table-striped table-light table-hover table-bordered mb-0">
          <thead>
            <tr>
              <th class="col-1">id</th>
              <th class="col-2">Title</th>
              <th class="col-1">Category</th>
              <th class="col-2">Description</th>
              <th class="col-1">Image</th>
              <th class="col-2">Date</th>
              <th class="col-1">Status</th>
              <th class="col-3">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Pagination Logic
            $limit = 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $sql = "SELECT * FROM posts ORDER BY pid DESC LIMIT {$offset}, {$limit}";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
              while ($post = $result->fetch_object()) {
            ?>
          <tbody id="search-result"></tbody>
          <tr class="data">
            <th class=""><?= $post->pid ?></th>
            <td class="">
              <?php
                if (strlen($post->title) > 30) {
                  echo substr_replace($post->title, '...', 30);
                } else {
                  echo $post->title;
                }
              ?>
            </td>
            <td class=""><?= $post->category ?></td>
            <td class="text-wrap ">
              <?php
                if (strlen($post->description) > 30) {
                  echo substr_replace($post->description, '...', 30);
                } else {
                  echo $post->description;
                }
              ?>
            </td>
            <td class="">
              <?php
                if (strlen($post->imgurl) > 15) {
                  echo substr_replace($post->imgurl, '...', 15);
                } else {
                  echo $post->imgurl;
                }
              ?>
            </td>
            <td class="small"><?= $post->date_created ?></td>
            <td><?php
                if ($post->approved == 1) {
                  echo 'Active';
                } else {
                  echo 'Pending';
                }
                ?></td>
            <td class="">
              <div class="d-flex">
                <a href="./updpost.php?id=<?= $post->pid ?>" class="btn btn-sm btn-outline-success mx-1">EDIT</a>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dltpost<?= $post->pid ?>">DELETE</button>

              </div>
            </td>
          </tr>

          <!-- Delete User Modal -->
          <div class="modal fade" id="dltpost<?= $post->pid ?>" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-dark">
                  Are you sure you want to delete this?
                </div>
                <div class="modal-footer">

                  <form action="" method="post">
                    <input type="number" class="d-none" name="pid" value="<?= $post->pid ?>">
                    <button class="btn btn-sm btn-danger" type="submit" name="dlt">DELETE</button>
                  </form>

                </div>
              </div>
            </div>
          </div>
          <!-- Delete User Modal End -->


        <?php }
            } else {
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
    if ($result1->num_rows > 0) {
      $totalRecords = $result1->num_rows;
      $limit = 10;
      $totalPages = ceil($totalRecords / $limit);
    ?>
      <div class="d-flex justify-content-center my-2">
        <ul class="list-unstyled d-flex">
          <?php
          if ($page > 1) {
          ?>
            <li><a href="./posts.php?page=<?= $page - 1 ?>" class="btn btn-sm btn-primary mx-1">Prev</a></li>
          <?php
          }
          for ($i = 1; $i <= $totalPages; $i++) {
            $activePage = $i == $page ? "bg-dark border-0" : "";
          ?>
            <li>
              <a href="./posts.php?page=<?= $i ?>" class="btn btn-sm btn-primary mx-1 <?= $activePage ?>"><?= $i ?></a>
            </li>
          <?php
          }
          if ($totalPages > $page) {
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
<script>
  let xhttp = new XMLHttpRequest()

  function searchKey() {
    let searchValue = document.getElementById('search-post').value

    let formData = new FormData()
    formData.append('searchValue', searchValue)
    formData.append('searchFrom', 'posts')
    formData.append('auth', 'search')

    xhttp.onreadystatechange = function() {
      let searchResult = document.getElementById('search-result')
      let data = document.querySelectorAll('.data')
      if (this.readyState === 4 && this.status === 200) {
        let html = ''
        if (searchValue !== '') {
          data.forEach(item => {
            item.classList.add('d-none')
          })
          if (this.response !== 'No results!') {
            let response = JSON.parse(this.response)
            response.forEach((item, ) => {
              html += `<tr>
              <td class='table-info'>${item.pid}</td>
              <td class='table-info'>${item.title.slice(0, 20)}...</td>
              <td class='table-info'>${item.category}</td>
              <td class='table-info'>${item.description.slice(0, 20)}...</td>
              <td class='table-info'>${item.imgurl}</td>
              <td class='table-info'>${item.date_created.slice(0, 10)}</td>
              <td class='table-info'>${item.approved == 1? 'active': 'pending'}</td>
              <td class='table-info'>
                <a href="./updpost.php?id=${item.pid}" class="btn btn-sm btn-outline-success mx-1">EDIT</a>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dltpost${item.pid}">DELETE</button>
              </td>
              </tr>`
            })
            searchResult.innerHTML = html
          } else {
            searchResult.innerHTML = `<td colspan='8' class='text-center table-info'>${this.response}</td>`
          }
        } else {
          data.forEach(item => {
            item.classList.remove('d-none')
          })
          searchResult.innerHTML = ''
        }
      }
    }

    xhttp.open('POST', '../../auth.php')
    xhttp.send(formData)
  }
</script>

<?php
require "./footer.php"
?>