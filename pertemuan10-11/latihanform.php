<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menangkap data yang dikirim dari form HTML
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$age = $_POST['age'];

// Memasukkan data ke tabel tbl_mhs
$sql = "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('$firstname', '$lastname', '$age')";

if (mysqli_query($conn, $sql)) {
    echo "Data berhasil disimpan ke database!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>