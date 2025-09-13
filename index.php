<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ZoneController;
use App\Controllers\PlaceController;

// Router başlat
$router = new Router();

/** WEB ROUTES */
$router->get('/', [HomeController::class, 'index']);

/** API ROUTES */
$router->get('/api/v1/zones', [ZoneController::class, 'index']);
$router->get('/api/v1/zones/{id}', [ZoneController::class, 'show']);
$router->post('/api/v1/zones', [ZoneController::class, 'store']);

// Silme (tekil & çoklu)
$router->delete('/api/v1/zones/{id}', [ZoneController::class, 'destroy']);
$router->delete('/api/v1/zones', [ZoneController::class, 'destroyBulk']);

$router->get('/api/v1/places', [PlaceController::class, 'search']);


// Çalıştır
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
