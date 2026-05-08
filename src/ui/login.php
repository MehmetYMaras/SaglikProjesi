<?php
/**
 * GİRİŞ SİSTEMİ (LOGIN)
 * Kullanıcı listesini JSON'dan dinamik olarak çekip giriş ekranına yansıtıyoruz.
 */
require_once __DIR__ . '/../core/app.php';
App::run();

$jsonVerisi = file_get_contents(__DIR__ . '/../../data/data/kullanicilar.json');
$kullanicilar = json_decode($jsonVerisi, true);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Health App - Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* GENEL TASARIM: Apple'ın karanlık mod estetiği */
        body { background-color: #000; color: #fff; font-family: -apple-system, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        
        /* GİRİŞ KARTI: Derinlik hissi ve yuvarlatılmış köşeler */
        .login-card { background: #1c1c1e; border-radius: 25px; padding: 40px; width: 100%; max-width: 400px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        
        /* LOGO ALANI: Resmi merkeze odaklayan konteyner */
        .logo-container { 
            margin: 0 auto 25px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }

        /* KULLANICI BUTONLARI: Modern iOS tarzı liste elemanları */
        .user-btn { background: #2c2c2e; color: white; border: none; border-radius: 12px; padding: 15px; margin-bottom: 10px; width: 100%; text-align: left; transition: 0.3s; text-decoration: none; display: block; }
        .user-btn:hover { background: #3a3a3c; transform: scale(1.02); }
    </style>
</head>
<body>
    <div class="login-card">
        
        <!-- LOGO BÖLÜMÜ: Assets klasöründeki profesyonel gradient ikon kullanıldı -->
        <div class="logo-container">
         <img src="../../assets/icons/logo.png" 
         alt="Health Logo" 
         style="width: 85px; height: auto; object-fit: contain;">
            </div>

        <h2 class="fw-bold mb-1">Hoş Geldiniz</h2>
        <p class="text-secondary small mb-4">Lütfen devam etmek için profilinizi seçin</p>
        
        <!-- KULLANICI LİSTESİ: JSON verisinden dinamik olarak üretilir -->
        <div class="user-list">
            <?php foreach($kullanicilar as $k): ?>
                <a href="index.php?u=<?= $k['id'] ?>" class="user-btn">
                    <span class="fw-bold"><?= $k['isim'] ?></span>
                    <span class="float-end text-secondary">→</span>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>