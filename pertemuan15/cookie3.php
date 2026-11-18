
<?php

// Mengatur masa berlaku cookie menjadi satu jam yang lalu
setcookie("username", "", time() - 3600);
setcookie("namalengkap", "", time() - 3600);

// Menampilkan pesan berhasil
echo "<h1>Cookie Berhasil Dihapus.</h1>";

// Link untuk membuat cookie kembali
echo "<h2>Klik <a href='cookie1.php'>di sini</a>
      untuk penciptaan cookies.</h2>";

// Link untuk memeriksa cookie
echo "<h2>Klik <a href='cookie2.php'>di sini</a>
      untuk pemeriksaan cookies.</h2>";

?>
