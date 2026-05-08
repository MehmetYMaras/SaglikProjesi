<?php
/**
 * @class App
 * @package HealthDashboard\Core
 * * [YAZILIM MİMARİSİ: CORE ENGINE / ÇEKİRDEK YÖNETİCİ]
 * Bu sınıf uygulamanın 'Kernel' (Çekirdek) katmanını temsil eder. 
 * Projenin modüler yapısını kontrol altında tutmak ve servislerin 
 * başlatılmasını (initialization) merkezi bir noktadan yönetmek amacıyla tasarlanmıştır.
 */
class App {
    /**
     * @var StorageService $storage
     * Uygulama genelinde kullanılacak veri erişim servisi.
     */
    private static $storage;

    /**
     * [SİSTEM BAŞLATICI - INITIALIZER]
     * Bu metod sistemin yaşam döngüsünü başlatır. 
     * Gerekli tüm bağımlılıkları (Dependencies) yükleyerek uygulamanın 
     * tutarlı bir şekilde ayağa kalkmasını sağlar.
     */
    public static function run() {
        // Bağımlılıkların sisteme dahil edilmesi (Modüler Yükleme)
        require_once __DIR__ . '/../utils/helper.php';
        require_once __DIR__ . '/../data/JsonProvider.php';
        require_once __DIR__ . '/../services/StorageService.php';
        require_once __DIR__ . '/../modules/SaglikVerisi.php';
        require_once __DIR__ . '/../modules/Tansiyon.php';
        
        // Servis katmanının örneklendirilmesi
        self::$storage = new StorageService();
    }

    /**
     * [SINGLETON BENZERİ ERİŞİM NOKTASI]
     * Uygulama içindeki diğer bileşenlerin (UI, API vb.) 
     * veri depolama servisine güvenli ve merkezi bir şekilde 
     * erişmesini sağlayan statik bir arayüz sunar.
     */
    public static function getStorage() {
        return self::$storage;
    }
}
?>