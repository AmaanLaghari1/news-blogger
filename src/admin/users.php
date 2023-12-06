<?php
session_start();

// If admin not logged in - redirect to login page
if ($_SESSION['role'] !== 'admin') {
  header("Location: ./index.php");
}

require_once "../config.php";

$con = db();


$title = "Users";

if (isset($_POST['dlt'])) {
  $con->query('SET FOREIGN_KEY_CHECKS = 0');
  $dlt = "DELETE FROM users WHERE user_id = '" . $_POST['uid'] . "'";
  $result = $con->query($dlt);
  $con->query('SET FOREIGN_KEY_CHECKS = 1');
}
require_once "./header.php";
?>
<div id="Users" class="container my-3 text-light p-3">
  <div class="row">
    <div class="table-responsive-sm">
      <div class="container d-flex jusitfy-content-between my-2">
        <div class="">
          <h1>Users</h1>
        </div>
        <div class="w-75">
          <form class="search-box mx-auto d-flex w-100 px-3 my-2" action="./search.php" method="get">
            <input id="search-user" name="search" type="text" onkeyup="searchUser()" class="form-control rounded-0" placeholder="Search Users">
          </form>
        </div>
        <div class="w-25 d-flex align-items-center justify-content-end">
          <a href="./adduser.php" class="btn btn-sm btn-primary">Add New</a>
        </div>
      </div>
      <div class="table-scroll">

        <table class="container table mb-0 table-striped table-light table-hover table-bordered">
          <thead>
            <tr>
              <th class="col-1">id</th>
              <th class="col-2">First Name</th>
              <th class="col-2">Last Name</th>
              <th class="col-2">Username</th>
              <th class="col-2">Password</th>
              <th class="col-2">Role</th>
              <th class="col-1">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Pagination Logic
            $limit = 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $sql = "SELECT * FROM users ORDER BY role ASC LIMIT {$offset}, {$limit}";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
              while ($user = $result->fetch_object()) {
                $id = 'user-data'
            ?>
          <tbody id="user-search"></tbody>
          <!-- <div id="user-data"> -->

          <tr class="user-data">
            <th class=""><?= $user->user_id ?></th>
            <td class=""><?= $user->firstname ?></td>
            <td class=""><?= $user->lastname ?></td>
            <td class=""><?= $user->username ?></td>
            <td class="text-wrap">
              <?php
                if (strlen($user->password) > 10) echo substr_replace($user->password, '...', 10)
              ?>
            </td>
            <td class=""><?= $user->role ?></td>
            <td class="">
              <div class="d-flex">
                <a href="./upduser.php?id=<?= $user->user_id ?>" class="btn btn-sm btn-outline-success mx-1">EDIT</a>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dltuser<?= $user->user_id ?>">DELETE</button>

              </div>
            </td>
          </tr>


          <!-- Delete User Modal -->
          <div class="modal fade" id="dltuser<?= $user->user_id ?>" aria-hidden="true" data-bs-backdrop="static">
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
                    <input type="number" class="d-none" name="uid" value="<?= $user->user_id ?>">
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
      <?php
      $sql1 = "SELECT * FROM users";
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
              <li><a href="./users.php?page=<?= $page - 1 ?>" class="btn btn-sm btn-primary mx-1">Prev</a></li>
            <?php
            }
            for ($i = 1; $i <= $totalPages; $i++) {
              $activePage = $i == $page ? "bg-dark border-0" : "";
            ?>
              <li>
                <a href="./users.php?page=<?= $i ?>" class="btn btn-sm btn-primary mx-1 <?= $activePage ?>"><?= $i ?></a>
              </li>
            <?php
            }
            if ($totalPages > $page) {
            ?>
              <li><a href="./users.php?page=<?= $page + 1 ?>" class="btn btn-sm btn-primary mx-1">Next</a></li>

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
</div>
<script>
  let xhttp = new XMLHttpRequest()

  function searchUser() {
    let searchValue = document.getElementById('search-user').value

    let formData = new FormData()
    formData.append('searchValue', searchValue)
    formData.append('auth', 'search-user')

    xhttp.onreadystatechange = function() {
      let searchResult = document.getElementById('user-search')
      let userdata = document.querySelectorAll('.user-data')
      if (this.readyState === 4 && this.status === 200) {
        let html = ''
        if (searchValue !== '') {
          userdata.forEach(item => {
            item.classList.add('d-none')
          })
          if (this.response !== 'No results!') {
            let response = JSON.parse(this.response)
            response.forEach((item, ) => {
              html += `<tr>
              <td class='table-info'>${item.user_id}</td>
              <td class='table-info'>${item.firstname}</td>
              <td class='table-info'>${item.lastname}</td>
              <td class='table-info'>${item.username}</td>
              <td class='table-info'>${item.password}</td>
              <td class='table-info'>${item.role}</td>
              <td class='table-info'>
                <a href="./upduser.php?id=${item.user_id}" class="btn btn-sm btn-outline-success mx-1">EDIT</a>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dltuser${item.user_id}">DELETE</button>
              </td>
              </tr>`
            })
            searchResult.innerHTML = html
          } else {
            searchResult.innerHTML = `<td colspan='7' class='text-center table-info'>${this.response}</td>`
          }
        } else {
          userdata.forEach(item => {
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