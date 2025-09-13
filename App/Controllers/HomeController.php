<?php
namespace App\Controllers;

final class HomeController
{
    public function index(): string
    {
        $title = 'ExeGeo • Ana Sayfa';
        $page  = 'home';

        // View'e aktarılacak veriler:
        $data = [
            'project' => 'ExeGeo',
            'tagline' => 'Küçük proje, net mimari. Haritadan koordinat, API’den doğrulama.',
            'endpoints' => [
                ['method' => 'GET',  'path' => '/api/v1/zones',       'desc' => 'Bölgeleri listeler'],
                ['method' => 'GET',  'path' => '/api/v1/zones/{id}',  'desc' => 'Tekil bölge'],
                ['method' => 'POST', 'path' => '/api/v1/zones',       'desc' => 'Yeni bölge oluşturur'],
            ],
        ];

        // Basit render: view dosyasını include edip çıktı tamponundan döndür.
        return $this->render(__DIR__ . '/../Views/home/index.php', compact('title','page','data'));
    }

    private function render(string $viewPath, array $vars = []): string
    {
        if (!is_file($viewPath)) {
            http_response_code(500);
            return 'View bulunamadı: ' . htmlspecialchars($viewPath);
        }
        extract($vars, EXTR_SKIP);
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}
