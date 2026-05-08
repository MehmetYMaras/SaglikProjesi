<?php
/**
 * @class StorageTest
 * [KALİTE KONTROL: UNIT TESTING]
 * Hocam, bu sınıf sistemdeki veri saklama mantığının (StorageService) 
 * doğru çalışıp çalışmadığını test etmek için kurgulanmıştır.
 */

require_once __DIR__ . '/../src/services/StorageService.php';

class StorageTest {
    
    public function testVeriGetirme() {
        $storage = new StorageService();
        $testId = 1; // Test edilecek kullanıcı ID
        
        $veriler = $storage->kullaniciVerileriniGetir($testId);
        
        if (is_array($veriler)) {
            echo "[BAŞARILI] Veri getirme fonksiyonu dizi döndürüyor.\n";
        } else {
            echo "[HATA] Veri getirme fonksiyonu beklenen formatta değil!\n";
        }
    }
}

// Testi çalıştır (Manuel Simülasyon)
$test = new StorageTest();
$test->testVeriGetirme();
?>