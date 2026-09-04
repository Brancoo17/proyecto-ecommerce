<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    $auth = $_SESSION['admin'] ?? false;
    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>

<!-- Barra Superior Promocional -->
<div class="top-announcement-bar">
    <div class="container announcement-content">
        <span><i class="fas fa-bolt"></i> Envíos en Salta - Capital | Atención personalizada por WhatsApp</span>
        <div class="top-social-links">
            <a href="https://www.instagram.com/riada_indumentaria/" target="_blank" title="Seguinos en Instagram">
                <i class="fab fa-instagram"></i> @riada_indumentaria
            </a>
        </div>
    </div>
</div>

<!-- Header Principal Sticky -->
<header class="header header-sticky">
    <div class="container">
        <div class="header-content">
            <!-- Logo de la Marca -->
            <div class="logo">
                <a href="/">
                    <div class="logo-icon-wrap">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h1>Riada <span>Indumentaria</span></h1>
                </a>
            </div>

            <!-- Navegación Principal -->
            <nav class="nav" id="mainNav">
                <ul>
                    <li>
                        <a href="/" class="<?php echo ($currentUri === '/' || $currentUri === '') ? 'activo' : ''; ?>">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li>
                        <a href="/productos" class="<?php echo (strpos($currentUri, '/productos') !== false) ? 'activo' : ''; ?>">
                            <i class="fas fa-layer-group"></i> Catálogo
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/riada_indumentaria/" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
                    </li>
                    <?php if($auth): ?>
                        <li>
                            <a href="/admin" class="<?php echo (strpos($currentUri, '/admin') !== false) ? 'activo' : ''; ?>">
                                <i class="fas fa-user-shield"></i> Admin
                            </a>
                        </li>
                        <li>
                            <a href="/logout" class="boton-logout-header">
                                <i class="fas fa-sign-out-alt"></i> Salir
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/admin/login" class="nav-admin-link <?php echo (strpos($currentUri, '/admin/login') !== false) ? 'activo' : ''; ?>">
                                <i class="fas fa-user-lock"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Acciones del Header (Carrito & Menú Móvil) -->
            <div class="header-actions">
                <div class="cart-icon" onclick="toggleCart()" title="Ver carrito">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </div>
                <button type="button" class="mobile-toggle-btn" id="mobileToggleBtn" aria-label="Abrir menú" onclick="toggleMenuMobile()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</header>
