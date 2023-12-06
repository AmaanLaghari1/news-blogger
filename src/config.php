<?php

function db(){
    $server = "localhost";
    $username = "root";
    $password = "";
    $db = "news";

    $con = new mysqli($server, $username, $password, $db);

    if($con->connect_error){
        die("Connection failed: ". $con->connect_error);
    }

    return $con;
}

function cleanedData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}