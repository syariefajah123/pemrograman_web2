```php
<!DOCTYPE html>
<html>
<head>
    <title>Contoh Penggunaan UDF</title>
</head>
<body>

<!-- Menentukan Form Input -->
<form method="POST">
    Masukkan Bilangan Pertama : <br>
    <input type="text" name="A" size="10"><br><br>

    Masukkan Bilangan Kedua : <br>
    <input type="text" name="B" size="10"><br><br>

    <input type="submit" name="hitung" value="Hitung">
</form>

<?php

// Mengecek apakah tombol Hitung sudah ditekan
if (isset($_POST['hitung'])) {

    // Mengambil nilai dari form
    $A = $_POST['A'];
    $B = $_POST['B'];

    // UDF untuk penjumlahan
    function jumlah($A, $B)
    {
        $jumlahbil = $A + $B;
        return $jumlahbil;
    }

    // UDF untuk pengurangan
    function kurang($A, $B)
    {
        $kurangbil = $A - $B;
        return $kurangbil;
    }

    // UDF untuk perkalian
    function kali($A, $B)
    {
        $kalibil = $A * $B;
        return $kalibil;
    }

    // UDF untuk pembagian
    function bagi($A, $B)
    {
        $bagibil = $A / $B;
        return $bagibil;
    }

    echo "<br>";

    echo "Bilangan Pertama : " . $A;
    echo "<br>";

    echo "Bilangan Kedua : " . $B;
    echo "<br><br>";

    // Penjumlahan
    echo "Hasil Penjumlahan 2 buah bilangan";
    echo "<br>";

    $jumlahbil = jumlah($A, $B);

    printf(
        "Penjumlahan antara : %d + %d = %d",
        $A,
        $B,
        $jumlahbil
    );

    echo "<br><br>";

    // Pengurangan
    echo "Hasil Pengurangan 2 buah bilangan";
    echo "<br>";

    $kurangbil = kurang($A, $B);

    printf(
        "Pengurangan antara : %d - %d = %d",
        $A,
        $B,
        $kurangbil
    );

    echo "<br><br>";

    // Perkalian
    echo "Hasil Perkalian 2 buah bilangan";
    echo "<br>";

    $kalibil = kali($A, $B);

    printf(
        "Perkalian antara : %d * %d = %d",
        $A,
        $B,
        $kalibil
    );

    echo "<br><br>";

    // Pembagian
    echo "Hasil Pembagian 2 buah bilangan";
    echo "<br>";

    if ($B != 0) {
        $bagibil = bagi($A, $B);

        printf(
            "Pembagian antara : %d / %d = %.2f",
            $A,
            $B,
            $bagibil
        );
    } else {
        echo "Pembagian dengan angka 0 tidak diperbolehkan.";
    }

    echo "<br><br>";
}

?>

</body>
</html>
```
