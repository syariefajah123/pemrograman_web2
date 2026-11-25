<?php

$koneksi = new mysqli(
    "localhost",
    "root",
    "",
    "db_mahasiswa"
);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

echo "Koneksi database berhasil!";

?>