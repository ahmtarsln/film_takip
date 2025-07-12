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

    if (isset($_SESSION['kullanici'])) {
        $kullaniciAd = $_SESSION["kullanici"]["ad"];
        $kullaniciID = $_SESSION["kullanici"]["id"];
        echo "<span class='top-echo'>Hoşgeldin $kullaniciAd<br>ID: $kullaniciID</span><br>";
    } else {
        header("Location: giris.php");
        exit;
    }

    // Çıkış işlemi
    if (isset($_POST['btnCikis'])) {
        session_destroy();
        header("Location: giris.php");
        exit;
    }

    // Film silme işlemi
    if (isset($_POST['btnSil']) && isset($_POST['filmID'])) {
        $filmID = $_POST['filmID'];
        $silmeSorgusu = $conn->prepare("DELETE FROM tblfilmler WHERE kullaniciID = ? AND filmID = ?");
        $silmeSorgusu->bindParam(1, $kullaniciID, PDO::PARAM_INT);
        $silmeSorgusu->bindParam(2, $filmID, PDO::PARAM_INT);
        $silmeSorgusu->execute();
        echo "<p class='success-message'>Film başarılı bir şekilde silindi.</p>";
    }

    // Kullanıcının filmlerini getir
    $sorgu = $conn->prepare("SELECT * FROM tblfilmler WHERE kullaniciID = ? ORDER BY eklenmeTarihi DESC");
    $sorgu->bindParam(1, $kullaniciID, PDO::PARAM_INT);
    $sorgu->execute();

    $eklenenFilmler = array();
    while ($film = $sorgu->fetch()) {
        $eklenenFilmler[] = $film;
    }

} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmler</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .top-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
        }
        .nav-left {
            display: flex;
            gap: 15px;
        }
        .nav-btn {
            background: #FF7043;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        .nav-btn:hover {
            background: #FF5722;
            transform: translateY(-2px);
        }
        .cikis-btn {
            background: #dc3545;
        }
        .cikis-btn:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="top-navigation">
            <div class="nav-left">
                <a href="filmekle.php" class="nav-btn">＋ Film Ekle</a>
            </div>
            <div class="nav-right">
                <form method="post" style="margin: 0; padding: 0; background: none; box-shadow: none; max-width: none;">
                    <button type="submit" name="btnCikis" class="nav-btn cikis-btn">🚪 Çıkış Yap</button>
                </form>
            </div>
        </div>

        <h2>İzlediğim Filmler</h2>

        <?php if (count($eklenenFilmler) > 0): ?>
            <div class="film-sayaci">
                Toplam <?php echo count($eklenenFilmler); ?> film izlemişsin!
            </div>

            <div class="filmler-listesi">
                <?php foreach ($eklenenFilmler as $film): ?>
                    <div class="film-item">
                        <div class="film-poster-container">
                            <img src="<?php echo !empty($film['filmGorselURL']) ? $film['filmGorselURL'] : 'https://via.placeholder.com/150x220?text=No+Image'; ?>"
                                alt="<?php echo htmlspecialchars($film['filmAd']); ?>" class="film-poster">
                        </div>

                        <div class="film-detaylar">
                            <div class="film-baslik">
                                <?php echo htmlspecialchars($film['filmAd']); ?> (<?php echo $film['filmYil']; ?>)
                            </div>

                            <div class="film-bilgi-grid">
                                <div class="film-bilgi">
                                    <strong>Tür:</strong> <?php echo htmlspecialchars($film['filmTur']); ?>
                                </div>

                                <div class="film-bilgi">
                                    <strong>Yönetmen:</strong> <?php echo htmlspecialchars($film['filmYonetmen']); ?>
                                </div>

                                <div class="film-bilgi">
                                    <strong>IMDB Puanı:</strong> <?php echo $film['imdbPuani']; ?>/10
                                </div>

                                <div class="film-bilgi">
                                    <strong>İzlediğin Yıl:</strong> <?php echo $film['izlemeYili']; ?>
                                </div>
                            </div>

                            <div class="film-konu">
                                <strong>Konu:</strong> <?php echo htmlspecialchars($film['filmKonu']); ?>
                            </div>

                            <div class="kullanici-degerlendirme">
                                <div class="puan-container">
                                    <strong>Senin Puanın:</strong> 
                                    <span class="puan-badge"><?php echo $film['filmPuan']; ?>/10</span>
                                </div>
                                <div class="dusunceler">
                                    <strong>Düşüncelerin:</strong> 
                                    <p><?php echo htmlspecialchars($film['filmDusunceler']); ?></p>
                                </div>
                            </div>

                            <div class="film-footer">
                                <div class="ekleme-tarihi">
                                    <strong>Eklenme Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($film['eklenmeTarihi'])); ?>
                                </div>
                                
                                <form action="" method="post" class="sil-form">
                                    <input type="hidden" name="filmID" value="<?php echo $film['filmID']; ?>">
                                    <button type="submit" name="btnSil" class="sil-btn"
                                        onclick="return confirm('Bu filmi silmek istediğinizden emin misiniz?')">
                                        🗑️ Sil
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="bos-liste">
                <h3>Henüz hiç film eklememişsin!</h3>
                <p><a href="filmekle.php">İlk filmini eklemek için tıkla</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>