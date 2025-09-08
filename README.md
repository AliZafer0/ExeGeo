# ExeGeo

Harita üzerinde çokgen/dikdörtgen çizimleri yapıp **coğrafi bölgeleri** (zones) yöneten; GeoJSON tabanlı CRUD API’leri ve modern bir web arayüzü olan hafif bir PHP uygulaması.

> **Stack:** PHP 8+, Leaflet + Leaflet.draw, Turf.js, Vanilla JS, Router/Controller tabanlı minimal MVC, PDO  
> **Öne çıkanlar:** Çiz–Kaydet–Listele–Sil akışı, DB sayfalama & arama, JSON modal, stil yönetimi, method-override desteği

---

## İçindekiler

- [Özellikler](#özellikler)
- [Mimari & Akış](#mimari--akış)
- [Dizin Yapısı](#dizin-yapısı)
- [Kurulum](#kurulum)
- [Yapılandırma](#yapılandırma)
- [Çalıştırma](#çalıştırma)
- [Ön Uç (UI)](#ön-uç-ui)
- [API Dokümantasyonu](#api-dokümantasyonu)
- [Veritabanı Şeması Önerisi](#veritabanı-şeması-önerisi)
- [Lisanslar & Kullanım Şartları](#lisanslar--kullanım-şartları)
- [Geliştirme Rehberi](#geliştirme-rehberi)
- [Sorun Giderme](#sorun-giderme)
- [Yol Haritası](#yol-haritası)
- [Teşekkür](#teşekkür)

---

## Özellikler

- 🗺️ **Çizim**: Leaflet.draw ile poligon ve dikdörtgen çiz, düzenle (edit)  
- 🧮 **Geometri ölçümleri**: Turf.js ile alan (m²), centroid, bbox  
- 💾 **Kalıcı kayıt**: GeoJSON + PostGIS uyumlu `geometry_geom` ekleme (varsa)  
- 📃 **Listeleme**: DB’den sayfalama + arama (q) ile kayıt çekme  
- 🗂️ **Haritada göster**: Listedeki kaydı tek tıkla haritaya getir/odaklan  
- 🗑️ **Silme**: Tekil & toplu silme, method-override fallback (`?_method=DELETE`)  
- 🎨 **Stil**: Dolgu/çizgi renkleri, opaklık, kalınlık, dash pattern; stil JSON olarak saklanabilir  
- 🔍 **POI/Adres arama**: Google Places (varsa) ya da OSM Nominatim ile arama  
- 📦 **Sıfır bağımlılık derleme**: Composer ile sadece autoload; ön uç CDN

---

## Mimari & Akış

```
Tarayıcı (Leaflet + Turf + app.js)
     │
     ├─ Çizim → GeoJSON Geometry
     │
     ├─ Ölçümler (area/centroid/bbox)
     │
     ├─ POST /api/v1/zones  (store)
     │
     ├─ GET  /api/v1/zones  (liste, sayfalama & arama)
     │         └─ GET /api/v1/zones/{id} (tekil + geometry_json)
     │
     └─ DELETE /api/v1/zones/{id} (sil)
```

Backend, minimal bir MVC iskeleti ile Router → Controller → (PDO) Database yolunu izler.

---

## Dizin Yapısı

> Proje kök: `C:\webdev\exegeo`

```
App/
  Config/
    Config.php
  Controllers/
    HomeController.php
    PlaceController.php
    ZoneController.php
  Core/
    Database.php
    Router.php
  Helpers/
  Models/
    Teachers.php
  Views/
    Error/
      404.php
    Partials/
      navbar.php
    home/
      index.php
public/
  assets/
    css/
      app.css
    images/
      .gitkeep
    js/
      app.js
  uploads/
    documents/.gitkeep
    images/.gitkeep
    videos/.gitkeep
  .htaccess
  index.php
vendor/
  composer/*
vendor/autoload.php
.htaccess
composer.json
index.php
README.md
```

---

## Kurulum

1) **Gereksinimler**
- PHP 8.1+ (PDO etkin)
- (Opsiyonel) PostGIS (geometry indeks/operasyonları için)
- Composer

2) **Bağımlılıklar**
```bash
composer install
```

3) **Virtual Host / PHP built-in**  
Geliştirme için `public/` dizinini web kök yapın (aşağıda [Çalıştırma](#çalıştırma)).

---

## Yapılandırma

`App/Config/Config.php` içinden çevre değişkenlerini okuyacak şekilde düzenleyin (örnek):

```php
return [
  'env' => $_ENV['APP_ENV'] ?? 'local',
  'db'  => [
    'dsn'  => $_ENV['DB_DSN']  ?? 'pgsql:host=localhost;port=5432;dbname=exegeo',
    'user' => $_ENV['DB_USER'] ?? 'postgres',
    'pass' => $_ENV['DB_PASS'] ?? '',
  ],
  'google_places_key' => $_ENV['GOOGLE_PLACES_KEY'] ?? null
];
```

`.env` (örnek):
```
APP_ENV=local
DB_DSN=pgsql:host=localhost;port=5432;dbname=exegeo
DB_USER=postgres
DB_PASS=postgres
GOOGLE_PLACES_KEY=YOUR_API_KEY   # boş ise OSM Nominatim kullanılır
```

> **Not:** MySQL kullanacaksanız `DB_DSN` için:  
> `mysql:host=127.0.0.1;port=3306;dbname=exegeo;charset=utf8mb4`

---

## Çalıştırma

**PHP built-in server** (geliştirme için):

```bash
php -S localhost:8080 -t public
```

- Web UI: http://localhost:8080/
- API: http://localhost:8080/api/v1/zones

Apache/Nginx kullanıyorsanız `public/` dizinini DocumentRoot yapın.  
`.htaccess` yönlendirmeleri zaten mevcut.

---

## Ön Uç (UI)

- **Konum:** `App/Views/home/index.php` + `public/assets/js/app.js`
- **Harita:** Leaflet (+ Leaflet.draw)  
- **Geometri:** Turf.js (`area`, `centroid`, `bbox`, `booleanPointInPolygon` vb.)
- **Özellikler:**  
  - Çizim, canlı stil değişikliği, seçili alan özeti  
  - DB’den sayfa sayfa liste & arama, haritada gösterme  
  - Silme için modal onayı ve method-override fallback

> **Not:** OSM karo servisini kullanırken yüksek trafikte kendi tile server’ınızı veya bir sağlayıcıyı tercih edin.

---

## API Dokümantasyonu

### Kaynak: Zones

#### GET `/api/v1/zones`
Liste + sayfalama + arama.

**Sorgu parametreleri**
- `page` (int, varsayılan `1`)
- `limit` (int, varsayılan `10`)
- `q` (string, isteğe bağlı; ada göre arama)
- `with=geometry` (isteğe bağlı; geometry_json alanını dahil eder)

**Yanıt (örnek)**
```json
{
  "page": 1,
  "per_page": 10,
  "last_page": 5,
  "total": 47,
  "items": [
    {
      "id": 12,
      "name": "Zone_AB12",
      "area_m2": 12345.67,
      "bbox_json": [29.0,36.1,29.2,36.3],
      "centroid_json": [29.1,36.2],
      "created_at": "2025-01-01 10:00:00"
    }
  ]
}
```

#### GET `/api/v1/zones/{id}`
Tek kaydı getirir. `with=geometry` verilirse `geometry_json` döner.

```json
{
  "id": 12,
  "name": "Zone_AB12",
  "geometry_json": {
    "type": "Polygon",
    "coordinates": [[[29.0,36.1],[29.2,36.1],[29.2,36.3],[29.0,36.3],[29.0,36.1]]]
  },
  "area_m2": 12345.67,
  "bbox_json": [29.0,36.1,29.2,36.3],
  "centroid_json": [29.1,36.2],
  "style_json": {"fill":"#1e3a8a", "stroke":"#5b9cff", "weight":2, "fillOpacity":0.25},
  "created_at": "2025-01-01 10:00:00"
}
```

#### POST `/api/v1/zones`
Yeni kayıt oluşturur.

**Gönderim (JSON)**
```json
{
  "name": "Zone_123",
  "geometry": { "type":"Polygon", "coordinates":[[[29.0,36.1],[29.2,36.1],[29.2,36.3],[29.0,36.3],[29.0,36.1]]] },
  "bbox": [29.0,36.1,29.2,36.3],
  "centroid": [29.1,36.2],
  "area_m2": 12345.67,
  "tags": ["selected","blue"],
  "meta": {"note":"optional"},
  "style_json": {"fill":"#1e3a8a","stroke":"#5b9cff","weight":2,"fillOpacity":0.25}
}
```

**Yanıt**
```json
{ "id": 42, "name": "Zone_123", "ok": true }
```

#### DELETE `/api/v1/zones/{id}`
Tek kaydı siler. Sunucu `DELETE` desteklemiyorsa:

- **Override 1:** `POST /api/v1/zones/{id}?_method=DELETE`
- **Override 2:** Gövdede `{ "_method": "DELETE" }`

**Yanıt**
```json
{ "ok": true, "deleted_id": 42 }
```

#### DELETE `/api/v1/zones` (opsiyonel toplu sil)
Gövde: `{ "ids": [1,2,3] }` → yanıt `{ "ok": true, "deleted": 3 }`

---

### Kaynak: Places (Arama)

#### GET `/api/v1/places?q=ankara%20hastane[&provider=google|osm]`
- Google API anahtarı varsa otomatik Google → aksi halde OSM Nominatim
- Yanıt:
```json
{
  "provider": "osm",
  "count": 2,
  "items": [
    { "name":"X Hastanesi", "lat":39.9, "lng":32.85, "address":"...", "provider":"osm" }
  ]
}
```

---

## Veritabanı Şeması Önerisi

**PostgreSQL + PostGIS**
```sql
CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `geometry_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`geometry_json`)),
  `geometry_geom` geometry NOT NULL,
  `bbox_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bbox_json`)),
  `centroid_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`centroid_json`)),
  `area_m2` double DEFAULT NULL,
  `tags_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags_json`)),
  `meta_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_json`)),
  `bbox_geom` polygon NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_zones_geom ON zones USING GIST(geometry_geom);
CREATE INDEX idx_zones_name ON zones (name);
```

**MySQL** (PostGIS özelliği yoksa `geometry_geom` yerine `geometry_json` üzerinden çalışın.)

---

## Lisanslar & Kullanım Şartları

- **Turf.js** → MIT Lisansı  
- **Leaflet** → BSD-2-Clause  
- **Leaflet.draw** → MIT  
- **OpenStreetMap Tiles** → OSM Tile Usage Policy’e uygun kullanın.  

> **Sonuç:** Kodu GitHub’da **herkese açık** yayınlamada bu kütüphaneler lisans açısından uygundur. Harici servislerin kullanım koşullarına uyun.

---

## Geliştirme Rehberi

### Kod Standardı
- PHP: PSR-4 autoload, Controller sınıfları tek sorumluluk
- JS: Modüler, saf JS
- İsimlendirme: `snake_case` (DB), `camelCase` (JS/PHP)

### Commit Mesajı
```
feat(zones): add pagination with search
fix(api): method-override fallback for delete
chore(ui): adjust styles for dark theme
```

### Hata Formatı (API)
```json
{ "ok": false, "error": "VALIDATION_ERROR", "message": "Geometry is required." }
```

### Sayfalama Formatı (API)
```json
{ "page":1, "per_page":10, "last_page":5, "total":47, "items":[...] }
```

### CORS
Aynı origin’de çalışır. Ayrı domain olacaksa uygun CORS başlıkları ekleyin.

---

## Sorun Giderme

- **“Toplam kayıt alınamadı.” (`COUNT_FAILED`)** → COUNT(*) sorgusunu ve bağlantıyı kontrol edin.  
- **`DELETE` 405/501** → method-override kullanın (`?_method=DELETE`).  
- **OSM Tiles yavaş** → Kendi tile sunucunuzu düşünün.  
- **Google Places boş** → API key, fatura ve kota ayarlarını kontrol edin.

---

## Yol Haritası

- [ ] Union/Intersect/Difference UI + DB  
- [ ] Çoklu seçim & toplu stil  
- [ ] Auth (JWT / session)  
- [ ] Export/Import (GeoJSON)  
- [ ] PHPUnit + CI

---

## Teşekkür

- Leaflet, Leaflet.draw, Turf.js, OSM

---

### Hızlı Başlangıç

```bash
composer install
php -S localhost:8080 -t public
# http://localhost:8080
```
