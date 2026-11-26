<?php

$nilai = 10;

if ($nilai > 0) {
    header("Location: berhasil.php");
    exit;
} else {
    echo "Nilai tidak valid";
}

?>