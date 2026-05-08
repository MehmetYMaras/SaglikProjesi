<?php
/**
 * @class Tansiyon
 * @package HealthDashboard\Modules
 * * [OOP PRENSİBİ: INHERITANCE (KALITIM)]
 * Projenin 2.b maddesinde belirtilen kalıtım zorunluluğu burada uygulanmıştır.
 * 'Tansiyon' sınıfı, 'SaglikVerisi' sınıfından türetilerek onun tüm özellik ve 
 * metodlarını miras almıştır. Böylece kod tekrarı önlenmiş ve modülerlik sağlanmıştır.
 */
class Tansiyon extends SaglikVerisi {

    /**
     * [OOP PRENSİBİ: POLYMORPHISM (ÇOK BİÇİMLİLİK)]
     * Burada bir polimorfizm örneği sergilenmiştir. 
     * Üst sınıftan gelen veriyi, Tansiyon birimine (mmHg) uygun şekilde 
     * biçimlendirerek özelleştirilmiş bir çıktı sunuyoruz.
     * * @return string Biçimlendirilmiş tansiyon verisi
     */
    public function formatliGoster() {
        // parent::getDeger() kullanımı ile kapsüllenmiş veriye güvenli erişim sağlanır.
        return $this->getDeger() . " mmHg";
    }

    /**
     * Bu metod sınıfa özgü bir yetenektir.
     * Tansiyon değerinin kritik seviyede olup olmadığını analiz eder.
     * @return string Sağlık durumu uyarısı
     */
    public function durumAnalizi() {
        // Örnek bir iş mantığı (Business Logic)
        if ($this->getDeger() > 140) {
            return "Yüksek Tansiyon Riski!";
        }
        return "Normal Seviye";
    }
}
?>
