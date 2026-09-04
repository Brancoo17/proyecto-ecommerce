<?php
    include_once __DIR__ . '/../templates/header.php';
?>

<main class="contenedor">

    <div class="alinear-izquierda w-100">
        <a href="/admin" class="boton boton-secundario"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <h1 class="nombre-pagina">Crear Producto</h1>
    <p class="descripcion-pagina">Ingresa los datos del nuevo producto</p>

    <?php
        include_once __DIR__ . "/../templates/alertas.php";
    ?>

    <form class="formulario" method="POST" enctype="multipart/form-data">
        <?php include __DIR__ . '/formulario.php'; ?>

        <input type="submit" value="Crear Producto" class="boton boton-secundario">
    </form>
    

</main>