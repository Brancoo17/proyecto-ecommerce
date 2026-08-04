<?php

    /**
     * @var  Model\Producto[] $productos
    */

    if(!isset($_SESSION)) {
        session_start();
    }
    $auth = $_SESSION['admin'] ?? false; 
?>

<!-- Header -->
<header class="header header-sticky">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="/"><h1><i class="fas fa-dumbbell"></i> Riada <span>Indumentaria</span></h1></a>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="/"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="https://www.instagram.com/riada_indumentaria/" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
                    <?php if($auth): ?>
                        <li><a href="/admin"><i class="fas fa-user-lock"></i> Admin</a></li>
                        <li><a href="/logout" class="boton boton-rojo"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="/admin/login"><i class="fas fa-user-lock"></i> Admin</a></li>
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

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h2>Encontrá tu estilo deportivo</h2>
            <p>Ropa deportiva de las mejores marcas para todos tus entrenamientos</p>
        </div>
    </div>
</section>

<!-- Product Section -->
<section class="productos">
    <div class="container">
        
        <h2>Productos</h2>
        <p>Descubrí nuestra selección de productos</p>

        <div class="categorias">
            <button type="button" class="boton boton-secundario" onclick="filtrarProductos('todos')">Todos</button>
            <button type="button" class="boton boton-secundario" onclick="filtrarProductos(1)">Tops</button>
            <button type="button" class="boton boton-secundario" onclick="filtrarProductos(2)">Bottoms</button>
            <button type="button" class="boton boton-secundario" onclick="filtrarProductos(3)">Conjuntos</button>
            <button type="button" class="boton boton-secundario" onclick="filtrarProductos(4)">Otros</button>
        </div>

        <div class="grid-productos">
            <?php foreach($productos as $producto): ?>

            <div class="producto" 
                 data-id="<?php echo $producto->id; ?>"
                 data-nombre="<?php echo s($producto->nombre); ?>"
                 data-precio="<?php echo $producto->precio; ?>"
                 data-imagen="<?php echo $producto->imagen; ?>"
                 data-categoria="<?php echo $producto->categoria_id; ?>"
                 data-stock='<?php echo json_encode($stockMap[$producto->id] ?? []); ?>'>
                <a href="/producto?id=<?php echo $producto->id; ?>" class="producto-enlace"></a>

                <div class="producto-imagen">
                    <img src="/imagenes/<?php echo $producto->imagen; ?>" alt="<?php echo $producto->nombre; ?>">
                </div>

                <div class="producto-info">
                    <h3><?php echo $producto->nombre; ?></h3>
                    <p class="producto-precio">$<?php echo number_format($producto->precio, 0, ',', '.'); ?></p>
                    <?php if($producto->categoria_id !== 4): ?>
                    <select name="talle" id="talle-<?php echo $producto->id; ?>">
                        <option value="" disabled selected>-- Seleccione un talle --</option>
                        <option value="1">S</option>
                        <option value="2">M</option>
                        <option value="3">L</option>
                        <option value="4">XL</option>
                    </select>
                    <?php endif; ?>
                </div>

                <div class="acciones-producto">
                    <button class="boton boton-primario btn-agregar-carrito"><i class="fas fa-shopping-cart"></i> Agregar al carrito</button>               
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; 2026 Riada Indumentaria. Todos los derechos reservados.</p>
    </div>
</footer>