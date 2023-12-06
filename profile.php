<?php
ob_start();

$title = "User Profile";
require_once "./header.php";
?>
<section id="Profile">
    <div class="container-fluid">
        <h1>User Information</h1>
        <div class="row px-2">
            <table class="table w-75 table-responsive table-dark bg-dark">
                <?php
                $sqlSelect = "SELECT * FROM users WHERE user_id = {$_SESSION['user_id']}";
                $result = $con->query($sqlSelect);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_object()) {
                ?>
                        <tr>
                            <td>User Id</td>
                            <td colspan="2"><?= $row->user_id ?></td>
                        </tr>
                        <tr>
                            <td>First Name</td>
                            <td colspan="2"><?= $row->firstname ?></td>
                        </tr>
                        <tr>
                            <td>Last Name</td>
                            <td colspan="2"><?= $row->lastname ?></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td colspan="2"><?= $row->username ?></td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td><?= $row->password ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pwdModal">Change Password</button>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </table>
        </div>

        <!-- Change Password Modal -->
        <div class="modal modal-lg fade text-dark" id="pwdModal" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <button type="button" class="btn-close ms-auto my-2 me-2" data-bs-dismiss="modal"></button>
                    <form action="" method="post" class="p-3">
                        <h1 class="my-2">Change Password</h1>
                        <div id="msgs"></div>
                        <div class="form-group my-2">
                            <input type="password" class="form-control" id="old-pass" name="oldpass" placeholder="Old Password">
                        </div>
                        <div class="form-group my-2">
                            <input type="password" class="form-control" id="new-pass" name="newpass" placeholder="New Password">
                        </div>
                        <button onclick="changePass()" type="button" class="btn btn-sm btn-success my-2">Change</button>
                    </form>
                </div>
            </div>
        </div>



        <div class="row">
            <h2>Posts added by you</h2>
            <?php
            $sqlSelect = "SELECT * FROM posts p INNER JOIN users u ON p.user = u.user_id WHERE u.user_id = {$_SESSION['user_id']} AND p.approved = 1";
            $myPosts = $con->query($sqlSelect);
            if ($myPosts->num_rows > 0) {
                while ($row = $myPosts->fetch_assoc()) {
                    $postId = $row['pid'];
                    $modalId = "updPost". $postId ."";
            ?>
                    <div class="col-xs-6 col-sm-6 col-md-4 my-2 align-self-center p-3">
                        <a href="./post_detail.php?id=<?= $row['pid'] ?>" class="text-decoration-none text-dark">
                            <div class="card card-hover-shadow">
                                <img style="height: 14rem;" src="./src/admin/uploads/<?= $row['imgurl'] ?>" alt="" class="card-img-top">
                                <div class="card-body">
                                    <h5>
                                        <?php
                                        if (strlen($row['title']) > 50) {
                                            echo substr_replace($row['title'], '...', 50);
                                        } else {
                                            echo $row['title'];
                                        }
                                        ?>
                                    </h5>
                                    <div class="card-text">
                                        <?php
                                        if (strlen($row['description']) > 100) {
                                            echo substr_replace($row['description'], '...', 100);
                                        } else {
                                            echo $row['description'];
                                        }
                                        ?>
                                    </div>
                                    <div class="card-text d-flex">
                                        <form action="./delpost.php" method="post">
                                            <input type="hidden" name="cid" value="<?= $row['category'] ?>">
                                            <input type="hidden" name="pid" value="<?= $row['pid'] ?>">
                                            <button type="submit" name="delpost" class="btn btn-sm btn-danger rounded-0 d-block ms-auto my-2">DELETE</button>
                                        </form>
                                        <!-- Update Post Modal -->
                                        <a role="button" class="btn btn-sm btn-success rounded-0 d-block mx-2 my-2" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">EDIT</a>


                                        
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div id="<?= $modalId ?>" class="modal modal-lg fade text-dark" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content p-2">
                                    <div class="modal-header">
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <h1>Update Post</h1>
                                    <form action="" method="post">
                                        <div id="updpost-msgs<?= $postId ?>"></div>
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="title<?= $postId ?>">Title</label>
                                            <input type="text" class="form-control" id="title<?= $postId ?>" value="<?= $row['title'] ?>" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="form-label">Category</div>
                                            <select class="form-control" name="" id="category<?= $postId ?>" required>
                                            <?php
                                            $sql = "SELECT * FROM categories";
                                            $result = $con->query($sql);
                                            if ($result->num_rows > 0) {
                                                while ($cat = $result->fetch_object()) {
                                                    $IsSelected = $cat->cid == $row['category'] ? "selected" : null;
                                            ?>
                                                    <option value="<?= $cat->cid ?>" <?= $IsSelected ?>><?= $cat->category ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="desc<?= $postId ?>">Description</label>
                                            <textarea class="form-control" id="desc<?= $postId ?>" rows="3" required><?= $row['description'] ?></textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="hidden" id="prev-img<?= $postId ?>" value="<?= $row['imgurl'] ?>">
                                            <label for="new-img<?= $postId ?>" class="form-label">Image</label>
                                            <input type="file" class="form-control" name="new-img" id="new-img<?= $postId ?>">
                                            <img style="height: 8rem;" src="./src/admin/uploads/<?= $row['imgurl'] ?>" class="img-thumbnail w-25" alt="current post image">
                                        </div>

                                        <button type="button" onclick="updPost(<?= $postId ?>)" class="btn btn-outline-success px-3 mb-2">Update</button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <p>There are no posts added by you! <a href="#" role="button" data-bs-toggle="modal" data-bs-target="#addPost" class="link">Add new</a></p>
            <?php } ?>
        </div>
    </div>
</section>


<script>
    function changePass() {
        let oldpass = document.getElementById('old-pass').value;
        let newpass = document.getElementById('new-pass').value;
        let formData = new FormData();
        formData.append('user_id', <?= $_SESSION['user_id'] ?>);
        formData.append('oldpass', oldpass);
        formData.append('newpass', newpass);
        formData.append('auth', 'change-pass');

        xhttp.onreadystatechange = function() {

            if (this.readyState === 4 && this.status === 200) {
                let res = JSON.parse(this.response);
                let msgs = document.getElementById('msgs')

                if (res.status === 'error') {
                    msgs.classList.add('text-danger');
                    msgs.innerHTML = res.msg;
                } else if (res.status === 'success') {
                    msgs.classList.remove('text-danger');
                    msgs.classList.add('text-success');
                    msgs.innerHTML = res.msg;
                }
            }

        }
        xhttp.open('POST', 'auth.php');
        xhttp.send(formData);
    }

    function updPost(pid) {
        let title = document.getElementById(`title${pid}`).value;
        let desc = document.getElementById(`desc${pid}`).value;
        let category = document.getElementById(`category${pid}`).value;
        let filePrevImg = document.getElementById(`prev-img${pid}`).value;
        let fileNewImg = document.getElementById(`new-img${pid}`).files[0];

        let formData = new FormData();
        formData.append('title', title);
        formData.append('desc', desc);
        formData.append('category', category);
        formData.append('fileToUpload', fileNewImg);
        formData.append('prev-img', filePrevImg);
        formData.append('pid', pid);
        formData.append('user_id', <?= $_SESSION['user_id'] ?>);
        formData.append('auth', 'upd-post');

        xhttp.onreadystatechange = function() {
            let msgs = document.getElementById(`updpost-msgs${pid}`)
            if (this.readyState === 4 && this.status === 200) {
                res = JSON.parse(this.response);
                if (res.status === 'error') {
                    msgs.innerHTML = `<div class='alert alert-danger'>${res.msg}</div>`;
                } else {
                    msgs.innerHTML = `<div class='alert alert-success'>${res.msg}</div>`;
                }
            }
        }

        xhttp.open('POST', 'auth.php');
        xhttp.send(formData);
    }
</script>
<?php
require_once "./footer.php";
ob_end_flush();
?>