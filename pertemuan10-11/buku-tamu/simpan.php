<?php
include "koneksi.php";

$nama = $_POST['nama'];
$email = $_POST['email'];
$pesan = $_POST['pesan'];

$sql = "INSERT INTO buku_tamu (nama, email, pesan) VALUES (?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $pesan);

if (mysqli_stmt_execute($stmt)) {
    echo "Data berhasil disimpan.<br><br>";
    echo "<a href='buku_tamu.php'>Kembali ke Form</a><br>";
    echo "<a href='tampil.php'>Lihat Buku Tamu</a>";
} else {
    echo "Data gagal disimpan: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>