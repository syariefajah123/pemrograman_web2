
<?php
session_start();

// Memeriksa apakah tombol Login ditekan
if (isset($_POST['Login'])) {

    $user = $_POST['user'];
    $pass = $_POST['pass'];

    // Memeriksa username dan password
    if ($user == "rahadian" && $pass == "123") {

        // Membuat session login
        $_SESSION['login'] = $user;

        // Menampilkan pesan berhasil login
        echo "<h1>Anda berhasil LOGIN</h1>";
        echo "<h2>Klik <a href='session2.php'>di sini</a>
              untuk menuju halaman pemeriksaan session.</h2>";

    } else {

        // Jika username atau password salah
        echo "<h2>Username atau Password salah!</h2>";
    }

} else {
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Here</title>
</head>

<body>

    <h2>Login Here...</h2>

    <form action="" method="post">

        Username :
        <input type="text" name="user" required>
        <br><br>

        Password :
        <input type="password" name="pass" required>
        <br><br>

        <input type="submit" name="Login" value="Log In">

    </form>

</body>
</html>

<?php
}
?>
