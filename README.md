# Film Takip Uygulaması

Bu uygulama, kullanıcıların izledikleri filmleri takip etmelerini sağlayan bir web uygulamasıdır.


## 📹 Tanıtım Videosu

[![Video Başlık](https://img.youtube.com/vi/Gxg5hdPOFXQ/1.jpg)](https://www.youtube.com/watch?v=Gxg5hdPOFXQ)


## Özellikler

- Kullanıcı kayıt ve giriş sistemi
- OMDB API ile film arama
- Film bilgilerini otomatik doldurma
- Kişisel film listesi
- Film değerlendirme ve yorum sistemi

## Kurulum

### 1. Projeyi İndirin
```bash
git clone https://github.com/kullanici-adi/film-takip-uygulamasi.git
cd film-takip-uygulamasi
```

### 2. Environment Dosyasını Ayarlayın
```bash
cp .env.example .env
```

`.env` dosyasını düzenleyin ve gerekli bilgileri girin:
- `OMDB_API_KEY`: [OMDB API](http://www.omdbapi.com/) ücretsiz API anahtarınız
- Veritabanı bağlantı bilgileri

### 3. Veritabanını Oluşturun

MySQL veritabanında aşağıdaki tabloları oluşturun:

```sql
-- Kullanıcılar tablosu
CREATE TABLE kullanicilar (
    kullaniciID INT PRIMARY KEY AUTO_INCREMENT,
    kullaniciAd VARCHAR(100) NOT NULL,
    kullaniciEmail VARCHAR(100) UNIQUE NOT NULL,
    kullaniciSifre VARCHAR(255) NOT NULL
);

-- Filmler tablosu
CREATE TABLE tblfilmler (
    filmID INT PRIMARY KEY AUTO_INCREMENT,
    kullaniciID INT,
    filmAd VARCHAR(255) NOT NULL,
    filmYil VARCHAR(4),
    filmPuan DECIMAL(3,1),
    filmDusunceler TEXT,
    filmGorselURL TEXT,
    filmTur VARCHAR(255),
    filmYonetmen VARCHAR(255),
    filmKonu TEXT,
    imdbPuani VARCHAR(10),
    izlemeYili INT,
    eklenmeTarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullaniciID) REFERENCES kullanicilar(kullaniciID)
);
```

### 4. Web Sunucusunu Başlatın

XAMPP, WAMP veya benzeri bir web sunucusu kullanın veya PHP'nin yerleşik sunucusunu çalıştırın:

```bash
php -S localhost:8000
```

## Kullanım

1. `kayit.php` sayfasından hesap oluşturun
2. `giris.php` sayfasından giriş yapın
3. `filmekle.php` sayfasından film arayın ve ekleyin
4. `filmlerim.php` sayfasından film listenizi görüntüleyin

## API Anahtarı

Bu uygulama OMDB API kullanmaktadır. Ücretsiz API anahtarını [buradan](http://www.omdbapi.com/) alabilirsiniz.

## Güvenlik

- API anahtarları `.env` dosyasında saklanır
- Şifreler `password_hash()` fonksiyonu ile hashlenir
- SQL injection koruması için PDO prepared statements kullanılır
