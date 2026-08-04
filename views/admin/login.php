<main class="contenedor contenido-centrado centrado-vertical">
    <div class="alinear-izquierda w-100">
        <a href="/" class="boton boton-primario"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <h1 class="nombre-pagina">Iniciar Sesión</h1>
    <p class="descripcion-pagina">Inicia Sesión como Administrador</p>

    <?php
        include_once __DIR__ . "/../templates/alertas.php";
    ?>

    <form method="POST" class="formulario-login">

        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" id="email" placeholder="Tu Email" name="email">
        </div>

        <div class="campo">
            <label for="password">Password:</label>
            <input type="password" id="password" placeholder="Tu Password" name="password">
        </div>

        <input type="submit" class="boton boton-primario" value="Iniciar Sesión">
    </form>
</main>