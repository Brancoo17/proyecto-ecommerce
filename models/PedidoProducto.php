<?php

namespace Model;

class PedidoProducto extends ActiveRecord {

    protected static string $tabla = 'pedido_productos';

    protected static array $columnasDB = [
        'id',
        'pedido_id',
        'producto_id',
        'talle_id',
        'cantidad',
        'precio_unitario'
    ];

    public ?int $id;
    public int $pedido_id;
    public int $producto_id;
    public int $talle_id;
    public int $cantidad;
    public float $precio_unitario;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->pedido_id = $args['pedido_id'] ?? 0;
        $this->producto_id = $args['producto_id'] ?? 0;
        $this->talle_id = $args['talle_id'] ?? 0;
        $this->cantidad = $args['cantidad'] ?? 0;
        $this->precio_unitario = $args['precio_unitario'] ?? 0;
    }
}