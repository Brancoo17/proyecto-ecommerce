<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\PaginasController;
use Controllers\LoginController;
use Controllers\ProductoController;
use Controllers\PedidoController;


$router = new Router();

$router->get('/', [PaginasController::class, 'index']);
$router->get('/productos', [PaginasController::class, 'productos']);
$router->get('/producto', [PaginasController::class, 'producto']);
$router->get('/checkout', [PaginasController::class, 'checkout']);

// Area Admin
$router->get('/admin', [PaginasController::class, 'admin']);
$router->get('/admin/login', [LoginController::class, 'login']);
$router->post('/admin/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// API Pedidos
$router->post('/api/pedidos', [PedidoController::class, 'crear']);
$router->post('/api/pedidos/estado', [PedidoController::class, 'cambiarEstado']);
$router->post('/api/productos/destacado', [ProductoController::class, 'cambiarDestacado']);


// CRUD
$router->get('/admin/crear', [ProductoController::class, 'crear']);
$router->post('/admin/crear', [ProductoController::class, 'crear']);
$router->get('/admin/actualizar', [ProductoController::class, 'actualizar']);
$router->post('/admin/actualizar', [ProductoController::class, 'actualizar']);
$router->post('/admin/eliminar', [ProductoController::class, 'eliminar']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();