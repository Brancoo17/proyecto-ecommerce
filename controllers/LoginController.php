<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;

class LoginController {

    public static function login(Router $router) {

        $alertas = [];

        $auth = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    // Verificar el password
                    $verificado = $usuario->comprobarPassword($auth->password);
                    
                    if($verificado) {
                        // Autenticar al usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar al usuario
                        if($usuario->rol === 'admin') {
                            $_SESSION['admin'] = $usuario->rol ?? null;
                            header('Location: /admin');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                    $auth->email = '';
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('admin/login', [
            'alertas' => $alertas,
            'auth' => $auth
        ]);
    }

    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

}