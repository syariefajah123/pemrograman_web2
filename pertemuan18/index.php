<?php
require_once 'error_handler.php';

echo "<h2>Praktik Error Handling - Pertemuan 18</h2>";

// Contoh 1: Try-Catch
echo "<h3>1. Try-Catch</h3>";
try {
    echo "Hasil: " . bagi(10, 2) . "<br>";
    echo "Hasil: " . bagi(10, 0) . "<br>";
} catch (ValidasiException $e) {
    echo "Caught Exception: " . $e->getMessage() . "<br>";
} finally {
    echo "Blok finally selalu dijalankan.<br>";
}

// Contoh 2: Pengecekan kondisi input
echo "<h3>2. Validasi Input</h3>";
$umur = -5;
if ($umur < 0) {
    echo "Error: Umur tidak boleh negatif!<br>";
} elseif ($umur < 17) {
    echo "Error: Umur minimal 17 tahun.<br>";
} else {
    echo "Umur valid: $umur<br>";
}

// Contoh 3: Custom error handler
echo "<h3>3. Custom Error Handler</h3>";
echo $variabelTidakAda; // memicu warning

// Contoh 4: Try-Catch-Finally dengan file
echo "<h3>4. File Handling</h3>";
try {
    $file = fopen("tidak_ada.txt", "r");
    if (!$file) {
        throw new Exception("File gagal dibuka.");
    }
} catch (Exception $e) {
    echo "Error file: " . $e->getMessage() . "<br>";
} finally {
    if (isset($file) && $file) fclose($file);
    echo "Proses file selesai.<br>";
}
?>