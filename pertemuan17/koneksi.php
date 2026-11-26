<?php
// Sesuaikan konfigurasi ini dengan MySQL di komputer masing-masing.
$host = "localhost";
$username = "root";
$password = ""; // XAMPP umumnya menggunakan password kosong secara default.
$database = "db_error_handling";

mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = new mysqli($host, $username, $password);

if ($koneksi->connect_error) {
    // Pesan aman untuk pengunjung. Detail teknis dapat diperiksa di log server.
    error_log("Koneksi MySQL gagal: " . $koneksi->connect_error);
    $pesan = "Koneksi database gagal. Periksa konfigurasi host, username, dan password.";
} else {
    $pesan = "Koneksi ke server MySQL berhasil.";

    // Membuat database contoh jika belum tersedia.
    $sql = "CREATE DATABASE IF NOT EXISTS `$database`";
    if (!$koneksi->query($sql)) {
        $pesan = "Koneksi berhasil, tetapi database tidak dapat dibuat.";
        error_log("Gagal membuat database: " . $koneksi->error);
    } else {
        $koneksi->select_db($database);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Contoh Koneksi MySQL</title>
</head>
<body>
    <h1>Contoh Koneksi MySQL</h1>
    <p><?php echo htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Host: <?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Username: <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="index.php">Kembali</a>
</body>
</html>
<?php
if (isset($koneksi) && $koneksi instanceof mysqli) {
    $koneksi->close();
}
?>
