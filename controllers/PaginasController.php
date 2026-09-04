<?php

namespace Controllers;

use MVC\Router;
use Model\Producto;
use Model\ProductoTalle;
use Model\Pedido;


class PaginasController {
    public static function index(Router $router) {

        $todosProductos = Producto::all();
        // Mostrar los productos marcados como destacados por el administrador
        $productosDestacados = Producto::whereAll('destacado', 1);
        if(empty($productosDestacados)) {
            $productosDestacados = array_slice($todosProductos, 0, 6);
        }

        $productoTalles = ProductoTalle::all();
        $stockMap = [];
        foreach($productoTalles as $pt) {
            $stockMap[$pt->producto_id][$pt->talle_id] = intval($pt->stock);
        }

        $router->render('paginas/index', [
            'productos' => $productosDestacados,
            'totalProductos' => count($todosProductos),
            'stockMap' => $stockMap
        ]);
    }

    public static function productos(Router $router) {
        $productos = Producto::all();
        $productoTalles = ProductoTalle::all();
        $stockMap = [];
        foreach($productoTalles as $pt) {
            $stockMap[$pt->producto_id][$pt->talle_id] = intval($pt->stock);
        }

        $categoriaActiva = $_GET['categoria'] ?? 'todos';

        $router->render('paginas/productos', [
            'productos' => $productos,
            'stockMap' => $stockMap,
            'categoriaActiva' => $categoriaActiva
        ]);
    }

    public static function producto(Router $router) {

        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

        if(!$id) {
            header('Location: /');
            return;
        }

        $producto = Producto::find($id);

        if(!$producto) {
            header('Location: /');
            return;
        }

        // Obtener los talles/stock del producto
        $productoTalles = ProductoTalle::whereAll('producto_id', $id);

        $router->render('paginas/producto', [
            'producto' => $producto,
            'productoTalles' => $productoTalles
        ]);
    }

    public static function checkout(Router $router) {
        if(!isset($_SESSION)) {
            session_start();
        }

        $carrito = $_SESSION['carrito'] ?? [];

        $total = 0;
        foreach($carrito as $item) {
            $precio = floatval($item['precio'] ?? 0);
            $cantidad = intval($item['cantidad'] ?? 1);
            $total += $precio * $cantidad;
        }

        $router->render('paginas/checkout', [
            'carrito' => $carrito,
            'total' => $total
        ]);
    }

    public static function admin(Router $router) {

        if(!isset($_SESSION)) session_start();

        if (!$_SESSION['admin']) {
            header('Location: /');
            return;
        }

        // Obtener los productos y pedidos
        $productos = Producto::all();
        $pedidos = Pedido::all();

        // Muestra mensaje condicional
        $resultado = $_GET['resultado'] ?? null;

        $router->render('admin/index', [
            'productos' => $productos,
            'pedidos' => $pedidos,
            'resultado' => $resultado
        ]);
    }

}