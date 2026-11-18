
<?php

// Memulai session
session_start();

// Memeriksa apakah session login tersedia
if (isset($_SESSION['login'])) {

    // Jika sudah login, tampilkan pesan selamat datang
    echo "<h1>Selamat Datang " . $_SESSION['login'] . "</h1>";

    echo "<h2>Halaman ini hanya bisa diakses jika Anda sudah login.</h2>";

    // Link untuk logout
    echo "<h2>Klik <a href='session3.php'>di sini</a>
          untuk LOGOUT.</h2>";

} else {

    // Jika belum login, tampilkan pesan
    die("Anda belum login! Anda tidak berhak masuk ke halaman ini.
         Silakan login <a href='session1.php'>di sini</a>.");

}

?>
