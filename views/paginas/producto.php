<?php

    /**
     * @var Model\Producto $producto
     * @var Model\ProductoTalle[] $productoTalles
     */

    if(!isset($_SESSION)) {
        session_start();
    }
    $auth = $_SESSION['admin'] ?? false;

    // Mapa de categorías
    $categorias = [
        1 => 'Tops',
        2 => 'Bottoms',
        3 => 'Conjuntos',
        4 => 'Otros'
    ];
    $categoriaNombre = $categorias[$producto->categoria_id] ?? 'Sin categoría';

    // Mapa de talles
    $tallesNombres = [
        1 => 'S',
        2 => 'M',
        3 => 'L',
        4 => 'XL',
        6 => 'Único'
    ];

    // Preparar stock por talle
    $stockMap = [];
    if(isset($productoTalles) && is_array($productoTalles)) {
        foreach($productoTalles as $pTalle) {
            $stockMap[$pTalle->talle_id] = $pTalle->stock;
        }
    }

    // Determinar si tiene talles o no
    $tieneTalles = !empty($stockMap) && !isset($stockMap[6]);
    $stockTotal = array_sum(array_values($stockMap));
?>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="/"><h1><i class="fas fa-dumbbell"></i> Riada <span>Indumentaria</span></h1></a>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="/"><i class="fas fa-home"></i>Inicio</a></li>
                    <li><a href="https://www.instagram.com/riada_indumentaria/" target="_blank"><i class="fab fa-instagram"></i>Instagram</a></li>
                    <?php if($auth): ?>
                        <li><a href="/admin"><i class="fas fa-user-lock"></i>Admin</a></li>
                        <li><a href="/logout" class="boton boton-rojo"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="/admin/login"><i class="fas fa-user-lock"></i>Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="header-actions">
                <div class="cart-icon" onclick="toggleCart()">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Breadcrumb -->
<nav class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="/"><i class="fas fa-home"></i> Inicio</a></li>
            <li><span><?php echo $categoriaNombre; ?></span></li>
            <li><span><?php echo $producto->nombre; ?></span></li>
        </ul>
    </div>
</nav>

<!-- Detalle del Producto -->
<section class="detalle-producto" 
         data-id="<?php echo $producto->id; ?>"
         data-nombre="<?php echo s($producto->nombre); ?>"
         data-precio="<?php echo $producto->precio; ?>"
         data-imagen="<?php echo $producto->imagen; ?>"
         data-categoria="<?php echo $producto->categoria_id; ?>"
         data-stock='<?php echo json_encode($stockMap ?? []); ?>'>
    <div class="container">

        <!-- Botón de Volver -->
        <div class="volver alinear-izquierda">
            <a href="/" class="boton boton-primario">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="detalle-grid">

            <!-- Imagen del producto -->
            <div class="detalle-imagen">
                <div class="imagen-principal">
                    <img src="/imagenes/<?php echo $producto->imagen; ?>" alt="<?php echo $producto->nombre; ?>" id="imagenPrincipal">
                </div>
            </div>

            <!-- Información del producto -->
            <div class="detalle-info">

                <span class="detalle-categoria"><?php echo $categoriaNombre; ?></span>
                <h2 class="detalle-nombre"><?php echo $producto->nombre; ?></h2>
                <p class="detalle-precio">$<?php echo number_format($producto->precio, 0, ',', '.'); ?></p>

                <div class="detalle-disponibilidad">
                    <?php if($stockTotal > 0): ?>
                        <span class="disponible"><i class="fas fa-check-circle"></i> En stock</span>
                    <?php else: ?>
                        <span class="no-disponible"><i class="fas fa-times-circle"></i> Sin stock</span>
                    <?php endif; ?>
                </div>

                <?php if($tieneTalles): ?>
                <!-- Selector de talles -->
                <div class="detalle-talles">
                    <p class="detalle-label">Talle:</p>
                    <div class="talles-grid">
                        <?php foreach([1 => 'S', 2 => 'M', 3 => 'L', 4 => 'XL'] as $talleId => $talleNombre): ?>
                            <?php $stock = $stockMap[$talleId] ?? 0; ?>
                            <button 
                                type="button" 
                                class="talle-btn <?php echo $stock <= 0 ? 'agotado' : ''; ?>"
                                data-talle="<?php echo $talleId; ?>"
                                <?php echo $stock <= 0 ? 'disabled' : ''; ?>
                            >
                                <?php echo $talleNombre; ?>
                                <?php if($stock <= 0): ?>
                                    <span class="talle-agotado-text">Agotado</span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Acciones -->
                <div class="detalle-acciones">
                    <div class="cantidad-selector">
                        <button type="button" class="cantidad-btn" id="restarCantidad"><i class="fas fa-minus"></i></button>
                        <input type="number" value="1" min="1" max="10" id="cantidadProducto" class="cantidad-input" readonly>
                        <button type="button" class="cantidad-btn" id="sumarCantidad"><i class="fas fa-plus"></i></button>
                    </div>
                    <button class="boton boton-primario boton-agregar btn-agregar-carrito" <?php echo $stockTotal <= 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart"></i> Agregar al carrito
                    </button>
                </div>

                <!-- Detalles adicionales -->
                <div class="detalle-extras">
                    <div class="extra-item">
                        <i class="fas fa-truck"></i>
                        <div>
                            <strong>Envío gratis</strong>
                            <p>En compras mayores a $50.000</p>
                        </div>
                    </div>
                    <div class="extra-item">
                        <i class="fas fa-undo"></i>
                        <div>
                            <strong>Devolución gratuita</strong>
                            <p>Hasta 30 días después de la compra</p>
                        </div>
                    </div>
                    <div class="extra-item">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Compra segura</strong>
                            <p>Protegemos tus datos</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; 2026 Riada Indumentaria. Todos los derechos reservados.</p>
    </div>
</footer>
