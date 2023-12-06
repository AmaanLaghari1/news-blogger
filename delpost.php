<?php
require_once "./src/config.php";

$con = db();

if(isset($_POST['delpost'])){
    $sql = "DELETE FROM posts WHERE pid = {$_POST['pid']}";
    $sqlUpd = "UPDATE categories SET posts_no = posts_no - 1 WHERE cid = '".$_POST['cid']."'";
    $con->query($sqlUpd);
    if($con->query($sql)){
        header('Location: ./profile.php');
    }
}