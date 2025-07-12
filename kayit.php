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

    if (isset($_POST["btnKayit"])) {
        $kullaniciAd = $_POST["kayitKullaniciAd"];
        $kullaniciSifre = $_POST["kayitKullaniciSifre"];
        $kullaniciEmail = $_POST["kayitEmail"];

        // Şifreyi güvenli şekilde hashle
        $hashedSifre = password_hash($kullaniciSifre, PASSWORD_DEFAULT);

        $sorgu = $conn->prepare("INSERT INTO kullanicilar(kullaniciAd, kullaniciSifre, kullaniciEmail) VALUES(?, ?, ?)");
        $sorgu->execute([$kullaniciAd, $hashedSifre, $kullaniciEmail]);

        $mesaj = "Kayıt başarılı! Giriş yapmak için <a href='giris.php'>buraya tıklayın</a>.";
    }
} catch (PDOException $e) {
    $mesaj = "Hata: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form method="post">
    <h2>Kayıt Ol</h2>
    <p>Kullanıcı Adı:</p>
    <input type="text" name="kayitKullaniciAd" required>
    <p>E-Mail:</p>
    <input type="email" name="kayitEmail" required>
    <p>Şifre:</p>
    <input type="password" name="kayitKullaniciSifre" required><br><br>
    <input type="submit" value="Kayıt Ol" name="btnKayit">
    <p><a href="giris.php">Zaten hesabın var mı? Giriş yap</a></p>
    <?php if (isset($mesaj)) echo "<p style='color:green;'>$mesaj</p>"; ?>
</form>
</body>
</html>
