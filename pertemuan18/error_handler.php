<?php
// Custom exception
class ValidasiException extends Exception {}

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<b>Error:</b> [$errno] $errstr<br>";
    echo "Terjadi di file: $errfile, baris: $errline<br>";
    return true;
}
set_error_handler("customErrorHandler");

// Fungsi dengan try-catch
function bagi($a, $b) {
    if ($b == 0) {
        throw new ValidasiException("Pembagian dengan nol tidak diperbolehkan!");
    }
    return $a / $b;
}
?>