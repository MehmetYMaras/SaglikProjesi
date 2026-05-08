<?php
/**
 * @class Helper
 * @package HealthDashboard\Utils
 * [YARDIMCI ARAÇLAR (UTILITIES) KATMANI]
 * Bu sınıf uygulama genelinde tekrarlanan mantıksal süreçleri 
 * (tarih formatlama, VKİ analizi vb.) merkezi bir yerden yöneterek 
 * 'Code Duplication' (Kod Tekrarı) hatasını önlemek amacıyla oluşturuldu.
 */
class Helper {
    /**
     * [STATIC HELPER METHOD]
     * Ham tarih verisini kullanıcı dostu Türkçe formata çevirir.
     */
    public static function formatTarih($tarih) {
        return date('d.m.Y H:i', strtotime($tarih));
    }

    /**
     * [BUSINESS LOGIC HELPER]
     * VKİ değerine göre sağlık kategorisini ve buna uygun CSS sınıflarını döndürür.
     */
    public static function vkiAnaliz($vki) {
        if ($vki < 18.5) return ["durum" => "Zayıf", "renk" => "info"];
        if ($vki < 25) return ["durum" => "Normal", "renk" => "success"];
        if ($vki < 30) return ["durum" => "Kilolu", "renk" => "warning"];
        return ["durum" => "Obez", "renk" => "danger"];
    }
}
?>