<?php
/**
 * @class JsonProvider
 * [VERİ ERİŞİM KATMANI]
 * JSON operasyonlarını standardize eden bu katman, 
 * dosya isimlendirmesinde küçük harf kuralına (jsonprovider.php) uygun olarak yapılandırılmıştır.
 */
class JsonProvider {
    /**
     * JSON dosyasını okur ve dizi (array) olarak döndürür.
     */
    public static function read($filePath) {
        if (!file_exists($filePath)) {
            return [];
        }
        $content = file_get_contents($filePath);
        return json_decode($content, true) ?? [];
    }

    /**
     * Veriyi JSON formatında dosyaya yazar.
     */
    public static function write($filePath, $data) {
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filePath, $jsonContent);
    }
}
?>