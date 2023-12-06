<?php
// session_start();
require_once "./src/config.php";

$con = db();

// SIGNUP handler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    switch ($_POST['auth']) {
        case 'signup':
            $uname = cleanedData($_POST['uname']);
            $fname = cleanedData($_POST['fname']);
            $lname = cleanedData($_POST['lname']);
            $password = $_POST['password'];
            $msg = [];

            if (empty($uname)) {
                $msg['error'] = "Username is required!";
                echo json_encode($msg);
            } else if (empty($fname)) {
                $msg['error'] = "First Name is required!";
                echo json_encode($msg);
            } else if (empty($lname)) {
                $msg['error'] = "Last Name is required!";
                echo json_encode($msg);
            } else if (empty($password)) {
                $msg['error'] = "Password is required!";
                echo json_encode($msg);
            } else if (strlen($password) < 7) {
                $msg['error'] = "Password must be 8 characters long!";
                echo json_encode($msg);
            } else {
                $hashPassword = sha1($password);
                $insertSql = "INSERT INTO users (firstname, lastname, username, password, role) VALUES ('{$fname}', '{$lname}', '{$uname}', '{$hashPassword}', 'primary')";
                if ($con->query($insertSql)) {
                    $msg['success'] = "Account created successfully.";
                    echo json_encode($msg);
                }
            } // SIGNUP end    
            break;

        case 'login':
            $uname = cleanedData($_POST['username']);
            $password = sha1($_POST['password']);

            $selectSql = "SELECT * FROM users WHERE username = '{$uname}' AND password = '{$password}'";
            // die();

            $result = $con->query($selectSql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                session_start();
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['role'] = $row['role'];
                if ($_SESSION['role'] == 'admin') {
                    echo "./src/admin/";
                } else {
                    echo "./home.php";
                }
            } else {
                echo "Invalid credintials";
            }
            break;

        case 'add-comment':
            $comment = cleanedData($_POST['comment']);
            $uid = $_POST['uid'];
            $pid = $_POST['pid'];
            $date = date('Y-m-d H:i:s');
            if ($comment !== null) {
                $sqlInsert = $con->prepare("INSERT INTO post_comments (comment, comment_date, uid, pid) VALUES (?, ?, ?, ?)");
                $sqlInsert->bind_param('ssii', $comment, $date, $uid, $pid);
                if ($sqlInsert->execute()) {
                    $response = ['status' => 'success', 'msg' => 'comment added...'];
                    $response = json_encode($response);
                    echo $response;
                }
            }
            break;

        case 'upd-comment':
            $comment = cleanedData($_POST['comment']);
            $uid = $_POST['uid'];
            $pid = $_POST['pid'];
            $cid = $_POST['com_id'];
            if ($comment !== null) {
                $sqlUpdate = $con->query("UPDATE post_comments SET comment = '{$comment}' WHERE pid = $pid AND uid = $uid AND com_id = $cid");
                if ($sqlUpdate) {
                    echo 'success';
                }
            }
            break;

        case 'dlt-comment':
            $uid = $_POST['uid'];
            $pid = $_POST['pid'];
            $cid = $_POST['com_id'];
            if ($uid !== null) {
                $sqlDelete = $con->query("DELETE FROM post_comments WHERE pid = $pid AND uid = $uid AND com_id = $cid");
                if ($sqlDelete) {
                    $response = ['status' => 'success', 'msg' => 'comment updated...'];
                    $response = json_encode($response);
                    echo 'success';
                }
            }
            break;

        case 'show-comments':
            $pid = $_POST['pid'];
            $sqlSelect = "SELECT pc.com_id, pc.comment, pc.comment_date, pc.uid, pc.pid, u.username, u.firstname, u.lastname FROM post_comments pc INNER JOIN users u ON pc.uid = u.user_id WHERE pc.pid = {$pid}";
            // die();
            $result = $con->query($sqlSelect);
            if ($result->num_rows > 0) {
                $data = array();
                $data[] = ['status' => true];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                echo json_encode($data);
            } else {
                $data[] = ['status' => false, 'msg' => "No comments to show!"];
                echo json_encode($data);
            }
            break;

        case 'change-pass':
            $sql = "SELECT password FROM users WHERE user_id = {$_POST['user_id']}";
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                $oldpass = $result->fetch_assoc()['password'];
                // echo $oldpass;

                $userInputOldPass = $_POST['oldpass'] === '' ? null : sha1($_POST['oldpass']);
                $userInputNewPass = $_POST['newpass'] === '' ? null : sha1($_POST['newpass']);

                if ($userInputNewPass === null || $userInputOldPass === null) {
                    $msgs =  json_encode(["status" => "error", "msg" => "Passwords cannot be empty!"]);
                    echo $msgs;
                    return;
                } elseif ($oldpass !== $userInputOldPass) {
                    $msgs =  json_encode(["status" => "error", "msg" => "Old Password is incorrect!"]);
                    echo $msgs;
                    return;
                } elseif (strlen($_POST['newpass']) < 7) {
                    $msgs =  json_encode(["status" => "error", "msg" => "Password must be atleast 8 characters long!"]);
                    echo $msgs;
                    return;
                } else {
                    $sqlUpdate = "UPDATE users SET password = '{$userInputNewPass}' WHERE password = '{$userInputOldPass}'";
                    if ($con->query($sqlUpdate)) {
                        $msgs =  json_encode(["status" => "success", "msg" => "Password changed..."]);
                        echo $msgs;
                    } else {
                        $msgs =  json_encode(["status" => "error", "msg" => "Invalid inputs!"]);
                        echo $msgs;
                    }
                }
            }
            break;

        case 'add-post':
            $msgs = array();
            // Image File
            if (isset($_FILES['fileToUpload'])) {

                $fileName = $_FILES['fileToUpload']['name'];
                $fileSize = $_FILES['fileToUpload']['size'];
                $fileTmp = $_FILES['fileToUpload']['tmp_name'];
                $fileType = $_FILES['fileToUpload']['type'];
                $temp = explode('.', $fileName);
                $fileExt = end($temp);
                $extensions = array('jpg', 'jpeg', 'png', 'avif', 'webp');

                if (in_array($fileExt, $extensions) === false) {
                    $msgs = ["status" => "error", "msg" => "Must be a jpg or png file format!"];
                    echo json_encode($msgs);
                }
                if ($fileSize > 2097152) { // 2097152 bytes = 2MB
                    $msgs = ["status" => "error", "msg" => "File size must be lower than 2MB!"];
                    echo json_encode($msgs);
                }
                if (empty($msgs)) {
                    move_uploaded_file($fileTmp, "./src/admin/uploads/" . $fileName);
                }
            }


            $title = cleanedData($_POST['title']);
            $category = cleanedData($_POST['category']);
            $description = addslashes(htmlspecialchars($_POST['desc']));
            $approve = cleanedData($_POST['approve']);
            $headline = isset($_POST['headline']) ? cleanedData($_POST['headline']) : 0;
            $user = $_POST['user_id'];
            $date = date('Y-m-d H:i:s');

            $sqlIns = "INSERT INTO posts (title, description, imgurl, user, category, date_created, headline, approved) VALUES ('{$title}', '{$description}', '{$fileName}', {$user}, {$category}, '{$date}', {$approve}, {$headline})";
            // exit();
            // die();
            $resultInsert = $con->query($sqlIns);
            if ($resultInsert) {
                $msgs = ["status" => "success", "msg" => "Your post will be added soon as admin approve..."];
                echo json_encode($msgs);
            }
            break;

        case 'upd-post':
            $msgs = array();
            // Image File
            if (empty($_FILES['fileToUpload']['name'])) {
                $fileName = isset($_POST['prev-img']) ? $_POST['prev-img'] : "";
            } else {
                $fileName = $_FILES['fileToUpload']['name'];
                $fileSize = $_FILES['fileToUpload']['size'];
                $fileTmp = $_FILES['fileToUpload']['tmp_name'];
                $fileType = $_FILES['fileToUpload']['type'];
                $temp = explode('.', $fileName);
                $fileExt = end($temp);
                $extensions = array('jpg', 'jpeg', 'png', 'avif', 'webp');

                if (in_array($fileExt, $extensions) === false) {
                    $msgs = ["status" => "error", "msg" => "Must be a jpg or png file format!"];
                    echo json_encode($msgs);
                }
                if ($fileSize > 2097152) { // 2097152 bytes = 2MB
                    $msgs = ["status" => "error", "msg" => "File size must be lower than 2MB!"];
                    echo json_encode($msgs);
                }
                if (empty($msgs)) {
                    move_uploaded_file($fileTmp, "./src/admin/uploads/" . $fileName);
                }
            }

            $title = cleanedData($_POST['title']);
            $desc = cleanedData($_POST['desc']);
            $category = cleanedData($_POST['category']);
            $user = $_POST['user_id'];
            $pid = $_POST['pid'];
            $date = date('Y-m-d H:i:s');

            $sql = "UPDATE posts SET date_created = '" . $date . "', title = '" . $title . "' , description = '" . $desc . "', imgurl = '" . $fileName . "', user = '" . $user . "', category = " . $category . " WHERE pid = " . $pid . "";
            // die();
            $result = $con->query($sql);
            if ($result) {
                $msgs = ["status" => "success", "msg" => "Post updated successfully..."];
                echo json_encode($msgs);
            } else {
                $msgs = ["status" => "error", "msg" => "Error updating the post!"];
                echo json_encode($msgs);
            }
            break;

        // Requests From Admin Panel
        case 'search-user':
            $searchValue = $_POST['searchValue'];
            $searchFrom = $_POST['searchFrom'];
            if($searchFrom === 'category'){
                $sqlSelect = "SELECT * FROM categories WHERE category LIKE '%{$searchValue}%'";
            }
            else {
                $sqlSelect = "SELECT * FROM users WHERE username LIKE '%{$searchValue}%' OR firstname LIKE '%{$searchValue}%' OR lastname LIKE '%{$searchValue}%'";
            }
            $result = $con->query($sqlSelect);
            if($result->num_rows > 0){
                $data = [];
                foreach($result as $user){
                    $data[] = $user;
                }
                echo json_encode($data);
            }
            else {
                echo 'No results!';
            }
            break;

        default:
            echo null;
            break;
    }
}
