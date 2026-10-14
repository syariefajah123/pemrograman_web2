<?php
// Koneksi ke database langsung dengan memilih databasenya
$con = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}

// Input data pertama (Karina)
$sql1 = "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('Karina', 'Suwandi', '29')";
mysqli_query($con, $sql1);

// Input data kedua (Glenn)
$sql2 = "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('Glenn', 'Gandari', '32')";
mysqli_query($con, $sql2);

echo "Semua data berhasil dimasukkan ke tabel!";

// Menutup koneksi
mysqli_close($con);
?>