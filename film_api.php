<?php
require_once 'config.php';
$config = include 'config.php';

class FilmAPI {
    private $omdbApiKey;
    
    public function __construct() {
        $config = include 'config.php';
        $this->omdbApiKey = $config['omdb_api_key'];
    }
    
    public function filmAra($filmAdi) {
        $filmAdi = urlencode($filmAdi);
        $url = "http://www.omdbapi.com/?t={$filmAdi}&apikey={$this->omdbApiKey}";
        
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data['Response'] == 'True') {
            return [
                'baslik' => $data['Title'],
                'yil' => $data['Year'],
                'poster' => $data['Poster'],
                'imdb_puani' => $data['imdbRating'],
                'tur' => $data['Genre'],
                'yonetmen' => $data['Director'],
                'oyuncular' => $data['Actors'],
                'konu' => $data['Plot']
            ];
        } else {
            return false;
        }
    }
    
    public function filmListesiAra($filmAdi) {
        $filmAdi = urlencode($filmAdi);
        $url = "http://www.omdbapi.com/?s={$filmAdi}&apikey={$this->omdbApiKey}";
        
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data['Response'] == 'True') {
            return $data['Search'];
        } else {
            return false;
        }
    }
    
    public function filmDetayGetir($imdbID) {
        $url = "http://www.omdbapi.com/?i={$imdbID}&apikey={$this->omdbApiKey}";
        
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
}

// AJAX isteklerini yönetme
header('Content-Type: application/json');

try {
    $api = new FilmAPI();
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'search':
                if (!isset($_GET['q'])) {
                    throw new Exception('Arama sorgusu belirtilmedi');
                }
                $sonuc = $api->filmListesiAra($_GET['q']);
                echo json_encode($sonuc ?: ['error' => 'Film bulunamadı']);
                break;
                
            case 'detail':
                if (!isset($_GET['id'])) {
                    throw new Exception('Film ID belirtilmedi');
                }
                $sonuc = $api->filmDetayGetir($_GET['id']);
                echo json_encode($sonuc ?: ['error' => 'Film detayları alınamadı']);
                break;
                
            default:
                throw new Exception('Geçersiz işlem');
        }
    } elseif (isset($_POST['filmAra'])) {
        // Eski POST yöntemiyle uyumluluk
        $sonuc = $api->filmAra($_POST['filmAdiAra']);
        echo json_encode($sonuc ?: ['error' => 'Film bulunamadı']);
    } else {
        throw new Exception('Geçersiz istek');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>