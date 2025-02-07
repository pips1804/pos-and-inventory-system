<?php

session_start();
include '../db_connection/db_connect.php';

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $login = $db->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $login->bind_param("ss", $email, $password);
    $login->execute();
    $result = $login->get_result();

    if ($result->num_rows > 0) {

        header("Location: ../home.php");
        exit();
    }

}
else{

}

