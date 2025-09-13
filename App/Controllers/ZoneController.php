<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use PDO;
use Throwable;

final class ZoneController
{
    private PDO $pdo;

    public function __construct()
    {
        // PDO bağlantısı
        $this->pdo = Database::getConnection();
    }

    /**
     * POST /api/v1/zones
     * Body:
     * {
     *   "name": "Zone_1",
     *   "geometry": { ...GeoJSON Geometry... },
     *   "bbox": [minX,minY,maxX,maxY],
     *   "centroid": [lng,lat],
     *   "area_m2": 123.45,
     *   "tags": ["tag1","tag2"],
     *   "meta": { "any": "thing" }
     * }
     */
    public function store(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $in = json_decode(file_get_contents('php://input'), true) ?? [];

        // Zorunlu alan: geometry
        $geometry = $in['geometry'] ?? null;
        if (!$geometry || !is_array($geometry)) {
            http_response_code(400);
            echo json_encode(['error' => 'geometry required']);
            return;
        }

        $name     = isset($in['name']) ? trim((string)$in['name']) : ('Zone_'.substr(bin2hex(random_bytes(4)),0,8));
        $bbox     = $in['bbox']      ?? null;   // [minX,minY,maxX,maxY]
        $centroid = $in['centroid']  ?? null;   // [lng,lat]
        $area     = isset($in['area_m2']) ? (float)$in['area_m2'] : null;
        $tags     = $in['tags']      ?? null;   // array|string|null
        $meta     = $in['meta']      ?? null;   // array|object|null

        // DB için JSON string'leri hazırla
        $geometryJson = json_encode($geometry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $bboxJson     = $bbox     ? json_encode($bbox)     : null;
        $centroidJson = $centroid ? json_encode($centroid) : null;
        $tagsJson     = $tags     ? json_encode($tags, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        $metaJson     = $meta     ? json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

        try {
            $sql = "
                INSERT INTO zones
                  (name, geometry_json, geometry_geom, bbox_json, centroid_json, area_m2, tags_json, meta_json)
                VALUES
                  (:name, :geometry_json, ST_GeomFromGeoJSON(:geometry_json), :bbox_json, :centroid_json, :area_m2, :tags_json, :meta_json)
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name'          => $name,
                ':geometry_json' => $geometryJson,
                ':bbox_json'     => $bboxJson,
                ':centroid_json' => $centroidJson,
                ':area_m2'       => $area,
                ':tags_json'     => $tagsJson,
                ':meta_json'     => $metaJson,
            ]);

            $id = (int)$this->pdo->lastInsertId();

            // Trigger'lar: geometry_geom SRID=4326 + bbox_geom auto
            echo json_encode([
                'ok'   => true,
                'id'   => $id,
                'name' => $name
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
        }
    }

public function index(): void
{
    header('Content-Type: application/json; charset=utf-8');

    $withGeo = isset($_GET['with']) && $_GET['with'] === 'geometry';

    if ($withGeo) {
        $sql = "SELECT id, name, geometry_json, bbox_json, centroid_json, area_m2, tags_json, created_at, updated_at
                FROM zones
                ORDER BY id DESC";
    } else {
        $sql = "SELECT id, name, area_m2, created_at, updated_at
                FROM zones
                ORDER BY id DESC";
    }

    $rows = $this->pdo->query($sql)->fetchAll();

    echo json_encode(
        ['items' => $rows, 'count' => count($rows)],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
}

    /**
     * GET /api/v1/zones/{id}
     */
    public function show(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $stmt = $this->pdo->prepare("
            SELECT id, name, geometry_json, bbox_json, centroid_json, area_m2, tags_json, meta_json, created_at, updated_at
            FROM zones
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'not_found']);
            return;
        }

        echo json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
        /**
     * DELETE /api/v1/zones/{id}
     */
    public function destroy(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // ID doğrulama
        $zoneId = (int)$id;
        if ($zoneId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'invalid_id']);
            return;
        }

        try {
            // Var mı kontrolü
            $check = $this->pdo->prepare("SELECT 1 FROM zones WHERE id = :id");
            $check->execute([':id' => $zoneId]);
            if (!$check->fetchColumn()) {
                http_response_code(404);
                echo json_encode(['error' => 'not_found']);
                return;
            }

            // Sil
            $del = $this->pdo->prepare("DELETE FROM zones WHERE id = :id");
            $del->execute([':id' => $zoneId]);

            echo json_encode(['ok' => true, 'deleted_id' => $zoneId], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * DELETE /api/v1/zones?ids=1,2,3
     * Virgül ile ayrılmış ID listesi. Geçersiz ID’ler yoksayılır.
     */
    public function destroyBulk(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $idsParam = $_GET['ids'] ?? '';
        if (!$idsParam) {
            http_response_code(400);
            echo json_encode(['error' => 'ids_required']);
            return;
        }

        // Rakamları ayıkla ve benzersizleştir
        $ids = array_values(array_unique(array_filter(array_map(
            static fn($v) => (int)trim($v),
            explode(',', (string)$idsParam)
        ), static fn($v) => $v > 0)));

        if (empty($ids)) {
            http_response_code(400);
            echo json_encode(['error' => 'invalid_ids']);
            return;
        }

        try {
            // Bulunanların sayısını öğren
            $inPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $existsStmt = $this->pdo->prepare("SELECT id FROM zones WHERE id IN ($inPlaceholders)");
            $existsStmt->execute($ids);
            $existing = $existsStmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($existing)) {
                http_response_code(404);
                echo json_encode(['error' => 'not_found']);
                return;
            }

            // Sil
            $delStmt = $this->pdo->prepare("DELETE FROM zones WHERE id IN ($inPlaceholders)");
            $delStmt->execute($ids);

            echo json_encode([
                'ok' => true,
                'requested' => $ids,
                'deleted'   => array_map('intval', $existing),
                'skipped'   => array_values(array_diff($ids, array_map('intval', $existing))),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
        }
    }

}
