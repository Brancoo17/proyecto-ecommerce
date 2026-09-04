<?php

// Definir la ruta de la carpeta de imágenes (compatible con localhost y hosting)
if(!defined('CARPETA_IMAGENES')) {
    if(is_dir(__DIR__ . '/../public/imagenes/')) {
        define('CARPETA_IMAGENES', __DIR__ . '/../public/imagenes/');
    } elseif(isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'] . '/imagenes/')) {
        define('CARPETA_IMAGENES', $_SERVER['DOCUMENT_ROOT'] . '/imagenes/');
    } else {
        define('CARPETA_IMAGENES', __DIR__ . '/../public/imagenes/');
    }
}

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Muestra los mensajes de alerta
function obtenerMensaje($codigo) {
    $mensaje = '';

    switch($codigo) {
        case 1:
            $mensaje = "Creado Correctamente";
            break;
        case 2:
            $mensaje = "Actualizado Correctamente";
            break;
        case 3:
            $mensaje = "Eliminado Correctamente";
            break;
        default:
            $mensaje = false;
            break;
    }

    return $mensaje;
}