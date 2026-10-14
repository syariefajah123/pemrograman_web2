<?php

$conn = mysqli_connect("localhost", "root", "");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_select_db($conn, "lat_dbase");

$firstname = $_POST['firstname'];
$lastname  = $_POST['lastname'];
$age       = $_POST['age'];

$sql = "INSERT INTO tbl_mhs (FirstName, LastName, Age)
        VALUES (?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ssi", $firstname, $lastname, $age);

if (mysqli_stmt_execute($stmt)) {
    echo "1 record berhasil ditambahkan.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>