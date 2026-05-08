# 🏥 - Akıllı Sağlık Takip Sistemi

Bu proje, **Ostim Teknik Üniversitesi** Bilgisayar Programcılığı bölümü Yazılım Geliştirme dersi final projesi kapsamında geliştirilmiştir. Kullanıcıların günlük sağlık verilerini (Adım, Tansiyon, Su, Kalori) modern bir arayüz üzerinden takip etmelerini sağlayan modüler bir web uygulamasıdır.

## 🚀 Teknik Özellikler

* **Mimar Yapı:** Proje, katmanlı bir mimari (Core, Data, Utils, Services, Modules) üzerine inşa edilmiştir.
* **OOP Prensipleri:** * **Inheritance (Kalıtım):** `Tansiyon` sınıfı `SaglikVerisi` sınıfından türetilmiştir.
    * **Encapsulation:** Veriler sınıflar içinde korunmuş (`private`/`protected`), erişim metodlar üzerinden sağlanmıştır.
* **Veri Yönetimi:** Veriler, taşınabilirlik ve performans açısından JSON formatında depolanmaktadır.
* **UI/UX:** Karanlık mod (Apple Health Style) estetiği, interaktif hover efektleri ve SVG tabanlı dinamik halkalar kullanılmıştır.

## 📁 Proje Klasör Yapısı

```text
SaglikProjesi/
├── assets/             # Görsel ve ikon dosyaları
├── data/               # JSON veritabanı dosyaları
├── docs/               # UML diyagramları ve gereksinim analizi (PDF)
└── src/
    ├── core/           # Uygulama çekirdeği (App.php)
    ├── data/           # Veri erişim katmanı (jsonprovider.php)
    ├── modules/        # OOP Nesne modelleri (SaglikVerisi, Tansiyon)
    ├── services/       # İş mantığı servisleri (StorageService)
    ├── ui/             # Kullanıcı arayüzü (login.php, index.php)
    └── utils/          # Yardımcı araçlar (helper.php)

🛠 Kurulum ve Çalıştırma
XAMPP veya benzeri bir PHP sunucusunu başlatın.

Dosyaları C:\xampp\htdocs\SaglikProjesi dizinine kopyalayın.

Tarayıcınızın adres çubuğuna şu adresi yazın:
http://localhost/SaglikProjesi/src/ui/login.php

Giriş ekranından bir profil seçerek sistemi kullanmaya başlayabilirsiniz.

📝 Kullanılan Teknolojiler
Backend: PHP 8.x (Nesne Yönelimli Programlama)

Frontend: HTML5, CSS3, Bootstrap 5

Veri Formatı: JSON

Geliştiren: Mehmet Yasin Maraş

Öğrenci No: 250408412

Kurum: Ostim Teknik Üniversitesi


---

