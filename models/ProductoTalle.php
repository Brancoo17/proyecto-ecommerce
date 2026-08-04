<?php

namespace Model;

class ProductoTalle extends ActiveRecord {

    protected static string $tabla = 'producto_talles';

    protected static array $columnasDB = [
        'id',
        'producto_id',
        'talle_id',
        'stock'
    ];

    public ?int $id;
    public ?int $producto_id;
    public ?int $talle_id;
    public int $stock;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->producto_id = $args['producto_id'] ?? null;
        $this->talle_id = $args['talle_id'] ?? null;
        $this->stock = $args['stock'] ?? 0;
    }

    public function validar() {

        if(!$this->stock) {
            self::$alertas['error'][] = "El stock es obligatorio.";
        }

        return self::$alertas;
    }

    public static function findStock(int $productoId, int $talleId) {
        $query = "SELECT * FROM " . static::$tabla .
                " WHERE producto_id = {$productoId}
                AND talle_id = {$talleId}
                LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }

    public static function findAllByProducto(int $productoId) {
        $query = "SELECT * FROM " . static::$tabla .
                " WHERE producto_id = {$productoId}";

        return self::consultarSQL($query);
    }
}