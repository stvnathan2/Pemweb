<?php

function connection()
{
    $dbServer = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = "pemweb";

    $conn = mysqli_connect($dbServer, $dbUser, $dbPass);

    if (!$conn) {
        die('Koneksi gagal: ' . mysqli_error());
    }

    mysqli_select_db($conn, $dbName);

    return $conn;
}

?>
