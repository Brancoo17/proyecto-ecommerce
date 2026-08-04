<?php

namespace Model;

class Usuario extends ActiveRecord {

    protected static string $tabla = 'usuarios';
    protected static array $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'rol', 'creado'];

    public ?int $id;
    public string $nombre;
    public string $apellido;
    public string $email;
    public string $password;
    public string $rol;
    public string $creado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->rol = $args['rol'] ?? '';
        $this->creado = $args['creado'] ?? '';
    }

    // Validar Login
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);
        
        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya está registrado';
        }

        return $resultado;
    }

    // Comprobar password
    public function comprobarPassword(string $password) : bool {
        
        $resultado = password_verify($password, $this->password);

        if(!$resultado) {
            self::$alertas['error'][] = 'El password es incorrecto';
            return false;
        }

        return true;
    }
}