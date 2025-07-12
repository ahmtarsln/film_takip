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

    // Film ekleme işlemi
    if (isset($_POST['btnFilmEkle'])) {
        $filmBaslik = $_POST['filmBaslik'];
        $filmYili = $_POST['filmYili'];
        $filmPoster = $_POST['filmPoster'];
        $filmTur = $_POST['filmTur'];
        $filmYonetmen = $_POST['filmYonetmen'];
        $filmKonu = $_POST['filmKonu'];
        $imdbPuani = $_POST['imdbPuani'];
        $izlemeYili = $_POST['izlemeYili'];
        $kullaniciPuani = $_POST['kullaniciPuani'];
        $kullaniciDusunceleri = $_POST['kullaniciDusunceleri'];
        $eklenmeTarihi = date('Y-m-d H:i:s');

        $sorgu = $conn->prepare("INSERT INTO tblfilmler (kullaniciID, filmAd, filmYil, filmPuan, filmDusunceler, filmGorselURL, filmTur, filmYonetmen, filmKonu, imdbPuani, izlemeYili, eklenmeTarihi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $sorgu->bindParam(1, $kullaniciID, PDO::PARAM_INT);
        $sorgu->bindParam(2, $filmBaslik, PDO::PARAM_STR);
        $sorgu->bindParam(3, $filmYili, PDO::PARAM_STR);
        $sorgu->bindParam(4, $kullaniciPuani, PDO::PARAM_STR);
        $sorgu->bindParam(5, $kullaniciDusunceleri, PDO::PARAM_STR);
        $sorgu->bindParam(6, $filmPoster, PDO::PARAM_STR);
        $sorgu->bindParam(7, $filmTur, PDO::PARAM_STR);
        $sorgu->bindParam(8, $filmYonetmen, PDO::PARAM_STR);
        $sorgu->bindParam(9, $filmKonu, PDO::PARAM_STR);
        $sorgu->bindParam(10, $imdbPuani, PDO::PARAM_STR);
        $sorgu->bindParam(11, $izlemeYili, PDO::PARAM_INT);
        $sorgu->bindParam(12, $eklenmeTarihi, PDO::PARAM_STR);

        $sorgu->execute();

        echo "<p style='color: green; text-align: center;'>Film başarıyla eklendi!</p>";
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
    <title>Film Ekle</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .film-arama {
            background: #2c2c2c;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .film-sonuclar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .film-card {
            background: lightsalmon;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: black;
        }

        .film-card:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .film-card img {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .secilen-film {
            background: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .loading {
            text-align: center;
            color: lightsalmon;
        }

        #filmForm {
            background: #2c2c2c;
            padding: 20px;
            border-radius: 10px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

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
                <a href="filmlerim.php" class="nav-btn">📽️ İzlediğim Filmler</a>
            </div>
            <div class="nav-right">
                <form method="post" style="margin: 0; padding: 0; background: none; box-shadow: none; max-width: none;">
                    <button type="submit" name="btnCikis" class="nav-btn cikis-btn">🚪 Çıkış Yap</button>
                </form>
            </div>
        </div>

        <h2><u>Film Ekle</u></h2>

        <!-- Film Arama Bölümü -->
        <div class="film-arama">
            <h3>Film Ara</h3>
            <input type="text" id="filmAramaInput" placeholder="Film adını yazın..." style="width: 70%; padding: 10px;">
            <button onclick="filmAra()" style="padding: 10px 20px;">🔍 Ara</button>
            <div id="loadingDiv" class="loading" style="display: none;">Aranıyor...</div>
            <div id="filmSonuclar" class="film-sonuclar"></div>
        </div>

        <!-- Seçilen Film Bilgisi -->
        <div id="secilenFilm" class="secilen-film" style="display: none;">
            <h3>Seçilen Film:</h3>
            <div id="secilenFilmBilgi"></div>
        </div>

        <!-- Film Ekleme Formu -->
        <form id="filmForm" action="" method="post">
            <input type="hidden" id="filmBaslik" name="filmBaslik">
            <input type="hidden" id="filmYili" name="filmYili">
            <input type="hidden" id="filmPoster" name="filmPoster">
            <input type="hidden" id="filmTur" name="filmTur">
            <input type="hidden" id="filmYonetmen" name="filmYonetmen">
            <input type="hidden" id="filmKonu" name="filmKonu">
            <input type="hidden" id="imdbPuani" name="imdbPuani">

            <p>İzlediğin Yıl: <input type="number" name="izlemeYili" min="1900" max="2024" required><br><br></p>
            <p>Puanın (1-10): <input type="number" name="kullaniciPuani" min="1" max="10" step="0.1" required><br><br>
            </p>
            <p>Düşüncelerin: <textarea name="kullaniciDusunceleri" rows="4" cols="50" required></textarea><br><br></p>
            <p><input type="submit" value="Film Ekle" name="btnFilmEkle"
                    style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;"><br><br>
            </p>
        </form>
    </div>

    <script>
        let secilenFilmData = null;

        async function filmAra() {
            const filmAdi = document.getElementById('filmAramaInput').value;
            if (!filmAdi.trim()) {
                alert('Lütfen film adı girin!');
                return;
            }

            document.getElementById('loadingDiv').style.display = 'block';
            document.getElementById('filmSonuclar').innerHTML = '';

            try {
                const response = await fetch(`film_api.php?action=search&q=${encodeURIComponent(filmAdi)}`);
                const data = await response.json();

                document.getElementById('loadingDiv').style.display = 'none';

                if (data.error) {
                    document.getElementById('filmSonuclar').innerHTML = `<p style="color: red;">${data.error}</p>`;
                } else if (data.length > 0) {
                    displayFilmSonuclari(data);
                } else {
                    document.getElementById('filmSonuclar').innerHTML = '<p style="color: red;">Film bulunamadı!</p>';
                }
            } catch (error) {
                document.getElementById('loadingDiv').style.display = 'none';
                document.getElementById('filmSonuclar').innerHTML = '<p style="color: red;">Hata oluştu!</p>';
            }
        }

        function displayFilmSonuclari(filmler) {
            const sonuclarDiv = document.getElementById('filmSonuclar');
            sonuclarDiv.innerHTML = '';

            filmler.forEach(film => {
                const filmCard = document.createElement('div');
                filmCard.className = 'film-card';
                filmCard.innerHTML = `
                    <img src="${film.Poster !== 'N/A' ? film.Poster : 'https://via.placeholder.com/100x150?text=No+Image'}" alt="${film.Title}">
                    <h4>${film.Title}</h4>
                    <p>${film.Year}</p>
                `;
                filmCard.onclick = () => filmSec(film.imdbID);
                sonuclarDiv.appendChild(filmCard);
            });
        }

        async function filmSec(imdbID) {
            try {
                const response = await fetch(`film_api.php?action=detail&id=${imdbID}`);
                const filmData = await response.json();

                if (filmData.error) {
                    alert(filmData.error);
                    return;
                }

                secilenFilmData = filmData;
                displaySecilenFilm(filmData);
                formDoldur(filmData);
            } catch (error) {
                alert('Film detayları alınamadı!');
            }
        }

        function displaySecilenFilm(film) {
            document.getElementById('secilenFilm').style.display = 'block';
            document.getElementById('secilenFilmBilgi').innerHTML = `
                <div style="display: flex; align-items: center; gap: 20px;">
                    <img src="${film.Poster !== 'N/A' ? film.Poster : 'https://via.placeholder.com/100x150?text=No+Image'}" 
                         alt="${film.Title}" style="width: 100px; height: 150px;">
                    <div>
                        <h4>${film.Title} (${film.Year})</h4>
                        <p><strong>Tür:</strong> ${film.Genre}</p>
                        <p><strong>Yönetmen:</strong> ${film.Director}</p>
                        <p><strong>IMDB Puanı:</strong> ${film.imdbRating}/10</p>
                        <p><strong>Konu:</strong> ${film.Plot}</p>
                    </div>
                </div>
            `;
        }

        function formDoldur(film) {
            document.getElementById('filmBaslik').value = film.Title;
            document.getElementById('filmYili').value = film.Year;
            document.getElementById('filmPoster').value = film.Poster !== 'N/A' ? film.Poster : '';
            document.getElementById('filmTur').value = film.Genre;
            document.getElementById('filmYonetmen').value = film.Director;
            document.getElementById('filmKonu').value = film.Plot;
            document.getElementById('imdbPuani').value = film.imdbRating;
        }

        // Enter tuşu ile arama
        document.getElementById('filmAramaInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                filmAra();
            }
        });
    </script>
</body>

</html>