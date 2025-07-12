<?php
session_start();
require_once 'config.php';
$config = include 'config.php';

try {
    $conn = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']}", 
        $config['db']['user'], 
        $config['db']['pass']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST["btnGiris"])) {
        $k_Email = $_POST["girisEmail"];
        $k_Sifre = $_POST["girisKullaniciSifre"];

        $sorgu = $conn->prepare("SELECT * FROM kullanicilar WHERE kullaniciEmail = ?");
        $sorgu->execute([$k_Email]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($kullanici && password_verify($k_Sifre, $kullanici["kullaniciSifre"])) {
            $_SESSION["kullanici"] = [
                "ad" => $kullanici["kullaniciAd"],
                "id" => $kullanici["kullaniciID"]
            ];
            header("Location: filmlerim.php");
            exit;
        } else {
            $hata = "E-posta veya şifre hatalı!";
        }
    }
} catch (PDOException $e) {
    $hata = "Veritabanı bağlantı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form method="post">
    <h2>Giriş Yap</h2>
    <p>E-Mail:</p>
    <input type="email" name="girisEmail" required>
    <p>Şifre:</p>
    <input type="password" name="girisKullaniciSifre" required><br><br>
    <input type="submit" value="Giriş Yap" name="btnGiris">
    <p><a href="kayit.php">Hesabınız yok mu? Kayıt olun</a></p>
    <?php if (isset($hata)) echo "<p style='color:red;'>$hata</p>"; ?>
</form>
</body>
</html>
