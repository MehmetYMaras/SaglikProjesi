<?php
/**
 * @class StorageService
 * @package HealthDashboard\Services
 * [YAZILIM MİMARİSİ: PERSISTENCE LAYER (VERİ ERİŞİM KATMANI)]
 * Bu sınıf 'Single Responsibility' prensibine uygun olarak
 * sistemdeki sağlık verilerinin kalıcı olarak saklanmasından sorumludur.
 */
class StorageService {
    /**
     * [ENCAPSULATION]
     * Dosya yolu private tutularak dış müdahale engellenmiştir.
     */
    private $path = __DIR__ . '/../../data/data/saglik_kayitlari.json';

    /**
     * Veritabanı simülasyonu: Yeni veriyi JSON formatında diske yazar.
     * @param int $userId Kullanıcı ID
     * @param string $tip Veri tipi (Adım, Su, vb.)
     * @param mixed $deger Ölçülen değer
     */
    public function kaydet($userId, $tip, $deger) {
        $veriler = [];
        if (file_exists($this->path)) {
            $veriler = json_decode(file_get_contents($this->path), true) ?? [];
        }

        // Yeni veri nesnesi oluşturma
        $veriler[] = [
            'user_id' => $userId,
            'tip' => $tip,
            'deger' => $deger,
            'tarih' => date("d.m.Y H:i"),
            'tarih_ham' => date("Y-m-d") // [GÜNCELLEME]: Gece 00:00 sıfırlama kontrolü için eklendi.
        ];

        // Veriyi JSON formatında ve 'Pretty Print' özelliğiyle kaydetme
        file_put_contents($this->path, json_encode($veriler, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Belirli bir kullanıcıya ait tüm geçmiş verileri filtreleyerek getirir.
     * @return array Filtrelenmiş veri seti
     */
    public function kullaniciVerileriniGetir($userId) {
        if (!file_exists($this->path)) return [];
        
        $veriler = json_decode(file_get_contents($this->path), true) ?? [];
        
        // Veri filtrasyonu (Functional Programming yaklaşımı)
        return array_filter($veriler, fn($v) => $v['user_id'] == $userId);
    }

    /**
     * [BUSINESS LOGIC: GÜNLÜK SIFIRLAMA KONTROLÜ]
     * Dashboard üzerindeki halkaların gece 00:00'da sıfırlanması için 
     * sadece 'bugün' girilen verileri süzer. Geçmiş veriler silinmez, sadece dashboard filtrelenir.
     */
    public function getBugunkuVeriler($userId) {
        $tumVeriler = $this->kullaniciVerileriniGetir($userId);
        $bugun = date("Y-m-d");

        // Sadece bugün kaydedilmiş verileri döndürür
        return array_filter($tumVeriler, fn($v) => isset($v['tarih_ham']) && $v['tarih_ham'] == $bugun);
    }
}
?>
