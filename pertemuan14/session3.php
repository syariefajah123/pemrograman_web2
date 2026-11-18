
<?php

// Memulai session
session_start();

// Memeriksa apakah session login tersedia
if (isset($_SESSION['login'])) {

    // Menghapus data session
    unset($_SESSION['login']);

    // Menghancurkan session
    session_destroy();

    // Menampilkan pesan logout berhasil
    echo "<h1>Anda sudah berhasil LOGOUT</h1>";

    echo "<h2>Klik <a href='session1.php'>di sini</a>
          untuk LOGIN kembali.</h2>";

    echo "<h2>Anda sekarang tidak bisa masuk ke halaman
          <a href='session2.php'>session2.php</a> lagi.</h2>";

} else {

    // Jika pengguna belum login
    echo "<h2>Anda belum login atau sudah logout.</h2>";

    echo "<h2>Klik <a href='session1.php'>di sini</a>
          untuk LOGIN.</h2>";

}

?>
