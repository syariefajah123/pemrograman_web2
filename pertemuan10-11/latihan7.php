<?php
// Koneksi sekaligus memilih database 'lat_dbase'
$conn = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menjalankan query select untuk mengambil data dari tabel tbl_mhs
$hasil = mysqli_query($conn, "SELECT * FROM tbl_mhs");

// Menghitung jumlah record / baris data
$hit = mysqli_num_rows($hasil);

// Menampilkan hasil
echo "Jumlah record: $hit";

// Menutup koneksi
mysqli_close($conn);
?>