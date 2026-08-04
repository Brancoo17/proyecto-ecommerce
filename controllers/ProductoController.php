<?php

namespace Controllers;

use MVC\Router;
use Model\Producto;
use Model\ProductoTalle;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as Image;

class ProductoController {
    public static function crear(Router $router) {

        $producto = new Producto;

        // Arrgeglo con mensaje de errores
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear una nueva instancia de Producto
            $producto->sincronizar($_POST['producto'] ?? []);

            // Generar un nombre único para la imagen
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            // Setear la imagen
            // Realiza un resize a la imagen con intervention
            $imagen = null;
            if($_FILES['producto']['tmp_name']['imagen']) {
                $manager = new Image(Driver::class);
                $imagen = $manager->decode($_FILES['producto']['tmp_name']['imagen'])->cover(800, 600);
                $producto->setImagen($nombreImagen);
            }

            // Revisar que no haya errores
            $alertas = $producto->validarProducto();

            // Subir la imagen
            if(empty($alertas)) {
                if($imagen) {
                    // Crear la carpeta si no existe
                    if(!is_dir(CARPETA_IMAGENES)) {
                        mkdir(CARPETA_IMAGENES);
                    }
                    $imagen->save(CARPETA_IMAGENES . $nombreImagen);
                }

                // Guardar el producto en la base de datos
                $resultado = $producto->guardar();

                // Guardar el stock
                $productoId = $resultado['id'];

                foreach($_POST['stock'] as $talleId => $stock) {

                    if($stock <= 0) {
                        continue;
                    }

                    $productoTalle = new ProductoTalle([
                        'producto_id' => $productoId,
                        'talle_id' => $talleId,
                        'stock' => $stock
                    ]);

                    $productoTalle->guardar();
                }

                if($resultado) {
                    header('Location: /admin?resultado=1');
                }
            }
        }

        $router->render('/admin/crear', [
            'producto' => $producto,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router) {
        
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

        if(!$id) {
            header('Location: /admin');
            return;
        }

        $producto = Producto::find($id);

        if(!$producto) {
            header('Location: /admin');
            return;
        }

        $alertas = [];

        // Obtener los talles existentes para el producto
        $productoTalles = ProductoTalle::whereAll('producto_id', $id);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincronizar el producto con los datos enviados
            $producto->sincronizar($_POST['producto'] ?? []);

            // Generar un nombre único para la imagen si se sube una nueva
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
            $imagen = null;

            if($_FILES['producto']['tmp_name']['imagen']) {
                $manager = new Image(Driver::class);
                $imagen = $manager->decode($_FILES['producto']['tmp_name']['imagen'])->cover(800, 600);
                $producto->setImagen($nombreImagen);
            }

            // Validar
            $alertas = $producto->validarProducto();

            if(empty($alertas)) {
                // Guardar la nueva imagen si se subió
                if($imagen) {
                    if(!is_dir(CARPETA_IMAGENES)) {
                        mkdir(CARPETA_IMAGENES);
                    }
                    $imagen->save(CARPETA_IMAGENES . $nombreImagen);
                }

                // Guardar el producto (actualizar en base de datos)
                $resultado = $producto->guardar();

                if($resultado) {
                    // Eliminar los talles anteriores para evitar duplicados o stock huérfano
                    $tallesAnteriores = ProductoTalle::whereAll('producto_id', $id);
                    foreach($tallesAnteriores as $talleAnterior) {
                        $talleAnterior->eliminar();
                    }

                    // Guardar el nuevo stock
                    foreach($_POST['stock'] as $talleId => $stock) {
                        if($stock === '' || $stock < 0) {
                            continue;
                        }

                        $productoTalle = new ProductoTalle([
                            'producto_id' => $id,
                            'talle_id' => $talleId,
                            'stock' => $stock
                        ]);

                        $productoTalle->guardar();
                    }

                    header('Location: /admin?resultado=2');
                    return;
                }
            }
        }

        $router->render('/admin/actualizar', [
            'producto' => $producto,
            'alertas' => $alertas,
            'productoTalles' => $productoTalles
        ]);
    }

    public static function eliminar() {
        
        // Eliminar Producto
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validar el ID
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if($id) {
                $producto = Producto::find($id);
                $resultado = $producto->eliminar();

                if($resultado) {
                    header('Location: /admin?resultado=3');
                }
            }
        }
    }
}