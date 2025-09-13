<?php
// Vars: $title, $page, $data
?><!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'ExeGeo') ?></title>

  <!-- Leaflet & Draw CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css"/>

  <!-- App CSS -->
  <link rel="stylesheet" href="/public/assets/css/app.css">
  <style>
:root {
  --bg:#0b1220; --bg2:#0f172a; --fg:#e2e8f0; --muted:#94a3b8; --card:#0c1220; --border:#1f2937; --accent:#22d3ee;
  --ok:#10b981; --warn:#f59e0b; --err:#ef4444; --info:#60a5fa;
  --chip:#334155; --chip-hover:#475569;
}

*{box-sizing:border-box}
body{
  margin:0;
  background:linear-gradient(180deg,#0b1220,#0f172a);
  color:var(--fg);
  font:400 15px/1.6 system-ui, -apple-system, Segoe UI, Roboto;
}
.wrap{max-width:1200px;margin:auto;padding:24px}
.nav{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:20px}
.brand{font-weight:700;letter-spacing:.4px}
.chip{font-size:12px;padding:2px 8px;border:1px solid var(--chip);border-radius:999px;color:var(--muted)}
.card{
  background:linear-gradient(180deg,#0f172a,#0b1220);
  border:1px solid var(--border);
  border-radius:16px;
  padding:18px;
  box-shadow:0 10px 30px rgba(0,0,0,.25);
  margin-bottom:18px;
}

.title{font-size:26px;margin:0 0 6px}
.muted{color:var(--muted)}
.mono{font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace}

.btn{
  appearance:none;border:1px solid var(--chip);background:#0b1220;color:var(--fg);
  padding:9px 12px;border-radius:10px;cursor:pointer;transition:.15s ease-in-out
}
.btn:hover{border-color:var(--chip-hover);transform:translateY(-1px)}
.btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
.btn--accent{border-color:#155e75}
.btn--danger{border-color:#7f1d1d}
.btn--small{padding:6px 8px;font-size:13px;border-radius:8px}

.grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media (max-width:1000px){ .grid3{grid-template-columns:1fr} }

.endpoint{border:1px solid var(--border);border-radius:12px;padding:12px;background:#0b1220}
.ep-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.method{font-weight:700;font-size:12px;padding:2px 8px;border-radius:999px;border:1px solid var(--border)}
.GET{color:#10b981}.POST{color:#22d3ee}.PUT{color:#f59e0b}.DELETE{color:#ef4444}
.path{font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;font-size:13px}

.map-shell{display:grid;grid-template-columns:1.2fr .8fr;gap:16px}
@media (max-width:1100px){ .map-shell{grid-template-columns:1fr} }

/* ====== MAP & LEAFLET (Dark) ====== */
#map{
  height:460px;border-radius:16px;border:1px solid var(--border);overflow:hidden;background:#0b1220
}
.leaflet-container{background:linear-gradient(180deg,#0b1220,#0f172a)}
.leaflet-bar{box-shadow:0 8px 24px rgba(0,0,0,.35);border:1px solid var(--border);overflow:hidden;border-radius:10px;}
.leaflet-bar a{background:#0b1220;color:var(--fg);border-bottom:1px solid var(--border)}
.leaflet-bar a:hover{background:#0f172a}
.leaflet-bar a.leaflet-disabled{background:#0b1220;opacity:.5}
.leaflet-control-zoom a{font-weight:700}

/* Draw toolbar – karanlık kutu + beyaz ikon */
.leaflet-draw-toolbar a{
  position: relative;width:26px;height:26px;line-height:26px;padding:0;background:#0b1220 !important;border-bottom:1px solid var(--border);
  background-image: none !important;
}
.leaflet-draw-toolbar a::after{
  content:"";position:absolute;inset:0;width:100%;height:100%;
  background-image:url("https://unpkg.com/leaflet-draw@1.0.4/dist/images/spritesheet.png");
  background-repeat:no-repeat;background-position:var(--pos,0 0);background-size:inherit;
  filter:brightness(0) invert(1);pointer-events:none;
}
.leaflet-draw-toolbar a:hover{background:#0f172a !important}
.leaflet-draw-toolbar a:hover::after{filter:brightness(0) saturate(1.4) invert(69%) sepia(12%) saturate(748%) hue-rotate(140deg) brightness(98%)}
@media (-webkit-min-device-pixel-ratio:2),(min-resolution:192dpi){
  .leaflet-draw-toolbar a::after{background-image:url("https://unpkg.com/leaflet-draw@1.0.4/dist/images/spritesheet-2x.png");background-size:260px 26px}
}
.leaflet-draw-actions a{background:#0b1220;color:var(--fg);border-color:var(--chip)}
.leaflet-draw-actions a:hover{background:#111827}

/* Tooltip & popup */
.leaflet-tooltip{background:#0b1220;color:var(--fg);border:1px solid var(--border);box-shadow:0 6px 20px rgba(0,0,0,.35)}
.leaflet-popup-content-wrapper{background:#0b1220;color:var(--fg);border:1px solid var(--border);box-shadow:0 16px 40px rgba(0,0,0,.45)}
.leaflet-popup-tip{background:#0b1220}

/* Vertex / edit noktaları */
.leaflet-editing-icon,.leaflet-vertex-icon,.leaflet-edit-move{
  width:12px;height:12px;border-radius:50%;background:var(--accent) !important;border:2px solid #0b1220 !important;box-shadow:0 0 0 2px rgba(34,211,238,.35)
}

/* Sidebar */
.side{display:flex;flex-direction:column;gap:12px}
.side .panel{border:1px solid var(--border);border-radius:12px;background:#0b1220;padding:12px}
.side h4{margin:0 0 8px;font-size:16px}
.list{display:flex;flex-direction:column;gap:8px;max-height:200px;overflow:auto}
.chip-row{display:flex;gap:8px;flex-wrap:wrap}
.tag{font-size:12px;border:1px solid var(--border);border-radius:999px;padding:2px 8px;color:var(--muted)}
.row{display:flex;align-items:center;justify-content:space-between;gap:8px;border:1px dashed #243043;border-radius:10px;padding:8px}
.row.selected{border-style:solid;border-color:#3b82f6;background:#0b1b35}
.row .meta{display:flex;gap:10px;align-items:center}
.row .name{font-weight:600}
.row .small{color:var(--muted);font-size:12px}
.row .actions{display:flex;gap:6px}

pre.code{white-space:pre-wrap;color:#cbd5e1;max-height:180px;overflow:auto;margin:0}
.stat{display:flex;gap:10px;align-items:center}
.dot{width:8px;height:8px;border-radius:50%;background:var(--accent);box-shadow:0 0 18px var(--accent)}

.select,.input{width:100%;appearance:none;background:#0b1220;color:var(--fg);border:1px solid var(--chip);border-radius:10px;padding:10px 12px}
.row-inline{display:flex;gap:8px;align-items:center}

/* Stil panelindeki mini palet butonları */
.swatch{width:22px;height:22px;border-radius:6px;border:1px solid var(--border);cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.25)}
.swatch:hover{transform:translateY(-1px)}
.swatch[title]{outline:none}

/* Scrollbar koyu tema uyumu */
::-webkit-scrollbar{width:10px;height:10px}
::-webkit-scrollbar-thumb{background:#1f2937;border-radius:8px;border:2px solid #0b1220}
::-webkit-scrollbar-track{background:#0b1220}

/* ======= Delete Confirm MODAL & Toast (ExeGeo uyumlu) ======= */
.exg-modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;z-index:9999}
.exg-hidden{display:none!important}
.exg-backdrop{position:absolute;inset:0;background:rgba(2,6,23,.62);backdrop-filter:blur(2px)}
.exg-dialog{
  position:relative;min-width:340px;max-width:92vw;background:var(--card);color:var(--fg);
  border:1px solid var(--border);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.5);padding:16px;animation:exg-pop .12s ease-out
}
@keyframes exg-pop{from{transform:scale(.97);opacity:.7}to{transform:scale(1);opacity:1}}
.exg-header{font-weight:700;font-size:18px;letter-spacing:.2px;margin-bottom:8px}
.exg-body{font-size:14px;color:var(--fg);opacity:.95}
.exg-body p{margin:8px 0}
.exg-meta{font-family:ui-monospace, SFMono-Regular, Menlo, monospace;color:var(--muted);font-size:12px}
.exg-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:14px}
.exg-btn{padding:10px 14px;border-radius:12px;border:1px solid var(--border);background:var(--bg2);color:var(--fg);font-weight:600;cursor:pointer;transition:transform .06s ease, background .12s}
.exg-btn:hover{transform:translateY(-1px)}
.exg-btn-ghost{background:transparent}
.exg-btn-danger{background:var(--err);border-color:#b91c1c}
.exg-btn-danger:hover{filter:brightness(.95)}
.exg-accent{color:var(--accent)}
.exg-kbd{font-size:12px;padding:2px 6px;border:1px solid var(--border);border-radius:6px;background:var(--bg2);margin-left:4px}
.exg-toast{position:fixed;right:16px;bottom:16px;background:var(--bg2);color:var(--fg);border:1px solid var(--border);padding:10px 12px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.35);z-index:10000}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="nav">
      <div class="brand">🌌 <strong><?= htmlspecialchars($data['project']) ?></strong></div>
      <div class="chip">environment: <span class="mono"><?= htmlspecialchars($_ENV['APP_ENV'] ?? 'local') ?></span></div>
    </div>

    <!-- HERO -->
    <div class="card">
      <h1 class="title"><?= htmlspecialchars($data['project']) ?> — Ana Sayfa</h1>
      <p class="muted"><?= htmlspecialchars($data['tagline']) ?></p>
      <div class="chip-row" style="margin-top:8px">
        <span class="tag">Router → HomeController@index</span>
        <span class="tag">Statik → /public/assets/*</span>
        <span class="tag">API → GET /api/v1/zones</span>
      </div>
    </div>

    <!-- MAP + SIDEBAR -->
    <div class="card">
      <h3 style="margin:0 0 10px">Harita ile Çoklu Bölge Seçimi</h3>
      <div class="map-shell">
        <div>
          <div id="map"></div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px">
            <button class="btn btn--small" id="btn-fit">Haritayı Alanlara Uydur</button>
            <button class="btn btn--small" id="btn-clear-all">Tüm Alanları Temizle</button>
            <button class="btn btn--small btn--accent" id="btn-save-selected">Seçileni Kaydet (API)</button>
            <button class="btn btn--small btn--accent" id="btn-save-all">Tümünü Kaydet (API)</button>
          </div>
          <div style="margin-top:10px" class="muted mono" id="save-out"></div>
          
          <div class="panel">
            <h4>Stil / Renk</h4>
            <div class="row-inline" style="gap:6px;flex-wrap:wrap">
              <label class="muted" style="font-size:12px">Dolgu</label>
              <input type="color" id="style-fill" value="#1e3a8a" title="Dolgu rengi" />
              <label class="muted" style="font-size:12px">Stroke</label>
              <input type="color" id="style-stroke" value="#5b9cff" title="Stroke rengi" />
            </div>
            <div class="row-inline" style="gap:6px;flex-wrap:wrap;margin-top:8px">
              <label class="muted" style="font-size:12px">Dolgu Opaklığı</label>
              <input type="range" id="style-fillop" min="0" max="1" step="0.05" value="0.25" />
              <label class="muted" style="font-size:12px">Kalınlık</label>
              <input type="range" id="style-weight" min="1" max="8" step="1" value="2" />
            </div>
            <div class="row-inline" style="gap:6px;flex-wrap:wrap;margin-top:8px">
              <label class="muted" style="font-size:12px">Çizgi</label>
              <select class="select" id="style-dash" style="max-width:180px">
                <option value="">Düz</option>
                <option value="6 6">Kesik (6-6)</option>
                <option value="2 8">Nokta (2-8)</option>
                <option value="12 6 2 6">Uzun-Kısa</option>
              </select>
              <!-- Uygula butonunu kaldırdık; anlık uygulanıyor -->
              <button class="btn btn--small" id="style-reset">Varsayılan</button>
              <button class="btn btn--small btn--accent" id="style-copy-all" title="Bu stili tüm alanlara uygula">Tümüne Kopyala</button>
              <button class="btn btn--small" id="style-random">Rastgele</button>
            </div>
            <div class="muted mono" id="style-out" style="margin-top:8px;font-size:12px">—</div>
          </div>
        </div>

        <div class="side">
          <!-- ŞEHİR SEÇİM PANELİ -->
          <div class="panel">
            <h4>Şehir Seçimi</h4>
            <div class="row-inline">
              <select class="select" id="city-select" title="Şehir seçiniz">
                <option value="">— Şehir seçin —</option>
              </select>
              <!-- Butona gerek yok, change’de uçuyor -->
              <!-- <button class="btn btn--small" id="btn-city-go">Git</button> -->
            </div>
            <div class="muted" style="margin-top:8px;font-size:13px">Şehir seçince harita otomatik zoom yapar.</div>
          </div>

          <!-- ADRES / POI ARAMA PANELİ -->
          <div class="panel">
            <h4>Adres / POI Arama (Google / OSM)</h4>
            <div class="row-inline" style="gap:6px; margin-bottom:6px">
              <input class="input" id="poi-q" placeholder="yazdıkça arar: ör. relax hotel" />
              <!-- <button class="btn btn--small" id="poi-search">Ara</button> -->
            </div>
            <div class="row-inline" style="gap:6px; margin-bottom:6px">
              <select class="select" id="poi-provider" title="Sağlayıcı">
                <option value="">Otomatik (Key varsa Google, yoksa OSM)</option>
                <option value="google">Google Places</option>
                <option value="osm">OpenStreetMap (Nominatim)</option>
              </select>
              <span class="muted mono" id="poi-provider-info" style="margin-left:auto"></span>
            </div>
            <div class="list" id="poi-results" style="margin-top:8px"></div>
            <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap">
              <!-- Kontrol butonunu da kaldırdık; seçimde otomatik kontrol -->
              <!-- <button class="btn btn--small btn--accent" id="poi-check">Kontrol Et</button> -->
              <a class="btn btn--small" id="poi-open" href="#" target="_blank" rel="noopener" style="display:none">Haritada Aç</a>
            </div>
            <div class="muted mono" id="poi-out" style="margin-top:6px"></div>
          </div>

          <!-- SEÇİLİ ALAN ÖZET + STİL -->
          <div class="panel">
            <h4>Seçilen Alan Özeti</h4>
            <div class="grid3">
              <div class="endpoint">
                <div class="ep-head"><span class="method GET">Alan</span><span class="path">m²</span></div>
                <div class="muted" id="m-area">—</div>
              </div>
              <div class="endpoint">
                <div class="ep-head"><span class="method GET">Merkez</span><span class="path">lat,lng</span></div>
                <div class="muted" id="m-centroid">—</div>
              </div>
              <div class="endpoint">
                <div class="ep-head"><span class="method GET">BBox</span><span class="path">minX,minY,maxX,maxY</span></div>
                <div class="muted" id="m-bbox">—</div>
              </div>
            </div>
            <div class="endpoint" style="margin-top:12px">
              <div class="ep-head"><span class="method POST">GeoJSON</span><span class="path">geometry</span></div>
              <pre class="code mono" id="m-geojson"></pre>
            </div>
          </div>

          <!-- ALAN LİSTESİ -->
          <div class="panel">
            <h4>Alan Listesi</h4>
            <div class="list" id="zone-list"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- DB LİSTE + SAYFALAMA -->
    <div class="card">
      <h3 style="margin:0 0 10px">Bölgeler (DB) — Liste &amp; Sayfalama</h3>

      <div class="row-inline" style="gap:8px; margin-bottom:10px">
        <input class="input" id="db-q" placeholder="Ada göre anında ara (ör. Zone_...)" />
        <select class="select" id="db-per" style="max-width:140px">
          <option value="5">5 / sayfa</option>
          <option value="10" selected>10 / sayfa</option>
          <option value="20">20 / sayfa</option>
          <option value="50">50 / sayfa</option>
        </select>
        <!-- Arama/Temizle butonlarını kaldırdık; input'ta anında -->
      </div>

      <div class="list" id="db-list" style="max-height:300px; overflow:auto"></div>

      <div style="display:flex; align-items:center; justify-content:space-between; margin-top:10px">
        <div id="db-stat" class="muted mono">—</div>
        <div id="db-pag" class="row-inline" style="gap:6px"></div>
      </div>
    </div>

    <!-- QUICK API CARD -->
    <div class="card">
      <h3 style="margin:0 0 8px">Hızlı API Test</h3>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn" id="btnPing">API Ping</button>
        <a class="btn" href="/api/v1/zones" target="_blank" rel="noopener">Zones JSON</a>
      </div>
      <div style="margin-top:14px">
        <div class="stat"><span class="dot"></span> <span id="statText" class="muted">Hazır beklemede…</span></div>
        <pre id="out" class="mono" style="white-space:pre-wrap;margin-top:10px;color:#cbd5e1"></pre>
      </div>
    </div>
  </div>

  <!-- Delete Confirm MODAL (HTML) -->
  <div id="exg-del-modal" class="exg-modal exg-hidden" role="dialog" aria-modal="true" aria-labelledby="exg-del-title">
    <div class="exg-backdrop"></div>
    <div class="exg-dialog" role="document">
      <div class="exg-header" id="exg-del-title">Bölgeyi silmek istediğine emin misin?</div>
      <div class="exg-body">
        <p>Bu işlem <span class="exg-accent">geri alınamaz</span>. Seçili bölge haritadan kaldırılacak.</p>
        <p class="exg-meta" id="exg-del-meta">—</p>
      </div>
      <div class="exg-actions">
        <button type="button" class="exg-btn exg-btn-ghost" id="exg-del-cancel">Vazgeç <span class="exg-kbd">Esc</span></button>
        <button type="button" class="exg-btn exg-btn-danger" id="exg-del-confirm">Evet, sil <span class="exg-kbd">Enter</span></button>
      </div>
    </div>
  </div>

  <!-- Leaflet & Turf -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
  <script src="https://unpkg.com/@turf/turf/turf.min.js"></script>

  <script>
/* =======================================================================================
 *  SECTION 0 — Yardımcılar & Sabitler
 * ======================================================================================= */
const $ = (s)=>document.querySelector(s);
const fmt = (n, p=6)=>Number(n).toFixed(p);
const rndId = ()=>'z_'+Math.random().toString(36).slice(2,9);
const safeParse = (v)=>{ try { return typeof v==='string' ? JSON.parse(v) : v; } catch(e){ return null; } };
const clone = (o)=>JSON.parse(JSON.stringify(o||{}));

function debounce(fn, wait=350){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), wait); }; }
function exgToast(msg){
  const el = document.createElement('div');
  el.className = 'exg-toast';
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(()=> el.remove(), 2200);
}

// Anlık aramalar için hız
const LIVE_DELAY = 250;

// Stil varsayılanı
const DEFAULT_STYLE = { stroke:'#5b9cff', fill:'#1e3a8a', weight:2, opacity:1, fillOpacity:0.25, dashArray:null };
const HOVER_BOOST = 1;
const SELECTED_BOOST = 1;

// Auto-save (sessiz) açık
const AUTO_SAVE = true;

/* Auto-save için per-zone debounce */
const _saveTimers = new Map();
function scheduleAutoSave(id){
  if(!AUTO_SAVE || !zones.has(id)) return;
  const timer = _saveTimers.get(id);
  if (timer) clearTimeout(timer);
  _saveTimers.set(id, setTimeout(()=>{ saveOne(id, true).then(()=>loadDbPage()); }, 500));
}

// 81 İl (ad, lat, lng, zoom)
const TR_CITIES = [
  ["Adana",37.000,35.321,11],["Adıyaman",37.764,38.276,11],["Afyonkarahisar",38.756,30.538,11],
  ["Ağrı",39.719,43.051,11],["Aksaray",38.372,34.025,11],["Amasya",40.653,35.833,11],
  ["Ankara",39.933,32.860,11],["Antalya",36.897,30.713,11],["Ardahan",41.110,42.702,11],
  ["Artvin",41.183,41.818,11],["Aydın",37.844,27.845,11],["Balıkesir",39.648,27.886,11],
  ["Bartın",41.636,32.339,11],["Batman",37.889,41.132,11],["Bayburt",40.255,40.224,11],
  ["Bilecik",40.141,29.979,11],["Bingöl",38.885,40.496,11],["Bitlis",38.401,42.108,11],
  ["Bolu",40.735,31.609,11],["Burdur",37.720,30.290,11],["Bursa",40.195,29.060,11],
  ["Çanakkale",40.147,26.405,11],["Çankırı",40.602,33.615,11],["Çorum",40.550,34.955,11],
  ["Denizli",37.776,29.086,11],["Diyarbakır",37.914,40.230,11],["Düzce",40.843,31.156,11],
  ["Edirne",41.677,26.555,11],["Elazığ",38.680,39.226,11],["Erzincan",39.750,39.500,11],
  ["Erzurum",39.904,41.267,11],["Eskişehir",39.766,30.526,11],["Gaziantep",37.066,37.383,11],
  ["Giresun",40.917,38.390,11],["Gümüşhane",40.460,39.481,11],["Hakkari",37.574,43.740,11],
  ["Hatay",36.206,36.157,11],["Iğdır",39.923,44.045,11],["Isparta",37.765,30.554,11],
  ["İstanbul",41.008,28.978,11],["İzmir",38.424,27.143,11],["Kahramanmaraş",37.575,36.937,11],
  ["Karabük",41.204,32.627,11],["Karaman",37.176,33.228,11],["Kars",40.603,43.097,11],
  ["Kastamonu",41.376,33.776,11],["Kayseri",38.731,35.478,11],["Kırıkkale",39.845,33.516,11],
  ["Kırklareli",41.735,27.225,11],["Kırşehir",39.145,34.163,11],["Kilis",36.716,37.114,11],
  ["Kocaeli",40.767,29.940,11],["Konya",37.871,32.484,11],["Kütahya",39.422,29.983,11],
  ["Malatya",38.355,38.309,11],["Manisa",38.619,27.428,11],["Mardin",37.312,40.733,11],
  ["Mersin",36.812,34.642,11],["Muğla",37.214,28.363,11],["Muş",38.734,41.491,11],
  ["Nevşehir",38.624,34.714,11],["Niğde",37.969,34.683,11],["Ordu",40.986,37.879,11],
  ["Osmaniye",37.074,36.247,11],["Rize",41.020,40.523,11],["Sakarya",40.781,30.403,11],
  ["Samsun",41.286,36.330,11],["Şanlıurfa",37.167,38.795,11],["Siirt",37.932,41.941,11],
  ["Sinop",42.026,35.153,11],["Sivas",39.747,37.018,11],["Şırnak",37.517,42.454,11],
  ["Tekirdağ",40.977,27.511,11],["Tokat",40.313,36.554,11],["Trabzon",41.005,39.716,11],
  ["Tunceli",39.107,39.548,11],["Uşak",38.682,29.408,11],["Van",38.501,43.400,11],
  ["Yalova",40.655,29.281,11],["Yozgat",39.820,34.804,11],["Zonguldak",41.451,31.793,11]
];

/* =======================================================================================
 *  SECTION 1 — Harita Kurulumu & Şehir Seçici
 * ======================================================================================= */
const map = L.map('map', { zoomControl: true }).setView([41.0082, 28.9784], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19, attribution:'© OpenStreetMap'}).addTo(map);

// Şehir Select doldur
const sel = $('#city-select');
TR_CITIES.sort((a,b)=>a[0].localeCompare(b[0],'tr')).forEach(c=>{
  const opt = document.createElement('option'); opt.value = c[0]; opt.textContent = c[0]; sel.appendChild(opt);
});
sel.value = "Antalya";
function goToSelectedCity(){
  const name = sel.value; if(!name) return;
  const c = TR_CITIES.find(x=>x[0]===name); if (!c) return;
  const [, lat, lng, z] = c;
  map.flyTo([lat, lng], z || 11, {duration: 0.8});
}
sel.addEventListener('change', goToSelectedCity);

/* =======================================================================================
 *  SECTION 2 — Draw Katmanı & Durum
 * ======================================================================================= */
const drawnItems = new L.FeatureGroup().addTo(map);
const drawControl = new L.Control.Draw({
  draw: { polyline:false, marker:false, circle:false, circlemarker:false },
  edit: { featureGroup: drawnItems, remove: false }
});
map.addControl(drawControl);

/** zones: Map<id, {id, dbId?, name, layer, geometry, metrics, fromDB?, tags?, style?}> */
const zones = new Map();
let selectedId = null;

/* Geometry → metrics */
function layerToGeometry(layer){ return layer.toGeoJSON().geometry; }
function getMetrics(geometry){
  const f = {type:'Feature', geometry, properties:{}};
  return {
    area: turf.area(f),
    centroid: turf.centroid(f).geometry.coordinates, // [lng,lat]
    bbox: turf.bbox(f)
  };
}

/* Stil uygula */
function applyZoneStyle(z, {selected=false, hovered=false}={}){
  const s = z.style || DEFAULT_STYLE;
  const weight = (s.weight||2) + (selected?SELECTED_BOOST:0) + (hovered?HOVER_BOOST:0);
  const style = {
    color: s.stroke || '#5b9cff',
    weight,
    opacity: s.opacity ?? 1,
    dashArray: s.dashArray || null,
    fillColor: s.fill || '#1e3a8a',
    fillOpacity: s.fillOpacity ?? 0.25
  };
  z.layer.setStyle(style);
  if (selected) try{ z.layer.bringToFront(); }catch(e){}
}

/* =======================================================================================
 *  SECTION 3 — Alan Listesi / Seçim / Özet UI
 * ======================================================================================= */
function refreshSidebar(){
  const list = $('#zone-list'); list.innerHTML = '';
  if (zones.size === 0) { list.innerHTML = '<div class="muted">Henüz alan yok. Soldan çiz veya DB’den yüklenmesini bekle.</div>'; renderSelection(null); setStylePanelEnabled(false); return; }
  for (const [id, z] of zones) {
    const row = document.createElement('div');
    row.className = 'row' + (id===selectedId ? ' selected' : '');
    row.dataset.id = id;
    const areaText = z.metrics?.area != null ? fmt(z.metrics.area,2)+' m²' : '—';
    const dbBadge = z.fromDB && z.dbId ? ` • DB#${z.dbId}` : '';
    row.innerHTML = `
      <div class="meta">
        <span class="name">${z.name}${dbBadge}</span>
        <span class="small">${areaText}</span>
      </div>
      <div class="actions">
        <button class="btn btn--small" data-act="focus">Odak</button>
        <button class="btn btn--small" data-act="rename">Ad</button>
        <button class="btn btn--small btn--danger" data-act="del" title="Delete tuşu ile de açılır">Sil</button>
      </div>
    `;
    row.addEventListener('click', (e)=>{
      const act = e.target?.dataset?.act;
      if (act==='focus'){ focusOn(id); e.stopPropagation(); return; }
      if (act==='rename'){ renameZone(id); e.stopPropagation(); return; }
      if (act==='del'){ requestDelete(id); e.stopPropagation(); return; }
      selectZone(id);
    });
    list.appendChild(row);
  }
  renderSelection(selectedId);
}

function renderSelection(id){
  if (!id || !zones.has(id)) {
    $('#m-area').textContent = '—';
    $('#m-centroid').textContent = '—';
    $('#m-bbox').textContent = '—';
    $('#m-geojson').textContent = '';
    setStylePanelEnabled(false);
    $('#style-out').textContent = '—';
    return;
  }
  const z = zones.get(id);
  const {area, centroid, bbox} = z.metrics;
  $('#m-area').textContent = fmt(area,2) + ' m²';
  $('#m-centroid').textContent = fmt(centroid[1],6)+', '+fmt(centroid[0],6);
  $('#m-bbox').textContent = bbox.map(n=>fmt(n,6)).join(', ');
  $('#m-geojson').textContent = JSON.stringify(z.geometry, null, 2);

  // Stil panelini doldur
  const s = z.style || DEFAULT_STYLE;
  $('#style-fill').value = toHex(s.fill || '#1e3a8a');
  $('#style-stroke').value = toHex(s.stroke || '#5b9cff');
  $('#style-fillop').value = s.fillOpacity ?? 0.25;
  $('#style-weight').value = s.weight ?? 2;
  $('#style-dash').value = s.dashArray || '';
  setStylePanelEnabled(true);
  $('#style-out').textContent = `fill:${s.fill}, stroke:${s.stroke}, w:${s.weight}, fop:${s.fillOpacity}, dash:${s.dashArray||'—'}`;
}

function setStylePanelEnabled(on){
  ['style-fill','style-stroke','style-fillop','style-weight','style-dash','style-reset','style-copy-all','style-random']
    .forEach(id=>{ const el = $('#'+id); if(!el) return; el.disabled = !on; });
}

function selectZone(id){
  selectedId = id;
  zones.forEach((z, k)=>{ applyZoneStyle(z, {selected: k===id}); });
  refreshSidebar();
}

function focusOn(id){
  const z = zones.get(id); if(!z) return;
  try { map.fitBounds(z.layer.getBounds(), {padding:[20,20]}); } catch(e){}
}

function renameZone(id){
  const z = zones.get(id); if(!z) return;
  const name = prompt('Yeni ad:', z.name) || z.name;
  z.name = name;
  refreshSidebar();
  scheduleAutoSave(id);
}

/* =======================================================================================
 *  SECTION 4 — Çizim Olayları & Düzenleme + Hover
 * ======================================================================================= */
function attachLayerInteractions(id, layer){
  let hovered = false;
  layer.on('mouseover', ()=>{ hovered = true; const z = zones.get(id); if(!z) return; if (id!==selectedId) applyZoneStyle(z,{hovered:true}); });
  layer.on('mouseout',  ()=>{ hovered = false; const z = zones.get(id); if(!z) return; applyZoneStyle(z,{selected:id===selectedId}); });
  layer.on('click', ()=>selectZone(id));
  layer.on('edit', ()=>{
    const g = layerToGeometry(layer);
    const m = getMetrics(g);
    const z = zones.get(id);
    z.geometry = g; z.metrics = m;
    if (selectedId===id) renderSelection(id);
    refreshSidebar();
    scheduleAutoSave(id);
  });
  try{ layer._path && layer._path.classList.add('zone-path'); }catch(e){}
}

map.on(L.Draw.Event.CREATED, e=>{
  const layer = e.layer;
  drawnItems.addLayer(layer);

  const id = rndId();
  const geometry = layerToGeometry(layer);
  const metrics = getMetrics(geometry);
  const style = clone(DEFAULT_STYLE);

  zones.set(id, { id, name:'Zone_'+id.slice(2).toUpperCase(), layer, geometry, metrics, fromDB:false, style });

  attachLayerInteractions(id, layer);
  selectZone(id);

  // Yeni çizim → otomatik kaydet (sessiz)
  scheduleAutoSave(id);
});

/* =======================================================================================
 *  SECTION 5 — Kaydet / Temizle / Fit
 * ======================================================================================= */
$('#btn-clear-all').addEventListener('click', ()=>{
  drawnItems.clearLayers(); zones.clear(); selectedId = null; refreshSidebar(); $('#save-out').textContent = '';
  loadDbPage(); // listeleri tazele
});
$('#btn-fit').addEventListener('click', ()=>{
  if (zones.size===0) return;
  let bounds = null;
  zones.forEach(z=>{ const b = z.layer.getBounds(); bounds = bounds ? bounds.extend(b) : b; });
  if (bounds) map.fitBounds(bounds, {padding:[20,20]});
});

$('#btn-save-selected').addEventListener('click', async ()=>{
  if (!selectedId || !zones.has(selectedId)) { $('#save-out').textContent = 'Seçili alan yok.'; return; }
  await saveOne(selectedId);
  await loadDbPage();
});
$('#btn-save-all').addEventListener('click', async ()=>{
  if (zones.size===0) { $('#save-out').textContent = 'Kaydedilecek alan yok.'; return; }
  $('#save-out').textContent = 'Kaydediliyor...';
  let ok=0, fail=0;
  for (const [id] of zones) {
    const res = await saveOne(id, true);
    if (res) ok++; else fail++;
  }
  $('#save-out').textContent = `Bitti → Başarılı: ${ok}, Hatalı: ${fail}`;
  await loadDbPage();
});

async function saveOne(id, silent=false){
  const z = zones.get(id); if(!z) return false;
  const body = {
    name: z.name,
    geometry: z.geometry,
    area_m2: z.metrics?.area ?? null,
    centroid: z.metrics?.centroid ?? null,
    bbox: z.metrics?.bbox ?? null,
    style_json: z.style ? JSON.stringify(z.style) : null
  };
  try{
    const r = await fetch('/api/v1/zones', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)});
    const j = await r.json().catch(()=>({}));
    if (!r.ok) { if (!silent) $('#save-out').textContent = 'Hata: ' + (j?.error || r.status) + (j?.message ? ' • '+j.message : ''); return false; }
    // başarı → UI'ya yansıt
    z.fromDB = true;
    if (j?.id) z.dbId = j.id;
    if (!silent) $('#save-out').textContent = 'Kaydedildi: id=' + (j.id ?? '?') + ' name=' + (j.name ?? body.name);
    refreshSidebar();
    return true;
  }catch(e){ if (!silent) $('#save-out').textContent = 'Hata: ' + String(e); return false; }
}

/* =======================================================================================
 *  SECTION 6 — DB’den Alanları Haritaya Önyükle
 * ======================================================================================= */
async function bootstrapFromDB(){
  try{
    const r = await fetch('/api/v1/zones?with=geometry');
    const j = await r.json();
    if (!r.ok) throw new Error(j?.error || 'load_error');

    let firstId = null, preferredId = null;

    (j.items || []).forEach(row=>{
      const geometry = safeParse(row.geometry_json);
      if (!geometry) return;

      const feature = { type:'Feature', geometry, properties:{} };
      const geo = L.geoJSON(feature);
      const layer = geo.getLayers()[0];
      if (!layer) return;

      drawnItems.addLayer(layer);

      const id = 'db_'+row.id;
      const metrics = getMetrics(geometry);
      const tags = safeParse(row.tags_json) || [];
      const style = safeParse(row.style_json) || clone(DEFAULT_STYLE);
      const isPreferred = Array.isArray(tags) && tags.includes('selected');

      zones.set(id, { id, dbId: row.id, name: row.name, layer, geometry, metrics, fromDB: true, tags, style });

      attachLayerInteractions(id, layer);
      applyZoneStyle(zones.get(id), {selected:isPreferred});

      firstId = firstId || id;
      if (isPreferred) preferredId = id;
    });

    refreshSidebar();
    if (zones.size) {
      let bounds=null; zones.forEach(z=>{ const b=z.layer.getBounds(); bounds=bounds?bounds.extend(b):b; });
      if (bounds) map.fitBounds(bounds,{padding:[20,20]});
    }
    if (preferredId) selectZone(preferredId); else if (firstId) selectZone(firstId);

  }catch(e){ console.error(e); $('#save-out').textContent = 'DB yüklenemedi: ' + String(e); }
}

/* =======================================================================================
 *  SECTION 7 — POI / Adres Arama (Google / OSM) & Kontrol
 * ======================================================================================= */
const poi = { selected: null, marker: null };

function renderPoiResults(items, provider){
  const el = $('#poi-results'); el.innerHTML = '';
  $('#poi-provider-info').textContent = provider ? '['+(provider.toUpperCase())+']' : '';
  if (!items.length) { el.innerHTML = '<div class="muted">Sonuç bulunamadı.</div>'; return; }

  items.slice(0, 10).forEach(item=>{
    const row = document.createElement('div');
    row.className = 'row';
    row.innerHTML = `
      <div class="meta">
        <span class="name">${item.name}</span>
        <span class="small">${item.address || ''}</span>
      </div>
      <div class="actions">
        <button class="btn btn--small" data-act="pick">Seç</button>
      </div>
    `;
    row.querySelector('[data-act="pick"]').addEventListener('click', ()=>{
      poi.selected = item;
      $('#poi-out').textContent = '"'+item.name+'" seçildi.';
      if (poi.marker) try { map.removeLayer(poi.marker); } catch(e){}
      poi.marker = L.marker([item.lat, item.lng]).addTo(map);
      map.flyTo([item.lat, item.lng], 15, {duration:0.6});

      const prov = (item.provider || provider || '').toLowerCase();
      const a = $('#poi-open');
      if (prov === 'osm') {
        a.href = `https://www.openstreetmap.org/?mlat=${item.lat}&mlon=${item.lng}#map=16/${item.lat}/${item.lng}`;
      } else {
        a.href = `https://www.google.com/maps/search/?api=1&query=${item.lat},${item.lng}${item.place_id ? '&query_place_id='+encodeURIComponent(item.place_id):''}`;
      }
      a.style.display = 'inline-block';

      // Seçim yapılınca otomatik içeride/dışarıda kontrol et
      const { lat, lng, name } = poi.selected;
      const res = checkPointInZones(lat, lng);
      if (res.inside.length) {
        const names = res.inside.map(id=>zones.get(id)?.name || id);
        selectZone(res.inside[0]);
        $('#poi-out').textContent = `✅ "${name}" alan(lar)ın İÇİNDE: ${names.join(', ')}`;
      } else {
        let m = Math.round(res.best.dist);
        let unit = 'm';
        if (m >= 1000) { m = (m/1000).toFixed(2); unit = 'km'; }
        $('#poi-out').textContent = `❌ "${name}" alanların DIŞINDA. En yakın alan: ${res.best.name} (~${m} ${unit})`;
      }
    });
    el.appendChild(row);
  });
}

async function searchPOI(){
  const q  = ($('#poi-q').value || '').trim();
  const pv = ($('#poi-provider').value || '').trim();
  if(!q){ $('#poi-results').innerHTML=''; $('#poi-out').textContent = ''; return; }
  $('#poi-out').textContent = 'Aranıyor…';
  try{
    const url = '/api/v1/places?q='+encodeURIComponent(q) + (pv ? '&provider='+encodeURIComponent(pv) : '');
    const r = await fetch(url);
    const j = await r.json();
    if(!r.ok){ $('#poi-out').textContent = 'Hata: ' + (j?.error || r.status); return; }
    renderPoiResults(j.items || [], j.provider || pv || '');
    $('#poi-out').textContent = (j.count || 0) + ' sonuç';
  }catch(e){ $('#poi-out').textContent = 'Hata: ' + String(e); }
}
// Yazarken ara
$('#poi-q').addEventListener('input', debounce(searchPOI, LIVE_DELAY));
$('#poi-provider').addEventListener('change', ()=> searchPOI());

function checkPointInZones(lat, lng){
  const pt = turf.point([lng, lat]);
  const inside = [];
  let best = { id:null, name:null, dist: Infinity };

  zones.forEach((z, id)=>{
    const poly = { type:'Feature', geometry:z.geometry, properties:{} };
    if (turf.booleanPointInPolygon(pt, poly)) {
      inside.push(id);
    } else {
      let dmin = Infinity;
      const g = z.geometry;
      const checkRing = (ring)=>{
        const ls = turf.lineString(ring);
        const snap = turf.nearestPointOnLine(ls, pt);
        const d = turf.distance(pt, snap, {units:'kilometers'}) * 1000;
        if (d < dmin) dmin = d;
      };
      if (g.type==='Polygon') (g.coordinates || []).forEach(checkRing);
      else if (g.type==='MultiPolygon') (g.coordinates || []).forEach(poly => (poly[0] ? checkRing(poly[0]) : null));
      if (dmin < best.dist) best = { id, name:z.name, dist:dmin };
    }
  });

  return { inside, best };
}

/* =======================================================================================
 *  SECTION 8 — DB Liste & Sayfalama (Tamamen AJAX + anlık arama)
 * ======================================================================================= */
const DBP = { page: 1, per: 10, q: '', last: 1, total: 0 };
const dbListEl = $('#db-list');
const dbPagEl  = $('#db-pag');
const dbStatEl = $('#db-stat');

function fmtDate(s){ return s ? s : '—'; }

function pageRange(cur, last, delta = 1) {
  const range = [];
  const left = Math.max(1, cur - delta);
  const right = Math.min(last, cur + delta);
  if (1 <= last) range.push(1);
  if (left > 2) range.push('…');
  for (let i = left; i <= right; i++) if (i !== 1 && i !== last) range.push(i);
  if (right < last - 1) range.push('…');
  if (last > 1) range.push(last);
  return [...new Set(range)];
}

async function loadDbPage() {
  const params = new URLSearchParams();
  params.set('page', DBP.page);
  params.set('limit', DBP.per);
  if ((DBP.q || '').trim()) params.set('q', DBP.q.trim());

  dbStatEl.textContent = 'Yükleniyor…';
  dbListEl.innerHTML = '';

  try {
    const r = await fetch('/api/v1/zones?' + params.toString());
    const j = await r.json();
    if (!r.ok) throw new Error(j?.error || r.status);

    const items = j.items || [];
    DBP.page = j.page || DBP.page;
    DBP.per  = j.per_page || DBP.per;
    DBP.last = j.last_page || 1;
    DBP.total= j.total || items.length;

    renderDbList(items);
    renderDbPager();
    dbStatEl.textContent = `Toplam: ${DBP.total} • Sayfa ${DBP.page}/${DBP.last} • ${DBP.per}/sayfa`;
  } catch (e) {
    dbStatEl.textContent = 'Hata: ' + String(e);
  }
}

function renderDbList(items) {
  if (!items.length) { dbListEl.innerHTML = '<div class="muted">Kayıt yok.</div>'; return; }
  const frag = document.createDocumentFragment();
  items.forEach(row => {
    const areaText = (row.area_m2 != null) ? fmt(row.area_m2, 2) + ' m²' : '—';
    const div = document.createElement('div');
    div.className = 'row';
    div.innerHTML = `
      <div class="meta">
        <span class="name">#${row.id} — ${row.name}</span>
        <span class="small">${areaText} • ${fmtDate(row.created_at)}</span>
      </div>
      <div class="actions">
        <button class="btn btn--small" data-act="show" data-id="${row.id}">Haritada Göster</button>
        <a class="btn btn--small" href="/api/v1/zones/${row.id}" target="_blank" rel="noopener">JSON</a>
        <button class="btn btn--small btn--danger" data-act="del-db" title="DB kaydını sil" data-id="${row.id}">Sil</button>
      </div>
    `;
    div.querySelector('[data-act="show"]').addEventListener('click', () => showOnMapFromDB(row.id));
    div.querySelector('[data-act="del-db"]').addEventListener('click', () => deleteByDbId(row.id));
    frag.appendChild(div);
  });
  dbListEl.innerHTML = '';
  dbListEl.appendChild(frag);
}

function renderDbPager() {
  dbPagEl.innerHTML = '';
  const btn = (label, disabled, onClick) => {
    const b = document.createElement('button');
    b.className = 'btn btn--small';
    if (disabled) b.setAttribute('disabled', 'disabled');
    b.textContent = label;
    if (!disabled) b.addEventListener('click', onClick);
    return b;
  };
  dbPagEl.appendChild(btn('« Önceki', DBP.page <= 1, () => { DBP.page--; loadDbPage(); }));
  pageRange(DBP.page, DBP.last, 1).forEach(p => {
    if (p === '…') {
      const span = document.createElement('span'); span.className = 'muted'; span.textContent = '…'; dbPagEl.appendChild(span);
    } else {
      const b = btn(String(p), false, () => { DBP.page = p; loadDbPage(); });
      if (p === DBP.page) b.className = 'btn btn--small btn--accent';
      dbPagEl.appendChild(b);
    }
  });
  dbPagEl.appendChild(btn('Sonraki »', DBP.page >= DBP.last, () => { DBP.page++; loadDbPage(); }));
}

async function showOnMapFromDB(id) {
  const key = 'db_' + id;
  if (zones.has(key)) { selectZone(key); focusOn(key); return; }
  try {
    const r = await fetch('/api/v1/zones/' + id);
    const j = await r.json();
    if (!r.ok) throw new Error(j?.error || r.status);

    const geometry = safeParse(j.geometry_json);
    if (!geometry) throw new Error('geometry_json yok');

    const feature = { type: 'Feature', geometry, properties: {} };
    const geo = L.geoJSON(feature);
    const layer = geo.getLayers()[0];
    if (!layer) throw new Error('geometry parse failed');

    drawnItems.addLayer(layer);
    const metrics = getMetrics(geometry);
    const style = safeParse(j.style_json) || clone(DEFAULT_STYLE);

    zones.set(key, { id: key, dbId: id, name: j.name, layer, geometry, metrics, fromDB: true, style });
    attachLayerInteractions(key, layer);
    selectZone(key);
    focusOn(key);
  } catch (e) {
    alert('Haritada gösterilemedi: ' + String(e));
  }
}

// Anlık arama + sayfa boyutu
const dbSearchNow = ()=>{ DBP.q = ($('#db-q').value || '').trim(); DBP.page = 1; loadDbPage(); };
$('#db-q').addEventListener('input', debounce(dbSearchNow, LIVE_DELAY));
$('#db-per').addEventListener('change', (e)=>{ DBP.per = parseInt(e.target.value,10) || 10; DBP.page = 1; loadDbPage(); });

/* =======================================================================================
 *  SECTION 9 — Hızlı API Test
 * ======================================================================================= */
$('#btnPing').addEventListener('click', ()=>{
  $('#statText').textContent = 'API ping atılıyor…';
  fetch('/api/v1/zones').then(r=>r.json()).then(j=>{
    $('#statText').textContent = 'API canlı görünüyor ✅';
    $('#out').textContent = JSON.stringify(j, null, 2);
  }).catch(e=>{
    $('#statText').textContent = 'API erişilemedi ❌';
    $('#out').textContent = String(e);
  });
});

/* =======================================================================================
 *  SECTION 10 — Stil Paneli (Canlı)
 * ======================================================================================= */
function toHex(c){ const ctx = document.createElement('canvas').getContext('2d'); ctx.fillStyle = c; return ctx.fillStyle; }
function readStyleFromUI(){
  return {
    fill: $('#style-fill').value,
    stroke: $('#style-stroke').value,
    fillOpacity: parseFloat($('#style-fillop').value || '0.25'),
    weight: parseInt($('#style-weight').value || '2',10),
    opacity: 1,
    dashArray: ($('#style-dash').value || '') || null
  };
}
function writeStyleToUI(s){
  $('#style-fill').value = toHex(s.fill || '#1e3a8a');
  $('#style-stroke').value = toHex(s.stroke || '#5b9cff');
  $('#style-fillop').value = s.fillOpacity ?? 0.25;
  $('#style-weight').value = s.weight ?? 2;
  $('#style-dash').value = s.dashArray || '';
}

function applyStyleLive(){
  if(!selectedId || !zones.has(selectedId)) return;
  const z = zones.get(selectedId);
  z.style = readStyleFromUI();
  applyZoneStyle(z,{selected:true});
  $('#style-out').textContent = 'Uygulandı.';
  refreshSidebar();
  scheduleAutoSave(selectedId);
}

// Canlı bağla
['style-fill','style-stroke','style-fillop','style-weight','style-dash'].forEach(id=>{
  const el = $('#'+id);
  if (!el) return;
  const h = id==='style-fillop' || id==='style-weight' ? 'input' : 'change';
  el.addEventListener(h, applyStyleLive);
});

$('#style-reset').addEventListener('click', ()=>{
  if(!selectedId || !zones.has(selectedId)) return;
  const z = zones.get(selectedId);
  z.style = clone(DEFAULT_STYLE);
  writeStyleToUI(z.style);
  applyZoneStyle(z,{selected:true});
  $('#style-out').textContent = 'Varsayılana döndü.';
  refreshSidebar();
  scheduleAutoSave(selectedId);
});

$('#style-copy-all').addEventListener('click', ()=>{
  if(!selectedId || !zones.has(selectedId)) return;
  const base = readStyleFromUI();
  zones.forEach((z, id)=>{
    z.style = clone(base);
    applyZoneStyle(z,{selected:id===selectedId});
  });
  $('#style-out').textContent = 'Bu stil tüm alanlara kopyalandı.';
  refreshSidebar();
  // toplu save çok agresif olmasın; tek tek sırala
  let i=0; zones.forEach((_, id)=> setTimeout(()=> scheduleAutoSave(id), 200*i++));
});

$('#style-random').addEventListener('click', ()=>{
  if(!selectedId || !zones.has(selectedId)) return;
  const rand = ()=>('#'+Math.floor(Math.random()*0xFFFFFF).toString(16).padStart(6,'0'));
  const s = { fill: rand(), stroke: rand(), fillOpacity: 0.25 + Math.random()*0.5, weight: 2 + Math.floor(Math.random()*4), opacity:1, dashArray: null };
  writeStyleToUI(s);
  applyStyleLive();
});

document.querySelectorAll('.swatch[data-preset]').forEach(el=>{
  el.addEventListener('click', ()=>{
    const p = el.dataset.preset;
    const presets = {
      teal:   { fill:'#0ea5e9', stroke:'#0f766e', fillOpacity:.20, weight:3, dashArray:null, opacity:1 },
      violet: { fill:'#22d3ee', stroke:'#6d28d9', fillOpacity:.22, weight:3, dashArray:'6 6', opacity:1 },
      sunset: { fill:'#ef4444', stroke:'#f59e0b', fillOpacity:.18, weight:4, dashArray:null, opacity:1 },
      lime:   { fill:'#84cc16', stroke:'#22c55e', fillOpacity:.20, weight:3, dashArray:'12 6 2 6', opacity:1 },
      steel:  { fill:'#334155', stroke:'#94a3b8', fillOpacity:.28, weight:2, dashArray:null, opacity:1 }
    };
    const s = presets[p] || clone(DEFAULT_STYLE);
    writeStyleToUI(s);
    applyStyleLive();
  });
});

/* =======================================================================================
 *  SECTION 11 — Bootstrap Akışı
 * ======================================================================================= */
function goToSelectedCityInitial(){ const c = TR_CITIES.find(x=>x[0]==="Antalya"); if (c){ map.setView([c[1],c[2]], c[3] || 11); } }
goToSelectedCityInitial();
bootstrapFromDB();
loadDbPage();

/* =======================================================================================
 *  SECTION 12 — Delete Confirm Modal (Delete tuşu / Sil butonu)
 * ======================================================================================= */
const DelModal = {
  el: $('#exg-del-modal'),
  meta: $('#exg-del-meta'),
  btnConfirm: $('#exg-del-confirm'),
  btnCancel: $('#exg-del-cancel'),
  open: false,
  targetId: null
};

function requestDelete(id){
  if (!id) id = selectedId;
  if (!id || !zones.has(id)) { exgToast('Seçili alan yok.'); return; }
  openDeleteModal(id);
}

function openDeleteModal(id){
  DelModal.targetId = id;
  const z = zones.get(id);
  const dbStr = (z?.fromDB && z?.dbId) ? ` • DB#${z.dbId}` : ' • (yerel)';
  DelModal.meta.textContent = `ID: ${id}${z?.name ? ' • Ad: '+z.name : ''}${dbStr}`;
  DelModal.el.classList.remove('exg-hidden');
  DelModal.open = true;
  setDelModalBusy(false);
  setTimeout(()=> DelModal.btnConfirm?.focus(), 0);
}

function closeDeleteModal(){
  DelModal.open = false;
  DelModal.el.classList.add('exg-hidden');
  DelModal.targetId = null;
}

async function confirmDeleteModal(){
  if (!DelModal.targetId) return closeDeleteModal();
  const id = DelModal.targetId;
  setDelModalBusy(true);
  closeDeleteModal();
  await deleteZone(id);
}

// Modal tıklamaları
DelModal.btnCancel.addEventListener('click', closeDeleteModal);
DelModal.btnConfirm.addEventListener('click', confirmDeleteModal);
DelModal.el.addEventListener('click', (e)=>{ if (e.target.classList.contains('exg-backdrop')) closeDeleteModal(); });

// Global klavye kısayolları
window.addEventListener('keydown', (e)=>{
  const tag = (e.target.tagName||'').toLowerCase();
  const editable = e.target.isContentEditable || ['input','textarea','select'].includes(tag);
  if (editable) return; // form odaklıyken tetikleme

  if (e.key === 'Delete' && selectedId && !DelModal.open) {
    e.preventDefault();
    requestDelete(selectedId);
  } else if ((e.key === 'Enter' || e.key === 'NumpadEnter') && DelModal.open) {
    e.preventDefault();
    confirmDeleteModal();
  } else if (e.key === 'Escape' && DelModal.open) {
    e.preventDefault();
    closeDeleteModal();
  }
});

/* =======================================================================================
 *  SECTION 13 — DELETE entegrasyonu (API + UI) + 405 fallback
 * ======================================================================================= */

// Küçük yardımcılar
function setDelModalBusy(busy){
  if (!DelModal?.btnConfirm || !DelModal?.btnCancel) return;
  DelModal.btnConfirm.disabled = !!busy;
  DelModal.btnCancel.disabled  = !!busy;
}

function removeZoneFromUI(id){
  try {
    const z = zones.get(id);
    if (z?.layer && map && map.removeLayer) {
      map.removeLayer(z.layer);
    }
  } catch(_) {}
  zones.delete(id);
  if (selectedId === id) selectedId = null;
  refreshSidebar();
}

// Tekil silme (optimistik + geri alma + method-override fallback)
async function deleteZone(id){
  const z = zones.get(id);
  if (!z) { exgToast('Alan bulunamadı.'); return false; }

  // Optimistik: önce UI’dan kaldır, hata olursa geri al
  const snapshot = { id, z: clone({ ...z, layer: undefined }) };
  removeZoneFromUI(id);

  // DB’de yoksa sadece yerelden silmiş olduk
  if (!z.fromDB || !z.dbId) {
    exgToast(`Silindi: #${id} (yerel)`);
    return true;
  }

  const url = `/api/v1/zones/${encodeURIComponent(z.dbId)}`;

  setDelModalBusy(true);
  try {
    let res = await fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json' } });
    let data = await res.json().catch(()=> ({}));

    // 405/501 gibi durumlarda method override fallback
    if (!res.ok && (res.status===405 || res.status===501)) {
      const ovUrl = `${url}?_method=DELETE`;
      res = await fetch(ovUrl, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type':'application/json' }, body: JSON.stringify({_method:'DELETE'}) });
      data = await res.json().catch(()=> ({}));
    }

    if (!res.ok || data?.ok !== true) {
      // geri al: geometri mevcut, layer'ı yeniden çiz
      restoreZoneFromSnapshot(id, snapshot);
      exgToast(`Silinemedi (#${z.dbId}) — ${data?.error || res.status}`);
      return false;
    }

    exgToast(`Silindi: DB#${z.dbId} • UI#${id}`);
    // DB listesi ve sayfayı tazele
    await loadDbPage();
    return true;
  } catch (e) {
    restoreZoneFromSnapshot(id, snapshot);
    exgToast(`Ağ hatası — silinemedi (#${z.dbId})`);
    return false;
  } finally {
    setDelModalBusy(false);
  }
}

function restoreZoneFromSnapshot(id, snap){
  try{
    const feature = { type:'Feature', geometry: snap.z.geometry, properties:{} };
    const geo = L.geoJSON(feature);
    const layer = geo.getLayers()[0];
    if (layer) {
      drawnItems.addLayer(layer);
      zones.set(id, { ...snap.z, id, layer });
      attachLayerInteractions(id, layer);
      applyZoneStyle(zones.get(id), {selected:false});
      refreshSidebar();
    }
  }catch(_){}
}

// DB’den sil (listeden)
async function deleteByDbId(dbId){
  // Haritada varsa önce UI'dan da kaldır
  const key = 'db_'+dbId;
  if (zones.has(key)) removeZoneFromUI(key);

  const url = `/api/v1/zones/${encodeURIComponent(dbId)}`;
  try{
    let res = await fetch(url, { method:'DELETE', headers:{ 'Accept':'application/json' }});
    let j = await res.json().catch(()=> ({}));

    if (!res.ok && (res.status===405 || res.status===501)) {
      const ovUrl = `${url}?_method=DELETE`;
      res = await fetch(ovUrl, { method:'POST', headers:{ 'Accept':'application/json','Content-Type':'application/json' }, body: JSON.stringify({_method:'DELETE'}) });
      j = await res.json().catch(()=> ({}));
    }

    if (!res.ok || j?.ok !== true){
      exgToast(`Silinemedi: DB#${dbId} — ${j?.error || res.status}`);
      return;
    }
    exgToast(`Silindi: DB#${dbId}`);
    await loadDbPage();
  }catch(e){
    exgToast(`Ağ hatası: DB#${dbId}`);
  }
}

/* =======================================================================================
 *  SECTION 14 — Yardımcı: Stil paneli başlat
 * ======================================================================================= */
function initStylePanelLive(){
  // seçili alan değiştiğinde UI’lar güncellenecek; renderSelection bunu yapıyor.
}
initStylePanelLive();
  </script>
</body>
</html>
