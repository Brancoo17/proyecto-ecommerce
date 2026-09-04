<?php

    /**
     * @var Model\Producto $producto
     * @var Model\ProductoTalle[] $productoTalles
     */

    if(!isset($_SESSION)) {
        session_start();
    }
    $auth = $_SESSION['admin'] ?? false;

    // Mapa de categorías actualizado
    $categorias = [
        1 => 'Tops',
        2 => 'Pantalones, Calzas y Shorts',
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

    include_once __DIR__ . '/../templates/header.php';
?>

<!-- Breadcrumb Moderno -->
<nav class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="/"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="/productos?categoria=<?php echo $producto->categoria_id; ?>"><?php echo $categoriaNombre; ?></a></li>
            <li><span><?php echo s($producto->nombre); ?></span></li>
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

        <!-- Botón de Navegación -->
        <div class="volver alinear-izquierda mb-2">
            <a href="/productos" class="boton boton-secundario btn-volver-catalogo">
                <i class="fas fa-arrow-left"></i> Volver al Catálogo
            </a>
        </div>

        <div class="card-detalle-wrapper">
            <div class="detalle-grid">

                <!-- Imagen del producto -->
                <div class="detalle-imagen">
                    <div class="imagen-principal">
                        <?php if($stockTotal <= 0 && !empty($stockMap)): ?>
                            <span class="badge-stock badge-agotado">Agotado</span>
                        <?php endif; ?>
                        <img src="/imagenes/<?php echo $producto->imagen; ?>" alt="<?php echo s($producto->nombre); ?>" id="imagenPrincipal">
                    </div>
                </div>

                <!-- Información del producto -->
                <div class="detalle-info">

                    <a href="/productos?categoria=<?php echo $producto->categoria_id; ?>" class="detalle-categoria">
                        <i class="fas fa-tag"></i> <?php echo $categoriaNombre; ?>
                    </a>

                    <h1 class="detalle-nombre"><?php echo s($producto->nombre); ?></h1>
                    
                    <p class="detalle-precio">$<?php echo number_format($producto->precio, 0, ',', '.'); ?></p>

                    <div class="detalle-disponibilidad">
                        <?php if($stockTotal > 0 || empty($stockMap)): ?>
                            <span class="disponible"><i class="fas fa-check-circle"></i> En stock para entrega</span>
                        <?php else: ?>
                            <span class="no-disponible"><i class="fas fa-times-circle"></i> Sin stock disponible</span>
                        <?php endif; ?>
                    </div>

                    <?php if($tieneTalles): ?>
                    <!-- Selector de talles -->
                    <div class="detalle-talles">
                        <p class="detalle-label"><i class="fas fa-ruler"></i> Seleccioná tu Talle:</p>
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
                            <button type="button" class="cantidad-btn" id="restarCantidad" aria-label="Restar cantidad"><i class="fas fa-minus"></i></button>
                            <input type="number" value="1" min="1" max="10" id="cantidadProducto" class="cantidad-input" readonly>
                            <button type="button" class="cantidad-btn" id="sumarCantidad" aria-label="Sumar cantidad"><i class="fas fa-plus"></i></button>
                        </div>
                        <button class="boton boton-primario boton-agregar btn-agregar-carrito" <?php echo ($stockTotal <= 0 && !empty($stockMap)) ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-bag"></i> 
                            <?php echo ($stockTotal <= 0 && !empty($stockMap)) ? 'Sin stock' : 'Agregar al carrito'; ?>
                        </button>
                    </div>

                    <!-- Detalles adicionales de confianza -->
                    <div class="detalle-extras">
                        <div class="extra-item">
                            <i class="fas fa-truck-fast"></i>
                            <div>
                                <strong>Envíos en Salta - Capital</strong>
                                <p>Entregas rápidas y seguras coordinadas por WhatsApp</p>
                            </div>
                        </div>
                        <div class="extra-item">
                            <i class="fas fa-rotate-left"></i>
                            <div>
                                <strong>Garantía de cambio</strong>
                                <p>Te asesoramos con los talles para tu tranquilidad</p>
                            </div>
                        </div>
                        <div class="extra-item">
                            <i class="fas fa-shield-halved"></i>
                            <div>
                                <strong>Compra 100% segura</strong>
                                <p>Trato directo y transparente</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>

<?php
    include_once __DIR__ . '/../templates/footer.php';
?>
