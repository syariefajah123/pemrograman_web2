<?php
// Koneksi sekaligus memilih database 'lat_dbase'
$conn = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menjalankan query select
$hasil = mysqli_query($conn, "SELECT * FROM tbl_mhs");

// Mengambil data baris per baris menggunakan mysqli_fetch_array
while ($data = mysqli_fetch_array($hasil)) {
    echo "$data[FirstName] $data[LastName] $data[Age]<br>";
}

// Menutup koneksi
mysqli_close($conn);
?>