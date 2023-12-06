<?php
require_once "./src/config.php";

$con = db();

$title = "News Detail";
require_once "./header.php";
?>

<section style="margin-top: 4.7rem;">
<input type="hidden" id="session-user-id" value="<?= $_SESSION['user_id'] ?>">

    <div class="container-fluid position-relative p-5">
        <div class="form-check form-switch position-absolute" style="top: 1%; right: 5%;">
            <input class="form-check-input" type="checkbox" id="mode" onclick="switchMode()">
            <label class="form-check-label" for="mode">Switch Dark Mode</label>
        </div>
        <div id="post-detail" class="container">
            <?php
            $sqlSelect = "SELECT * FROM posts p INNER JOIN users u ON p.user = u.user_id WHERE p.pid = {$_GET['id']}";
            $result = $con->query($sqlSelect);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
            ?>
                <img src="./src/admin/uploads/<?= $row['imgurl'] ?>" alt="" class="img-thumbnail my-2 w-100" style="height: 30rem;">
                <h1 class="display-4 my-3">
                    <?= $row['title'] ?>
                </h1>
                <p class="lead">
                    <i>posted by</i> <?= $row['firstname'] . " " . $row['lastname'] ?>
                </p>
                <p class="text-danger">
                    <?= $row['date_created'] ?>
                </p>
                <p class="lead">
                <h2>Article - </h2>
                <div style="line-height: 1.8em;">
                    <?= $row['description'] ?>
                </div>
                </p>
            <?php
            }
            ?>
            <div class="row">
                <h2>Comments</h2>
                <div style="height: 14rem; overflow-y: scroll;" class="col-xs-12 col-sm-12 col-md-6" id="comments-box">

                </div>
                <?php

                ?>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <h5>Leave a comment...</h5>
                    <div id="msg"></div>
                    <form method="post">
                        <div class="form-group d-flex my-2">
                            <textarea class="form-control w-75" name="comment" id="comment" cols="30" rows="1"></textarea>
                            <input type="hidden" id="uid" value="<?= $_SESSION['user_id'] ?>">
                            <input type="hidden" id="pid" value="<?= $_GET['id'] ?>">
                            <button onclick="addComment()" type="button" name="post-comment" class="btn btn-primary mx-2">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // const xhttp = new XMLHttpRequest();

    function switchMode() {
        document.getElementById('post-detail').classList.toggle('dark-mode');
    }

    function showComment() {
        let comment = document.getElementById('comment').value;
        let uid = document.getElementById('uid').value;
        let pid = document.getElementById('pid').value;
        let sessionId = document.getElementById('session-user-id').value;
        
        let formdata = new FormData()
        formdata.append('pid', <?= $_GET['id'] ?>);
        formdata.append('auth', 'show-comments');
        
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let res = JSON.parse(this.response);
                let html = '';
                if (res[0].status === true) {
                    for (let i = 1; i < res.length; i++) {
                        const isUser = sessionId === res[i].uid;
                        html +=
                            `<div>
                    <p><i class="fa-solid fa-user"></i> <b>${res[i].firstname} ${res[i].lastname}</b> 
                    <span class='small' style="font-size: 70%;">${res[i].comment_date}</span>
                    <br/>
                    ${res[i].comment} <br/>
                    ${isUser ? `
                        <button class="btn btn-sm btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#editComment${res[i].com_id}" aria-expanded="false" aria-controls="editComment${res[i].com_id}">
                            Edit
                        </button>
                        
                        <button class="btn btn-sm btn-link text-danger" type="button" data-bs-toggle="collapse" data-bs-target="#dltComment${res[i].com_id}" aria-expanded="false" aria-controls="dltComment${res[i].com_id}">
                            Delete
                        </button>
                        
                        `
                        : ''}
                    <div class="collapse" id="editComment${res[i].com_id}">
                        <form>
                            <input type="hidden" id="upd-com_id${res[i].com_id}" value="${res[i].com_id}"/>
                            <input type="hidden" id="upd-uid${res[i].com_id}" value="${res[i].uid}"/>
                            <input type="hidden" id="upd-pid${res[i].com_id}" value="${res[i].pid}"/>
                            <div class="form-group d-flex">
                                <input class="form-control rounded-0" type="text" id="upd-comment${res[i].com_id}" value="${res[i].comment}"/>
                                <button class="btn btn-sm btn-success rounded-0" type="button" onclick="updComment(${res[i].com_id})">save</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="collapse" id="dltComment${res[i].com_id}">
                        <div class="d-flex"> Are you sure to delete this comment?
                            <form>
                            <input type="hidden" id="dlt-com_id${res[i].com_id}" value="${res[i].com_id}"/>
                            <input type="hidden" id="dlt-uid${res[i].com_id}" value="${res[i].uid}"/>
                            <input type="hidden" id="dlt-pid${res[i].com_id}" value="${res[i].pid}"/>
                            <button class="btn btn-sm btn-danger" type="button" onclick="dltComment(${res[i].com_id})">delete</button>
                            </form>
                        </div>
                    </div>

                    </p>
                    </div>`;
                    }
                } else if (res[0].status === false) {
                    html = `<p>${res[0].msg}</p>`
                }
                const comments = document.getElementById('comments-box')
                comments.innerHTML = html;
                comments.scrollTo({
                    top: comments.scrollHeight,
                    behavior: 'smooth'
                })
            }
        }
        xhttp.open('POST', 'auth.php');
        xhttp.send(formdata);
    }
    showComment();

    function addComment() {
        let comment = document.getElementById('comment').value;
        let uid = document.getElementById('uid').value;
        let pid = document.getElementById('pid').value;

        let formdata = new FormData()
        formdata.append('comment', comment);
        formdata.append('uid', uid);
        formdata.append('pid', pid);
        formdata.append('auth', 'add-comment');

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let res = JSON.parse(this.response);
                if (res.status === 'success') {
                    document.getElementById('msg').innerHTML =
                        `
                    <div class='alert alert-success alert-dismissible'>
                    ${res.msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    `;
                    showComment()
                } else {

                }
            }
        }
        xhttp.open('POST', 'auth.php');
        xhttp.send(formdata);
    }

    function updComment(id) {
        let uid = document.getElementById(`upd-uid${id}`).value
        let pid = document.getElementById(`upd-pid${id}`).value
        let com_id = document.getElementById(`upd-com_id${id}`).value
        let comment = document.getElementById(`upd-comment${id}`).value

        let formData = new FormData()
        formData.append('uid', uid)
        formData.append('pid', pid)
        formData.append('com_id', com_id)
        formData.append('comment', comment)
        formData.append('auth', 'upd-comment')

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.responseText)
                showComment()
            }
        }

        xhttp.open('POST', 'auth.php')
        xhttp.send(formData)
    }

    function dltComment(id) {
        let uid = document.getElementById(`dlt-uid${id}`).value
        let pid = document.getElementById(`dlt-pid${id}`).value
        let com_id = document.getElementById(`dlt-com_id${id}`).value

        let formData = new FormData()
        formData.append('uid', uid)
        formData.append('pid', pid)
        formData.append('com_id', com_id)
        formData.append('auth', 'dlt-comment')

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.responseText)
                showComment()
            }
        }

        xhttp.open('POST', 'auth.php')
        xhttp.send(formData)
    }
</script>

<?php
require_once "./footer.php";
