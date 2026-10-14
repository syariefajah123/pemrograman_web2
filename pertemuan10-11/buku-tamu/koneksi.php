<?php

$koneksi = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>