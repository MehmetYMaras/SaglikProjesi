<?php
/**
 * @class SaglikVerisi
 * @package HealthDashboard\Modules
 * * [OOP PRENSİBİ: ABSTRACTION (SOYUTLAMA)]
 * Bu sınıf sistemdeki tüm sağlık metriklerinin (Adım, Kalori, Tansiyon vb.) 
 * ortak özelliklerini barındıran 'Base Class' (Temel Sınıf) olarak tasarlanmıştır.
 * Doğrudan bir nesne üretmek yerine, diğer sınıflara şablon olması amaçlanmıştır.
 */
class SaglikVerisi {
    /**
     * [OOP PRENSİBİ: ENCAPSULATION (KAPSÜLLEME)]
     * Veri güvenliği ve tutarlılığı için değişkenler 'protected' olarak tanımlanmıştır.
     * Bu sayede veriye doğrudan dışarıdan müdahale engellenmiş, sadece kalıtım yoluyla 
     * veya kontrollü metodlar (Getters/Setters) aracılığıyla erişim izni verilmiştir.
     */
    protected $deger; 
    protected $tarih;

    /**
     * @param mixed $deger Girilen sağlık verisi değeri
     * Sınıf ilklendirilirken sistem tarihini otomatik olarak atar.
     */
    public function __construct($deger) {
        $this->deger = $deger;
        $this->tarih = date("d.m.Y H:i");
    }

    /**
     * [GETTER METHOD]
     * Encapsulation gereği, private/protected verilere kontrollü erişim sağlar.
     * @return mixed
     */
    public function getDeger() {
        return $this->deger;
    }

    /**
     * Kaydın oluşturulma zamanını döndürür.
     * @return string
     */
    public function getTarih() {
        return $this->tarih;
    }
}
?>
