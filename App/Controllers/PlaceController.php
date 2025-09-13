<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Config\Config;

final class PlaceController
{
    // GET /api/v1/places?q=relax%20hotel[&provider=google|osm]
    public function search(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $q = trim($_GET['q'] ?? '');
        if ($q === '') {
            http_response_code(400);
            echo json_encode(['error'=>'q_required']);
            return;
        }

        // İsteğe bağlı override: ?provider=google|osm
        $provider = strtolower($_GET['provider'] ?? '');
        if ($provider !== 'google' && $provider !== 'osm') {
            // otomatik seçim: key varsa google, yoksa osm
            $provider = (Config::GOOGLE_MAPS_API_KEY !== '') ? 'google' : 'osm';
        }

        try {
            if ($provider === 'google') {
                $out = $this->googleTextSearch($q);
            } else {
                $out = $this->nominatimSearch($q);
            }
            echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            http_response_code(502);
            echo json_encode(['error'=>'upstream_failure','message'=>$e->getMessage()]);
        }
    }

    // --- Google: Text Search (New) ---
    private function googleTextSearch(string $q): array
    {
        $key = Config::GOOGLE_MAPS_API_KEY;
        if ($key === '') {
            throw new \RuntimeException('google_key_missing');
        }

        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json'
             . '?query=' . rawurlencode($q)
             . '&language=tr&region=tr'
             . '&key=' . $key;

        $json = $this->httpGet($url);
        $data = json_decode($json, true);
        if (!is_array($data)) throw new \RuntimeException('google_bad_json');

        $status = $data['status'] ?? 'UNKNOWN';
        if (!in_array($status, ['OK','ZERO_RESULTS'], true)) {
            $msg = $data['error_message'] ?? $status;
            throw new \RuntimeException('google_status: ' . $msg);
        }

        $items = array_map(function(array $r){
            return [
                'name'     => $r['name'] ?? '',
                'address'  => $r['formatted_address'] ?? ($r['vicinity'] ?? ''),
                'lat'      => $r['geometry']['location']['lat'] ?? null,
                'lng'      => $r['geometry']['location']['lng'] ?? null,
                'place_id' => $r['place_id'] ?? null,
                'rating'   => $r['rating'] ?? null,
                'types'    => $r['types'] ?? [],
                'provider' => 'google',
            ];
        }, $data['results'] ?? []);

        return ['items'=>$items, 'count'=>count($items), 'provider'=>'google'];
    }

    // --- OSM/Nominatim: keysiz ---
    private function nominatimSearch(string $q): array
    {
        // TR odaklı, Türkçe; format=jsonv2; güvenli limit 10
        $url = 'https://nominatim.openstreetmap.org/search'
             . '?q=' . rawurlencode($q)
             . '&format=jsonv2&addressdetails=1&limit=10&accept-language=tr&countrycodes=tr';

        $json = $this->httpGet($url, [
            'User-Agent: ' . Config::APP_CONTACT,
            'Accept: application/json',
        ]);
        $arr = json_decode($json, true);
        if (!is_array($arr)) throw new \RuntimeException('nominatim_bad_json');

        // Nominatim alanları -> frontend ile uyumlu hale getir
        $items = array_map(function(array $r){
            // isim + açıklama
            $display = $r['display_name'] ?? '';
            $name    = $r['namedetails']['name'] ?? ($r['name'] ?? '');
            if ($name === '' && $display !== '') {
                // display_name'i parçala (en soldaki kısa parça adı gibi iş görür)
                $parts = explode(',', $display);
                $name  = trim($parts[0] ?? $display);
            }

            // place_id alanı: OSM kimliğinden üretelim
            $pid = ($r['osm_type'] ?? '') . ':' . ($r['osm_id'] ?? '');

            return [
                'name'     => $name,
                'address'  => $display,
                'lat'      => isset($r['lat']) ? (float)$r['lat'] : null,
                'lng'      => isset($r['lon']) ? (float)$r['lon'] : null,
                'place_id' => $pid,
                'rating'   => null,
                'types'    => array_filter([$r['class'] ?? null, $r['type'] ?? null]),
                'provider' => 'osm',
            ];
        }, $arr);

        return ['items'=>$items, 'count'=>count($items), 'provider'=>'osm'];
    }

    private function httpGet(string $url, array $headers = []): string
{
    $devSkip = \App\Config\Config::DEV_INSECURE_SSL ?? false;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // 🔓 LOCAL GELİŞTİRME: SSL doğrulamasını kapat
        if ($devSkip) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $resp = curl_exec($ch);
        if ($resp === false) { $err = curl_error($ch); $no = curl_errno($ch); curl_close($ch); throw new \RuntimeException('cURL: '.($err?:'unknown').' #'.$no); }
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($status >= 400) throw new \RuntimeException('HTTP '.$status.' from upstream');
        return $resp;
    }

    // stream fallback
    $opts = [
        'http' => [
            'method'  => 'GET',
            'timeout' => 12,
            'header'  => $headers ? implode("\r\n", $headers) : '',
        ],
        'ssl' => [
            'verify_peer'      => !$devSkip,
            'verify_peer_name' => !$devSkip,
        ],
    ];
    $ctx = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $ctx);
    if ($resp === false) { $e = error_get_last(); throw new \RuntimeException('stream_get: '.($e['message'] ?? 'unknown')); }
    return $resp;
}

}
