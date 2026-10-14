<?php

include "koneksi.php";

/* Jumlah record setiap halaman */
$jumlah_data = 5;

/* Menentukan halaman */
if (isset($_GET['halaman'])) {
    $halaman = $_GET['halaman'];
} else {
    $halaman = 1;
}

/* Menentukan posisi data */
$awal = ($halaman - 1) * $jumlah_data;

/* Mengambil data */
$sql = "SELECT * FROM buku_tamu
        ORDER BY id DESC
        LIMIT $awal, $jumlah_data";

$query = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Buku Tamu</title>
</head>

<body>

<h2>DATA BUKU TAMU</h2>

<a href="buku_tamu.php">+ Isi Buku Tamu</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Pesan</th>
        <th>Tanggal</th>
    </tr>

<?php

$no = $awal + 1;

while ($data = mysqli_fetch_assoc($query)) {

?>

    <tr>

        <td>
            <?php echo $no; ?>
        </td>

        <td>
            <?php echo htmlspecialchars($data['nama']); ?>
        </td>

        <td>
            <?php echo htmlspecialchars($data['email']); ?>
        </td>

        <td>
            <?php echo htmlspecialchars($data['pesan']); ?>
        </td>

        <td>
            <?php echo $data['tanggal']; ?>
        </td>

    </tr>

<?php

    $no++;

}

?>

</table>

<br>

<?php

/* Menghitung seluruh data */
$sql_total = "SELECT COUNT(*) AS total FROM buku_tamu";

$query_total = mysqli_query($koneksi, $sql_total);

$data_total = mysqli_fetch_assoc($query_total);

$total_data = $data_total['total'];

/* Menghitung jumlah halaman */
$total_halaman = ceil($total_data / $jumlah_data);

echo "Halaman: ";

for ($i = 1; $i <= $total_halaman; $i++) {

    if ($i == $halaman) {

        echo "<b> $i </b>";

    } else {

        echo "<a href='tampil.php?halaman=$i'> $i </a>";

    }

}

?>

</body>
</html>

<?php

mysqli_close($koneksi);

?>