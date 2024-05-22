<?php

function connection()
{
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $name = 'db_users';

    $conn = mysqli_connect($server, $user, $password, $name);

    if (!$conn){
        die('Koneksi gagal: ' . $conn->connect_error);
    }

    mysqli_select_db($conn, $name);

    return $conn;
}