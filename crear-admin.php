<?php

require 'vendor/autoload.php';

// Conectar a la BD (ajustá con tus datos)
include 'includes/database.php';

$nombre   = 'Admin';
$apellido = 'Branco';
$email    = 'admin@empresa.com';
$password = password_hash('admin1234', PASSWORD_BCRYPT);
$rol      = 'admin';

$query = "INSERT INTO usuarios (nombre, apellido, email, password, rol) 
          VALUES ('{$nombre}', '{$apellido}', '{$email}', '{$password}', '{$rol}')";

$db->query($query);

echo "Administrador creado correctamente.";