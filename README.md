<h1 align="center">🌍 ExeGeo</h1>
<p align="center">
  Harita üzerinde çokgen/dikdörtgen çizimleri yapıp <b>coğrafi bölgeleri</b> (zones) yöneten; <br/>
  GeoJSON tabanlı CRUD API’leri ve modern bir web arayüzü olan hafif PHP uygulaması.
</p>

<p align="center">
  <b>Stack:</b> PHP 8+, Leaflet + Leaflet.draw, Turf.js, Vanilla JS, minimal MVC (Router/Controller), PDO <br/>
  <b>Öne çıkanlar:</b> Çiz–Kaydet–Listele–Sil akışı, DB sayfalama & arama, JSON modal, stil yönetimi, method-override desteği
</p>

---

## 📑 İçindekiler

<ul style="list-style:none; padding-left:0; line-height:1.8; text-align:center;">
  <li>🌟 <a href="#-özellikler" style="color:#4ade80; text-decoration:none;">Özellikler</a></li>
  <li>🏗️ <a href="#-mimari--akış" style="color:#60a5fa; text-decoration:none;">Mimari & Akış</a></li>
  <li>📂 <a href="#-dizin-yapısı" style="color:#facc15; text-decoration:none;">Dizin Yapısı</a></li>
  <li>🔧 <a href="#-kurulum" style="color:#f472b6; text-decoration:none;">Kurulum</a></li>
  <li>⚙️ <a href="#-yapılandırma" style="color:#a78bfa; text-decoration:none;">Yapılandırma</a></li>
  <li>▶️ <a href="#-çalıştırma" style="color:#22d3ee; text-decoration:none;">Çalıştırma</a></li>
  <li>🎨 <a href="#-ön-uç-ui" style="color:#fb923c; text-decoration:none;">Ön Uç (UI)</a></li>
  <li>📡 <a href="#-api-dokümantasyonu" style="color:#38bdf8; text-decoration:none;">API Dokümantasyonu</a></li>
  <li>🗄️ <a href="#-veritabanı-şeması-önerisi" style="color:#84cc16; text-decoration:none;">Veritabanı Şeması Önerisi</a></li>
  <li>📜 <a href="#-lisanslar--kullanım-şartları" style="color:#f87171; text-decoration:none;">Lisanslar & Kullanım Şartları</a></li>
  <li>👨‍💻 <a href="#-geliştirme-rehberi" style="color:#0ea5e9; text-decoration:none;">Geliştirme Rehberi</a></li>
  <li>🛠️ <a href="#-sorun-giderme" style="color:#14b8a6; text-decoration:none;">Sorun Giderme</a></li>
  <li>🗺️ <a href="#-yol-haritası" style="color:#eab308; text-decoration:none;">Yol Haritası</a></li>
  <li>🙏 <a href="#-teşekkür" style="color:#ec4899; text-decoration:none;">Teşekkür</a></li>
</ul>
---

## 🚀 Özellikler
- 🗺️ **Çizim**: Leaflet.draw ile poligon/dikdörtgen çiz, düzenle  
- 🧮 **Geometri ölçümleri**: Turf.js ile alan (m²), centroid, bbox  
- 💾 **Kalıcı kayıt**: GeoJSON + PostGIS uyumlu `geometry_geom` ekleme (varsa)  
- 📃 **Listeleme**: DB’den sayfalama + arama (q) ile kayıt çekme  
- 🗂️ **Haritada göster**: Listedeki kaydı tek tıkla haritaya getir/odaklan  
- 🗑️ **Silme**: Tekil & toplu silme, method-override fallback (`?_method=DELETE`)  
- 🎨 **Stil**: Renk, opaklık, kalınlık, dash pattern; stil JSON olarak saklanabilir  
- 🔍 **POI/Adres arama**: Google Places veya OSM Nominatim  
- 📦 **Hafif kurulum**: Composer autoload + frontend CDN

---

## 🏗️ Mimari & Akış
```text
Tarayıcı (Leaflet + Turf + app.js)
   │
   ├─ Çizim → GeoJSON Geometry
   ├─ Ölçümler (area/centroid/bbox)
   ├─ POST /api/v1/zones  (store)
   ├─ GET  /api/v1/zones  (liste, sayfalama & arama)
   │         └─ GET /api/v1/zones/{id} (tekil)
   └─ DELETE /api/v1/zones/{id} (sil)
```
Backend: Router → Controller → PDO → DB

---

## 📂 Dizin Yapısı
> Proje kök: `C:\webdev\exegeo`
```
App/
  Config/Config.php
  Controllers/{Home,Place,Zone}Controller.php
  Core/{Database,Router}.php
  Models/...
  Views/
    Error/404.php
    Partials/navbar.php
    home/index.php
public/
  assets/{css,js,images}
  uploads/{documents,images,videos}
  index.php
app/
  database/
    zones.sql   <-- 📌 Veritabanı şeması burada
vendor/
composer.json
README.md
.htaccess
```

---

## 🔧 Kurulum
**Gereksinimler**
- PHP 8.1+ (PDO aktif)
- MariaDB/PostgreSQL (PostGIS opsiyonel)
- Composer

**Adımlar**
```bash
composer install
```

---

## ⚙️ Yapılandırma
`App/Config/Config.php` → `.env` üzerinden ayar
```env
APP_ENV=local
DB_DSN=mysql:host=127.0.0.1;port=3306;dbname=exegeo;charset=utf8mb4
DB_USER=root
DB_PASS=secret
GOOGLE_PLACES_KEY=YOUR_API_KEY
```

---

## ▶️ Çalıştırma
```bash
php -S localhost:8080 -t public
```
- UI → http://localhost:8080  
- API → http://localhost:8080/api/v1/zones

---

## 🎨 Ön Uç (UI)
- Leaflet (+ Leaflet.draw) → çizim, düzenleme  
- Turf.js → alan, centroid, bbox hesaplama  
- Özellikler: DB’den listeleme, silme, modal, method override

---

## 📡 API Dokümantasyonu
### Zones
- `GET /api/v1/zones` → sayfalama + arama  
- `GET /api/v1/zones/{id}` → tekil kayıt (opsiyonel geometry_json)  
- `POST /api/v1/zones` → yeni kayıt  
- `DELETE /api/v1/zones/{id}` → silme (override destekli)

### Places
- `GET /api/v1/places?q=ankara%20hastane` → Google / OSM arama

---

## 🗄️ Veritabanı Şeması Önerisi
- SQL dosyası: `app/database/zones.sql`  
- SRID: 4326 (WGS84)  
- İndeks: GIST (geometry), name

(PostGIS → geometry_geom, MySQL → geometry_json kullanılabilir)

---

## 📜 Lisanslar & Kullanım
- ExeGeo → MIT  
- Turf.js, Leaflet.draw → MIT  
- Leaflet → BSD-2-Clause  
- OSM Tiles → kullanım politikasına uyun

---

## 👨‍💻 Geliştirme Rehberi
- PHP: PSR-4, tek sorumluluklu Controller  
- JS: Modüler, saf JS  
- Commit formatı: `feat()`, `fix()`, `chore()`  
- API hata formatı:  
```json
{ "ok": false, "error": "VALIDATION_ERROR", "message": "Geometry required" }
```

---

## 🛠️ Sorun Giderme
- `DELETE` 405 → `?_method=DELETE` kullan  
- OSM tile yavaş → alternatif provider  
- Google Places boş → API key/fatura ayarını kontrol et

---

## 🗺️ Yol Haritası
- [ ] GeoJSON import/export  
- [ ] Çoklu seçim & toplu stil  
- [ ] Auth (JWT/session)  
- [ ] PHPUnit testleri  
- [ ] Union/Intersect/Difference UI  

---

## 🙏 Teşekkür
- Leaflet, Leaflet.draw, Turf.js, OSM

---

## ⚡ Hızlı Başlangıç
```bash
composer install
php -S localhost:8080 -t public
# Tarayıcı: http://localhost:8080
```
