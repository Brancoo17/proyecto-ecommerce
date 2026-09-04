<?php

namespace Model;

class Producto extends ActiveRecord {

    protected static string $tabla = 'productos';
    protected static array $columnasDB = ['id', 'categoria_id', 'nombre', 'precio', 'imagen', 'destacado'];

    public ?int $id;
    public ?int $categoria_id;
    public string $nombre;
    public string $precio;
    public string $imagen;
    public ?int $destacado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->categoria_id = $args['categoria_id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->destacado = isset($args['destacado']) ? intval($args['destacado']) : 0;
    }

    public function validarProducto() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this->precio) {
            self::$alertas['error'][] = 'El precio es obligatorio';
        }
        if(!$this->imagen) {
            self::$alertas['error'][] = 'La imagen es obligatoria';
        }
        if(!$this->categoria_id) {
            self::$alertas['error'][] = 'La categoría es obligatoria';
        }
        return self::$alertas;
    }

    public function setImagen(string $imagen): void {

        // Elimina la imagen previa
        if(!is_null($this->id)) {
            $this->eliminarImagen();
        }

        // Asignar al atributo de imagen el nombre de la imagen
        if($imagen) {
            $this->imagen = $imagen;
        }
    }

    // Elimina el archivo de la imagen
    public function eliminarImagen() {
        // Comprobar si existe la imagen
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if($existeArchivo) {
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }
}