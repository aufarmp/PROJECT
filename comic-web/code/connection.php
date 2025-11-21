<?php
    $host = "mysql";
    $user = "root";
    $pass = "password";
    $db   = "db_komik";

    $conn = mysqli_connect($host, $user, $pass, $db);
        
    // Cek koneksi jika terjadi kegagalan
    if(!$conn){
        die("Koneksi gagal: " . mysqli_connect_error());
    }

?>