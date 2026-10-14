<?php

$koneksi = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS buku_tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($koneksi, $sql)) {
    echo "Tabel buku_tamu berhasil dibuat.";
} else {
    echo "Gagal membuat tabel: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);

?>