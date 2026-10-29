<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "lat_dbase";

$connection = mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$connection) {
    die('Could not connect: ' . mysqli_connect_error());
}

$pilih_db = mysqli_select_db($connection, $dbname);
if (!$pilih_db) {
    echo "Tidak dapat memilih database";
    exit;
}

$query = "UPDATE tbl_mhs SET Age = '36' WHERE FirstName = 'Karina' AND LastName = 'Suwandi'";
if (mysqli_query($connection, $query)) {
    echo "Data berhasil di-update!";
} else {
    echo "Error updating record: " . mysqli_error($connection);
}

mysqli_close($connection);
?>