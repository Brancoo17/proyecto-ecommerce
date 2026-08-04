<?php

namespace Controllers;

use Model\Pedido;
use Model\PedidoProducto;
use Model\ProductoTalle; // Importamos el modelo de stock/talles

class PedidoController {

    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (ob_get_length()) ob_clean();

            // 1. Leer el JSON recibido
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if ($data) {
                // 2. Crear y guardar el Pedido principal
                $pedido = new Pedido([
                    'nombre' => $data['nombre'] ?? '',
                    'telefono' => $data['telefono'] ?? '',
                    'total' => $data['total'] ?? 0,
                    'estado' => 'Pendiente',
                    'observaciones' => $data['observaciones'] ?? null
                ]);

                $resultado = $pedido->guardar();

                if ($resultado['resultado']) {
                    $pedidoId = $resultado['id'];

                    // 3. Guardar los productos del pedido y descontar el stock
                    if (isset($data['productos']) && is_array($data['productos'])) {
                        foreach ($data['productos'] as $item) {
                            $productoId = (int) ($item['id'] ?? 0);
                            $talleId = (int) ($item['talleId'] ?? $item['talle_id'] ?? 6);
                            $cantidad = (int) ($item['cantidad'] ?? 1);
                            $precio = (float) ($item['precio'] ?? 0);

                            // Guardar la línea de producto del pedido
                            $pedidoProducto = new PedidoProducto([
                                'pedido_id' => $pedidoId,
                                'producto_id' => $productoId,
                                'talle_id' => $talleId,
                                'cantidad' => $cantidad,
                                'precio_unitario' => $precio
                            ]);
                            $pedidoProducto->guardar();

                            // DESCONTAR STOCK: Buscar el registro en 'producto_talles' y restar la cantidad
                            $productoStock = ProductoTalle::findStock($productoId, $talleId);
                            if ($productoStock) {
                                $nuevoStock = max(0, $productoStock->stock - $cantidad);
                                $productoStock->stock = $nuevoStock;
                                $productoStock->guardar(); // Actualiza la BD
                            }
                        }
                    }

                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true, 'id' => $pedidoId]);
                    exit;
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['ok' => false]);
            exit;
        }
    }

    // Actualiza el estado de un pedido en la base de datos vía API y ajusta el stock si se cancela
    public static function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            while (ob_get_level()) {
                ob_end_clean();
            }

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
            $nuevoEstado = trim($data['estado'] ?? '');

            if ($id && $nuevoEstado) {
                $pedido = Pedido::find($id);
                if ($pedido) {
                    // 1. Guardar el estado anterior antes de actualizarlo
                    $estadoAnterior = $pedido->estado;

                    // 2. Actualizar el pedido con el nuevo estado
                    $pedido->estado = $nuevoEstado;
                    $pedido->updated_at = date('Y-m-d H:i:s');
                    $resultado = $pedido->guardar();

                    // 3. Si el estado cambió A 'Cancelado' (y antes NO estaba cancelado): Reponer Stock
                    if ($nuevoEstado === 'Cancelado' && $estadoAnterior !== 'Cancelado') {
                        $items = PedidoProducto::whereAll('pedido_id', $pedido->id);
                        foreach ($items as $item) {
                            $productoStock = ProductoTalle::findStock($item->producto_id, $item->talle_id);
                            if ($productoStock) {
                                $productoStock->stock = $productoStock->stock + $item->cantidad;
                                $productoStock->guardar(); // Guarda el stock sumado en la BD
                            }
                        }
                    } 
                    // (Opcional): Si un pedido ESTABA 'Cancelado' y se vuelve a reactivar: Restar Stock nuevamente
                    else if ($estadoAnterior === 'Cancelado' && $nuevoEstado !== 'Cancelado') {
                        $items = PedidoProducto::whereAll('pedido_id', $pedido->id);
                        foreach ($items as $item) {
                            $productoStock = ProductoTalle::findStock($item->producto_id, $item->talle_id);
                            if ($productoStock) {
                                $productoStock->stock = max(0, $productoStock->stock - $item->cantidad);
                                $productoStock->guardar(); // Resta el stock nuevamente en la BD
                            }
                        }
                    }

                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true, 'estado' => $nuevoEstado]);
                    exit;
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['ok' => false]);
            exit;
        }
    }

}
