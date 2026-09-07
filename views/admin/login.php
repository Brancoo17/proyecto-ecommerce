<main class="login-pagina">
    <div class="login-wrapper">
        <div class="login-volver-contenedor">
            <a href="/" class="login-volver">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Volver a la tienda</span>
            </a>
        </div>

        <div class="login-card">
            <div class="login-header">
                <div class="login-icon-box">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1 class="login-titulo">Panel de Control</h1>
                <p class="login-subtitulo">Inicia sesión con tus credenciales de administrador</p>
            </div>

            <?php
                include_once __DIR__ . "/../templates/alertas.php";
            ?>

            <form method="POST" class="formulario-login-moderno" action="/admin/login" novalidate>
                <div class="login-grupo">
                    <label for="email" class="login-label">Correo Electrónico</label>
                    <div class="login-input-contenedor">
                        <i class="fa-regular fa-envelope login-icono-campo"></i>
                        <input 
                            type="email" 
                            id="email" 
                            class="login-input" 
                            placeholder="admin@riada.com" 
                            name="email"
                            value="<?php echo s($auth->email ?? ''); ?>"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="login-grupo">
                    <label for="password" class="login-label">Contraseña</label>
                    <div class="login-input-contenedor">
                        <i class="fa-solid fa-lock login-icono-campo"></i>
                        <input 
                            type="password" 
                            id="password" 
                            class="login-input login-input-password" 
                            placeholder="••••••••" 
                            name="password"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            id="toggle-password" 
                            class="boton-toggle-password" 
                            aria-label="Mostrar contraseña" 
                            title="Mostrar contraseña"
                        >
                            <i class="fa-regular fa-eye" id="icono-ojo"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-btn-submit">
                    <span>Iniciar Sesión</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

            <div class="login-footer">
                <p>
                    <i class="fa-solid fa-lock"></i>
                    <span>Acceso restringido exclusivamente a administradores</span>
                </p>
            </div>
        </div>
    </div>
</main>