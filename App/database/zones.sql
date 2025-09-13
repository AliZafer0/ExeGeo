-- =========================================================
-- ExeGeo • Zones şeması (MariaDB 12.x, utf8mb4, SRID 4326)
-- Tek dosya, idempotent. Çalıştır: mysql -u root -p < zones.sql
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- (İstersen veritabanı oluştur)
-- CREATE DATABASE IF NOT EXISTS `general` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `general`;

START TRANSACTION;

-- Önce eski tetikleyicileri ve tabloyu güvenle düşürelim
DROP TRIGGER IF EXISTS `trg_zones_bi`;
DROP TRIGGER IF EXISTS `trg_zones_bu`;
DROP TABLE  IF EXISTS `zones`;

-- ---------------------------------------------------------
-- T A B L O
-- ---------------------------------------------------------
CREATE TABLE `zones` (
  `id`             BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(190)        NOT NULL,
  `geometry_json`  LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
                    CHECK (JSON_VALID(`geometry_json`)),
  `geometry_geom`  GEOMETRY SRID 4326  NOT NULL,
  `bbox_json`      LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
                    CHECK (JSON_VALID(`bbox_json`)),
  `centroid_json`  LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
                    CHECK (JSON_VALID(`centroid_json`)),
  `area_m2`        DOUBLE DEFAULT NULL,
  `tags_json`      LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
                    CHECK (JSON_VALID(`tags_json`)),
  `meta_json`      LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
                    CHECK (JSON_VALID(`meta_json`)),
  `bbox_geom`      POLYGON SRID 4326   NOT NULL,
  `created_at`     TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- Tip ve SRID güvenliği
  CHECK (GeometryType(`geometry_geom`) IN ('POLYGON','MULTIPOLYGON')),
  CHECK (ST_SRID(`geometry_geom`) = 4326),
  CHECK (GeometryType(`bbox_geom`) = 'POLYGON'),
  CHECK (ST_SRID(`bbox_geom`) = 4326),
  PRIMARY KEY (`id`),
  KEY `idx_zones_name` (`name`),
  SPATIAL KEY `sp_geom` (`geometry_geom`),
  SPATIAL KEY `sp_bbox` (`bbox_geom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- T E T İ K L E Y İ C İ L E R
--  geometry_json -> geometry_geom + bbox/centroid json alanlarını türet
-- ---------------------------------------------------------
DELIMITER $$
CREATE TRIGGER `trg_zones_bi`
BEFORE INSERT ON `zones`
FOR EACH ROW
BEGIN
  -- geometry_json varsa buradan üret; yoksa verilen geometry_geom'u 4326'ya sabitle
  IF NEW.geometry_json IS NOT NULL THEN
    SET NEW.geometry_geom = ST_SRID(ST_GeomFromGeoJSON(NEW.geometry_json), 4326);
  ELSE
    SET NEW.geometry_geom = ST_SRID(ST_GeomFromText(ST_AsText(NEW.geometry_geom)), 4326);
  END IF;

  SET NEW.bbox_geom     = ST_Envelope(NEW.geometry_geom);
  SET NEW.bbox_json     = ST_AsGeoJSON(NEW.bbox_geom);
  SET NEW.centroid_json = ST_AsGeoJSON(ST_Centroid(NEW.geometry_geom));
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_zones_bu`
BEFORE UPDATE ON `zones`
FOR EACH ROW
BEGIN
  IF NEW.geometry_json IS NOT NULL THEN
    SET NEW.geometry_geom = ST_SRID(ST_GeomFromGeoJSON(NEW.geometry_json), 4326);
  ELSE
    SET NEW.geometry_geom = ST_SRID(ST_GeomFromText(ST_AsText(NEW.geometry_geom)), 4326);
  END IF;

  SET NEW.bbox_geom     = ST_Envelope(NEW.geometry_geom);
  SET NEW.bbox_json     = ST_AsGeoJSON(NEW.bbox_geom);
  SET NEW.centroid_json = ST_AsGeoJSON(ST_Centroid(NEW.geometry_geom));
END$$
DELIMITER ;

-- ---------------------------------------------------------
-- T E M İ Z   Ö R N E K   V E R İ (kopyasız, WKB yok)
--  Not: area_m2 istersen uygulama tarafında/turf ile doldur.
-- ---------------------------------------------------------
INSERT INTO `zones` (`name`, `geometry_json`, `area_m2`, `tags_json`, `meta_json`)
VALUES
('Havalimanı',
 '{"type":"Polygon","coordinates":[[[30.786124,36.923689],[30.776677,36.882646],[30.818073,36.874956],[30.825459,36.915592],[30.786124,36.923689]]]}',
 16956791.020557, NULL, NULL),
('Fener',
 '{"type":"Polygon","coordinates":[[[30.76212,36.852295],[30.758518,36.845976],[30.792657,36.85202],[30.790941,36.858064],[30.762807,36.870287],[30.748739,36.850921],[30.76212,36.852295]]]}',
 5601601.1211534, NULL, NULL);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
