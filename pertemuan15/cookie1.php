
<?php

// Menentukan nilai cookie
$value = 'rahadian';
$value2 = 'rahadi ramelan';

// Membuat cookie username
setcookie("username", $value);

// Membuat cookie nama lengkap dengan masa berlaku 1 jam
setcookie("namalengkap", $value2, time() + 3600);

// Menampilkan pesan
echo "<h1>Ini halaman pengesetan cookie</h1>";

echo "<h2>Klik <a href='cookie2.php'>di sini</a>
      untuk pemeriksaan cookies.</h2>";

?>
