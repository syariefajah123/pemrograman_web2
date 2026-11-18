
<?php

// Memeriksa apakah cookie username tersedia
if (isset($_COOKIE['username'])) {

    // Menampilkan isi cookie username
    echo "<h1>Cookie 'username' ada. Isinya: "
         . $_COOKIE['username'] . "</h1>";

} else {

    // Jika cookie username tidak tersedia
    echo "<h1>Cookie 'username' TIDAK ada.</h1>";

}

// Memeriksa apakah cookie namalengkap tersedia
if (isset($_COOKIE['namalengkap'])) {

    // Menampilkan isi cookie namalengkap
    echo "<h1>Cookie 'namalengkap' ada. Isinya: "
         . $_COOKIE['namalengkap'] . "</h1>";

} else {

    // Jika cookie namalengkap tidak tersedia
    echo "<h1>Cookie 'namalengkap' TIDAK ada.</h1>";

}

// Link untuk membuat cookie kembali
echo "<h2>Klik <a href='cookie1.php'>di sini</a>
      untuk penciptaan cookies.</h2>";

// Link untuk menghapus cookie
echo "<h2>Klik <a href='cookie3.php'>di sini</a>
      untuk penghapusan cookies.</h2>";

?>
