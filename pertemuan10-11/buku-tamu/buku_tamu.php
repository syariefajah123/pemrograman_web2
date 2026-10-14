<!DOCTYPE html>
<html>
<head>
    <title>Form Buku Tamu</title>
</head>

<body>

<h2>BUKU TAMU</h2>

<form action="simpan.php" method="post">

    <label>Nama</label><br>
    <input type="text" name="nama" required>

    <br><br>

    <label>Email</label><br>
    <input type="email" name="email" required>

    <br><br>

    <label>Pesan</label><br>
    <textarea name="pesan" rows="5" cols="40" required></textarea>

    <br><br>

    <input type="submit" value="Kirim">

</form>

<br>

<a href="tampil.php">Lihat Buku Tamu</a>

</body>
</html>