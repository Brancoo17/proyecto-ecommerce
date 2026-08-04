<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="/"><h1><i class="fas fa-dumbbell"></i> Riada <span>Indumentaria</span></h1></a>
            </div>
        </div>
    </div>
</header>

<main class="contenedor">

    <div class="alinear-izquierda w-100">
        <a href="/admin" class="boton boton-secundario"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <h1 class="nombre-pagina">Actualizar Producto</h1>
    <p class="descripcion-pagina">Actualiza los datos del producto</p>

    <?php
        include_once __DIR__ . "/../templates/alertas.php";
    ?>

    <form class="formulario" method="POST" enctype="multipart/form-data">
        <?php include __DIR__ . '/formulario.php'; ?>

        <input type="submit" value="Actualizar Producto" class="boton boton-secundario">
    </form>
    

</main>