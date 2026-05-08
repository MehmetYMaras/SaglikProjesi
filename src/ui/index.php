<?php
// GÜVENLİK VE STATE KONTROLÜ: Eğer URL'de kullanıcı belirtilmemişse direkt giriş sayfasına gönderilir.
if (!isset($_GET['u'])) {
    header("Location: login.php");
    exit();
}

/**
 * YAZILIM MİMARİSİ VE MODÜLERLİK (MODULARITY)
 * Proje, hocanın istediği 'Modülerlik' kuralı gereği katmanlı mimari kullanılmıştır.
 */
require_once __DIR__ . '/../core/app.php';
App::run();
require_once '../modules/SaglikVerisi.php'; 
require_once '../modules/Tansiyon.php';
require_once '../services/StorageService.php';

// STORAGE SERVICE: Veritabanı işlemlerini yürüten servis nesnesi.
$storage = new StorageService();

/**
 * VERİ YÖNETİMİ VE JSON PERSISTENCE
 * Kullanıcı listesi ve fiziksel detaylar harici JSON dosyalarından çekilir.
 */
$jsonVerisi = file_get_contents('../../data/data/kullanicilar.json');
$kullanicilar = json_decode($jsonVerisi, true);

// Fiziksel detayları çekiyoruz
$detaylarJson = json_decode(file_get_contents('../../data/data/kullanici_detaylari.json'), true);

// STATE MANAGEMENT: URL üzerinden aktif kullanıcı kimliği ve isim eşleştirmesi.
$aktifId = $_GET['u'];
$aktifIsim = "Bilinmiyor";
$aktifBilgiler = []; // Boy, kilo, yaş verilerini tutacak

// 1. İsim eşleştirmesi
foreach($kullanicilar as $k) {
    if($k['id'] == $aktifId) {
        $aktifIsim = $k['isim'];
        break;
    }
}

// 2. Fiziksel detay eşleştirmesi (Senin yazdığın kısım)
foreach($detaylarJson as $d) {
    if($d['id'] == $aktifId) {
        $aktifBilgiler = $d;
        break;
    }
}

// VKİ (Vücut Kitle İndeksi) Hesaplama - Teknik Cila
$vki = 0;
if (!empty($aktifBilgiler['boy']) && !empty($aktifBilgiler['kilo'])) {
    $boyMetre = $aktifBilgiler['boy'] / 100;
    $vki = round($aktifBilgiler['kilo'] / ($boyMetre * $boyMetre), 1);
}
// STATE MANAGEMENT: URL üzerinden aktif kullanıcı kimliği takip edilir.
$aktifId = $_GET['u'];
$aktifIsim = "Bilinmiyor";

// Aktif kullanıcının ismini listeden eşleştiriyoruz.
foreach($kullanicilar as $k) {
    if($k['id'] == $aktifId) {
        $aktifIsim = $k['isim'];
        break;
    }
}

// BUSINESS LOGIC: Günlük hedefleri kodun içinden yönetebiliyoruz.
$gunlukHedef = 7000;

// Güncel tarihi Türkçe formatta hazırlıyoruz
$gunler = ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"];
$aylar = ["", "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
$formatliTarih = date('j') . " " . $aylar[date('n')] . " " . $gunler[date('w')];
$saat = date('H');
$selam = ($saat < 12) ? "Günaydın" : (($saat < 18) ? "İyi Günler" : "İyi Akşamlar");

/**
 * FORM DATA HANDLING & ERROR MANAGEMENT
 */
$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $girilenDeger = $_POST['deger'];
        $veriTipi = $_POST['tip'];

        if($girilenDeger <= 0) {
            throw new Exception("Hatalı Giriş: Değer sıfırdan büyük olmalıdır!");
        }

        $storage->kaydet($aktifId, $veriTipi, $girilenDeger);
        
        // POST-REDIRECT-GET: Form tekrarını önlemek için yönlendirme.
        echo "<script>window.location.href='index.php?u=$aktifId';</script>";
    } catch (Exception $e) {
        $mesaj = "<div class='alert alert-danger bg-dark text-danger border-danger mt-2 p-2 small'>Sistem Mesajı: " . $e->getMessage() . "</div>";
    }
}

/**
 * ANALİTİK VERİ HESAPLAMA (DASHBOARD LOGIC)
 */
$gecmis = $storage->kullaniciVerileriniGetir($aktifId);
$istatistik = ['Adım' => 0, 'Kalori' => 0, 'Su' => 0, 'Tansiyon' => 0];

foreach($gecmis as $kayit) {
    if(array_key_exists($kayit['tip'], $istatistik)) {
        if($kayit['tip'] == 'Tansiyon') {
            $istatistik[$kayit['tip']] = $kayit['deger'];
        } else {
            $istatistik[$kayit['tip']] += $kayit['deger'];
        }
    }
}

// PROGRESS RING CALCULATION
$yuzde = ($istatistik['Adım'] / $gunlukHedef) * 100;
if($yuzde > 100) $yuzde = 100;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Health Dashboard - <?= $aktifIsim ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #000; color: #fff; font-family: -apple-system, sans-serif; letter-spacing: -0.5px; }
        .card { border-radius: 20px; border: none; background-color: #1c1c1e !important; transition: 0.3s; }
        .stat-card { padding: 16px; border-left: 4px solid; }
        .text-secondary { color: #8e8e93 !important; }
        .user-select { background: #1c1c1e; color: white; border: 1px solid #38383a; border-radius: 12px; padding: 6px 12px; }
        
        /* DİNAMİK HALKA (PROGRESS RING) */
        .halka-container {
            position: relative; width: 120px; height: 120px; border-radius: 50%;
            background: conic-gradient(#ff2d55 <?= $yuzde * 3.6 ?>deg, #333 0deg);
            display: flex; align-items: center; justify-content: center;
        }
        .halka-ic { width: 95px; height: 95px; background-color: #1c1c1e; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        /* --- PROFESYONEL HOVER EFEKTLERİ --- */
.card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* iOS tarzı yaylanma efekti */
}

.card:hover {
    transform: translateY(-7px) scale(1.01); /* Hafif yükselme ve büyüme */
    background-color: #2c2c2e !important; /* Arka planı bir tık açar */
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6) !important; /* Derinlik katar */
    border: 1px solid #3a3a3c !important; /* Kenarlığı belirginleştirir */
    cursor: pointer;
}

/* Tablo satırları için de küçük bir dokunuş */
.table-hover tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
    transition: 0.2s;
}
    </style>
</head>
<body>
    <div class="container py-4">
        
        <!-- HEADER SECTION: Profesyonel Profil Yönetimi ve Logo Entegrasyonu -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            
            <!-- SOL TARAF: Uygulama Logosu ve Dinamik Tarih -->
            <div class="d-flex align-items-center">
                <img src="/SaglikProjesi/assets/icons/logo.png" alt="Health Logo" class="me-3" style="width: 70px; height: 70px; object-fit: contain;">
                <div>
                    <h1 class="fw-bold display-6 mb-0"><?= $selam ?>, <?= $aktifIsim ?></h1>
                    <!-- PHP ile gelen güncel tarih -->
                    <p class="text-secondary small mb-0"><?= $formatliTarih ?></p>
                </div>
            </div>

            <!-- SAĞ TARAF: Kullanıcı Resmi, İsim ve Çıkış -->
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <div class="fw-bold small text-white">Kullanıcı: <?= $aktifIsim ?></div>
                    <a href="login.php" class="text-danger small text-decoration-none fw-bold" style="font-size: 0.75rem;">
                        Oturumdan Çık →
                    </a>
                </div>
                
                <!-- Dinamik Profil Resmi Alanı -->
                <div style="width: 55px; height: 55px; border-radius: 50%; overflow: hidden; border: 2px solid #2c2c2e; background: #1c1c1e;">
                    <img src="../../assets/images/<?= $aktifIsim ?>.png" 
                         alt="<?= $aktifIsim ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.src='../../assets/icons/logo.png'">
                </div>
            </div>
        </div>
        
        <!-- KULLANICI FİZİKSEL VERİLERİ (KÜNYE) -->
<div class="row g-3 mb-4 text-center">
    <!-- YAŞ / CİNSİYET -->
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm" style="border-left: 4px solid #af52de !important;">
            <small style="color: #af52de; font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;">YAŞ / CİNSİYET</small>
            <b style="color: #af52de; font-size: 1.25rem;"><?= $aktifBilgiler['yas'] ?> / <?= $aktifBilgiler['cinsiyet'] ?></b>
        </div>
    </div>
    
    <!-- BOY -->
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm" style="border-left: 4px solid #34c759 !important;">
            <small style="color: #34c759; font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;">BOY</small>
            <b style="color: #34c759; font-size: 1.25rem;"><?= $aktifBilgiler['boy'] ?> cm</b>
        </div>
    </div>

    <!-- KİLO -->
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm" style="border-left: 4px solid #ff9500 !important;">
            <small style="color: #ff9500; font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;">KİLO</small>
            <b style="color: #ff9500; font-size: 1.25rem;"><?= $aktifBilgiler['kilo'] ?> kg</b>
        </div>
    </div>

    <!-- VKİ -->
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm" style="border-left: 4px solid #5856d6 !important;">
            <small style="color: #5856d6; font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;">VKİ</small>
            <b style="color: #5856d6; font-size: 1.25rem;"><?= $vki ?></b>
        </div>
    </div>
</div>
        <!-- 
    MODÜLER DUAL-RING ANALİZ PANELİ (HAREKET & ADIM) 
    Mimari Yaklaşım: Görsel hiyerarşiyi güçlendirmek adına başlık ortalanmış, 
    kartın her iki yanına veri tiplerini simgeleyen asimetrik borderlar eklenmiştir.
-->
<div class="row">
    <div class="col-12">
        <div class="card p-4 mb-4 shadow" style="border-radius: 25px; background-color: #1c1c1e !important; border-left: 5px solid #ff2d55 !important; border-right: 5px solid #ffcc00 !important;">
            
            <!-- UI SEMANTICS: Başlık merkezi konuma getirilmiştir -->
            <h5 class="fw-bold mb-4 text-center" style="color: #ffffff; font-size: 1.1rem; letter-spacing: -0.5px;">Aktivite Analizi</h5>

            <div class="row align-items-center">
                <!-- 1. BİLEŞEN: HAREKET (SOL KISIM) -->
                <div class="col-12 col-md-6 d-flex align-items-center mb-4 mb-md-0">
                    <div class="halka-container" style="background: conic-gradient(#ff2d55 <?= ($istatistik['Kalori'] / 300) * 360 ?>deg, #333 0deg); width: 110px; height: 110px;">
                        <div class="halka-ic" style="width: 85px; height: 85px;">
                            <h4 class="mb-0 fw-bold" style="color: #ff2d55; font-size: 1.2rem;">%<?= round(($istatistik['Kalori'] / 300) * 100) ?></h4>
                        </div>
                    </div>
                    <div class="ps-4">
                        <div style="font-size: 1.1rem; color: #ffffff; font-weight: 500; margin-top: -15px; margin-bottom: 5px;">Hareket</div>
                        <h4 class="fw-bold mb-0 text-danger" style="font-size: 1.4rem; letter-spacing: -1px;">
                            <?= number_format($istatistik['Kalori']) ?> / 300 KCAL
                        </h4>
                    </div>
                </div>

                <!-- 2. BİLEŞEN: ADIM (SAĞ KISIM - EN SAĞA ÇEKİLDİ) -->
                <div class="col-12 col-md-6 d-flex align-items-center justify-content-md-end">
                    <div class="d-flex align-items-center">
                        <div class="halka-container" style="background: conic-gradient(#ffcc00 <?= $yuzde * 3.6 ?>deg, #333 0deg); width: 110px; height: 110px;">
                            <div class="halka-ic" style="width: 85px; height: 85px;">
                                <h4 class="mb-0 fw-bold" style="color: #ffcc00; font-size: 1.2rem;">%<?= round($yuzde) ?></h4>
                            </div>
                        </div>
                        <div class="ps-4 pe-md-3">
                            <div style="font-size: 1.1rem; color: #ffffff; font-weight: 500; margin-top: -15px; margin-bottom: 5px;">Adım</div>
                            <h4 class="fw-bold mb-0" style="color: #ffcc00; font-size: 1.4rem; letter-spacing: -1px;">
                                <?= number_format($istatistik['Adım']) ?> / <?= number_format($gunlukHedef) ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DASHBOARD ANALİTİK KARTLARI (SU & TANSİYON) -->
<div class="row g-3 mb-4 text-center">
    <div class="col-6">
        <div class="card stat-card border-info" style="border-left-width: 5px !important;">
            <p class="text-info small fw-bold mb-0">SU</p>
            <h4 class="fw-bold mb-0 text-info"><?= $istatistik['Su'] ?> ml</h4>
        </div>
    </div>
    <div class="col-6">
        <div class="card stat-card border-primary" style="border-left-width: 5px !important;">
            <p class="text-primary small fw-bold mb-0">TANSİYON</p>
            <h4 class="fw-bold mb-0 text-primary"><?= $istatistik['Tansiyon'] > 0 ? $istatistik['Tansiyon'] : '--' ?></h4>
        </div>
    </div>
</div>

        <!-- INPUT SECTION: Kullanıcıdan veri toplama katmanı -->
        <div class="card p-4 shadow-lg mb-4 border border-secondary">
            <h6 class="text-secondary mb-3 small text-uppercase fw-bold">YENİ VERİ EKLE</h6>
            <form action="index.php?u=<?= $aktifId ?>" method="POST">
                <div class="row g-2">
                    <div class="col-12 col-md-5">
                        <select name="tip" class="user-select w-100 py-2">
                            <option value="Adım">Adım Sayısı</option>
                            <option value="Tansiyon">Tansiyon (mmHg)</option>
                            <option value="Kalori">Yakılan Kalori (kcal)</option>
                            <option value="Su">Su Tüketimi (ml)</option>
                        </select>
                    </div>
                    <div class="col-8 col-md-4">
                        <input type="number" name="deger" class="user-select w-100 py-2" placeholder="Değeri Girin" required>
                    </div>
                    <div class="col-4 col-md-3">
                        <button class="btn btn-primary w-100 py-2 fw-bold" type="submit">SİSTEME İŞLE</button>
                    </div>
                </div>
            </form>
            <?= $mesaj ?>
        </div>

        <!-- HISTORY SECTION: Veri listeleme ve geçmiş takibi -->
        <div class="card p-4 shadow">
    <h6 class="text-secondary mb-3 small text-uppercase fw-bold">GEÇMİŞ KAYITLAR (<?= $aktifIsim ?>)</h6>
    
    <div class="table-responsive text-center">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr class="text-secondary border-secondary small text-uppercase">
                    <th>Tarih</th>
                    <th>Kategori</th>
                    <th class="text-end pe-4">Değer</th>
                </tr>
            </thead>
            <tbody class="small align-middle">
                <?php if (empty($gecmis)): ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted border-0">Henüz veri kaydedilmedi.</td></tr>
                <?php else: 
                    // STACK DATA: Veriler son girilenden ilk girilene (descending) doğru sıralanır.
                    foreach(array_reverse($gecmis) as $satir): 
                        
                        /**
                         * DİNAMİK RENK MANTIĞI (UI/UX)
                         * Kategoriye göre badge (rozet) renkleri atanarak görsel hiyerarşi sağlanır.
                         * Apple Health renk paletine sadık kalınmıştır.
                         */
                        $badgeColor = 'secondary';
                        if($satir['tip'] == 'Adım') $badgeColor = 'warning text-dark';
                        if($satir['tip'] == 'Su') $badgeColor = 'info';
                        if($satir['tip'] == 'Tansiyon') $badgeColor = 'primary';
                        if($satir['tip'] == 'Kalori') $badgeColor = 'danger';
                ?>
                    <tr class="border-secondary">
                        <td class="py-3 text-secondary"><?= $satir['tarih'] ?></td>
                        <td>
                            <span class="badge bg-<?= $badgeColor ?> bg-opacity-75 px-2 py-1">
                                <?= $satir['tip'] ?>
                            </span>
                        </td>
                        <td class="text-end pe-4 fw-bold"><?= $satir['deger'] ?></td>
                    </tr>
                <?php endforeach; 
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

    </div>
</body>
</html>

<?php
/**try {
    if($girilenDeger <= 0) {
        throw new Exception("Hatalı Giriş: Değer sıfırdan büyük olmalıdır!");
    }
    $storage->kaydet($aktifId, $veriTipi, $girilenDeger);
} catch (Exception $e) {
    // Kullanıcıya anlamlı hata mesajı dönme zorunluluğu
    $mesaj = "Sistem Mesajı: " . $e->getMessage();
}
 */
?>