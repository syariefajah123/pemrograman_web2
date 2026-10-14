<?php

$koneksi = mysqli_connect("localhost", "root", "");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS lat_dbase";

if (mysqli_query($koneksi, $sql)) {
    echo "Database lat_dbase berhasil dibuat.";
} else {
    echo "Gagal membuat database: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);

?>