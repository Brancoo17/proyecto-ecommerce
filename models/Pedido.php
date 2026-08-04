<?php

namespace Model;

class Pedido extends ActiveRecord {

    protected static string $tabla = 'pedidos';

    protected static array $columnasDB = [
        'id',
        'nombre',
        'telefono',
        'total',
        'estado',
        'observaciones',
        'created_at',
        'updated_at'
    ];

    public ?int $id;
    public string $nombre;
    public string $telefono;
    public float $total;
    public string $estado;
    public ?string $observaciones;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->total = $args['total'] ?? 0;
        $this->estado = $args['estado'] ?? 'Pendiente';
        $this->observaciones = $args['observaciones'] ?? null;
        $this->created_at = $args['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $args['updated_at'] ?? date('Y-m-d H:i:s');
    }
}