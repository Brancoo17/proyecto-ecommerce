<?php
    /**
     * @var Model\Producto[] $productos
     * @var array $stockMap
     * @var string $categoriaActiva
     */

    include_once __DIR__ . '/../templates/header.php';
?>

<!-- Banner Cabecera Catálogo -->
<section class="catalogo-hero">
    <div class="container">
        <div class="catalogo-hero-content">
            <span class="badge-subtitulo"><i class="fas fa-layer-group"></i> Colección Completa</span>
            <h1>Catálogo de Productos</h1>
            <p>Explorá todas nuestras prendas de entrenamiento, calzas, tops, conjuntos y accesorios diseñados para tu máximo rendimiento.</p>
        </div>
    </div>
</section>

<!-- Sección Catálogo Principal -->
<main class="catalogo-section">
    <div class="container">
        <!-- Barra de Búsqueda y Filtros -->
        <div class="catalogo-controles">
            <div class="buscador-caja">
                <i class="fas fa-search buscador-icono"></i>
                <input type="text" id="buscadorProductos" placeholder="Buscar por nombre (ej. Calza, Remera, Buzo)..." autocomplete="off">
                <button type="button" id="btnLimpiarBuscador" class="btn-limpiar-busqueda" style="display: none;" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="orden-caja">
                <label for="ordenarProductos"><i class="fas fa-sort-amount-down"></i> Ordenar:</label>
                <select id="ordenarProductos" onchange="ordenarProductosLista(this.value)">
                    <option value="destacados">Destacados</option>
                    <option value="precio-menor">Precio: Menor a Mayor</option>
                    <option value="precio-mayor">Precio: Mayor a Menor</option>
                    <option value="nombre-az">Nombre: A - Z</option>
                </select>
            </div>
        </div>

        <!-- Botones de Filtro por Categoría -->
        <div class="categorias-tabs">
            <button type="button" class="tab-categoria <?php echo ($categoriaActiva === 'todos') ? 'activo' : ''; ?>" data-categoria="todos" onclick="filtrarProductosCatalogo('todos')">
                <i class="fas fa-border-all"></i> Todos los productos
            </button>
            <button type="button" class="tab-categoria <?php echo ($categoriaActiva === '1') ? 'activo' : ''; ?>" data-categoria="1" onclick="filtrarProductosCatalogo(1)">
                <i class="fas fa-tshirt"></i> Tops
            </button>
            <button type="button" class="tab-categoria <?php echo ($categoriaActiva === '2') ? 'activo' : ''; ?>" data-categoria="2" onclick="filtrarProductosCatalogo(2)">
                <svg class="icono-pantalones-svg" viewBox="0 0 24 24" fill="currentColor" width="1.6rem" height="1.6rem" style="display:inline-block; vertical-align:middle;">
                    <path d="M18 2H6a1 1 0 0 0-1 1.07l1.3 18.2A1 1 0 0 0 7.3 22h3.2a1 1 0 0 0 1-.9L12 12.5l.5 8.6a1 1 0 0 0 1 .9h3.2a1 1 0 0 0 1-.73L19 3.07A1 1 0 0 0 18 2zm-7.7 18H7.5L6.4 4h4.8l.9 16zm6.2 0h-2.8l.9-16h4.8l-1.1 16h-1.8z"/>
                </svg> Pantalones & Calzas
            </button>
            <button type="button" class="tab-categoria <?php echo ($categoriaActiva === '3') ? 'activo' : ''; ?>" data-categoria="3" onclick="filtrarProductosCatalogo(3)">
                <i class="fas fa-person-running"></i> Conjuntos
            </button>
            <button type="button" class="tab-categoria <?php echo ($categoriaActiva === '4') ? 'activo' : ''; ?>" data-categoria="4" onclick="filtrarProductosCatalogo(4)">
                <i class="fas fa-tag"></i> Otros
            </button>
        </div>

        <!-- Indicador de Resultados -->
        <div class="catalogo-info-barra">
            <span id="contadorProductos" class="contador-productos">Mostrando <?php echo count($productos); ?> productos</span>
        </div>

        <!-- Mensaje de Sin Resultados -->
        <div id="sinResultados" class="sin-resultados-box" style="display: none;">
            <div class="sin-resultados-icon">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3>No encontramos productos</h3>
            <p>No hay prendas que coincidan con los filtros seleccionados.</p>
            <button type="button" class="boton boton-secundario" onclick="resetearFiltros()">Ver todos los productos</button>
        </div>

        <!-- Grid de Todos los Productos -->
        <div class="grid-productos" id="gridCatalogo">
            <?php foreach($productos as $producto): ?>
                <?php 
                    $stocks = $stockMap[$producto->id] ?? [];
                    $stockTotal = array_sum(array_values($stocks));
                    $estaAgotado = ($stockTotal <= 0 && !empty($stocks));
                ?>
                <div class="producto card-producto-moderno <?php echo $estaAgotado ? 'producto-agotado' : ''; ?>" 
                     data-id="<?php echo $producto->id; ?>"
                     data-nombre="<?php echo s($producto->nombre); ?>"
                     data-precio="<?php echo $producto->precio; ?>"
                     data-imagen="<?php echo $producto->imagen; ?>"
                     data-categoria="<?php echo $producto->categoria_id; ?>"
                     data-stock='<?php echo json_encode($stocks); ?>'>
                    
                    <a href="/producto?id=<?php echo $producto->id; ?>" class="producto-enlace" aria-label="Ver <?php echo s($producto->nombre); ?>"></a>

                    <div class="producto-imagen">
                        <?php if($estaAgotado): ?>
                            <span class="badge-stock badge-agotado">Agotado</span>
                        <?php endif; ?>

                        <img src="/imagenes/<?php echo $producto->imagen; ?>" alt="<?php echo s($producto->nombre); ?>" loading="lazy">
                        <div class="overlay-ver-mas">
                            <span><i class="fas fa-eye"></i> Ver detalle</span>
                        </div>
                    </div>

                    <div class="producto-info">
                        <span class="categoria-etiqueta">
                            <?php 
                                switch($producto->categoria_id) {
                                    case 1: echo 'Top'; break;
                                    case 2: echo 'Pantalón / Calza'; break;
                                    case 3: echo 'Conjunto'; break;
                                    default: echo 'Accesorio'; break;
                                }
                            ?>
                        </span>
                        <h3><?php echo s($producto->nombre); ?></h3>
                        <p class="producto-precio">$<?php echo number_format($producto->precio, 0, ',', '.'); ?></p>
                        
                        <?php if($producto->categoria_id !== 4): ?>
                            <div class="selector-talle-wrap">
                                <label for="talle-cat-<?php echo $producto->id; ?>"><i class="fas fa-ruler-horizontal"></i> Talle:</label>
                                <select name="talle" id="talle-cat-<?php echo $producto->id; ?>" class="select-talle-moderno">
                                    <option value="" disabled selected>-- Elegí tu talle --</option>
                                    <option value="1" <?php echo (($stocks[1] ?? 0) <= 0) ? 'disabled' : ''; ?>>S <?php echo (($stocks[1] ?? 0) <= 0) ? '(Sin stock)' : ''; ?></option>
                                    <option value="2" <?php echo (($stocks[2] ?? 0) <= 0) ? 'disabled' : ''; ?>>M <?php echo (($stocks[2] ?? 0) <= 0) ? '(Sin stock)' : ''; ?></option>
                                    <option value="3" <?php echo (($stocks[3] ?? 0) <= 0) ? 'disabled' : ''; ?>>L <?php echo (($stocks[3] ?? 0) <= 0) ? '(Sin stock)' : ''; ?></option>
                                    <option value="4" <?php echo (($stocks[4] ?? 0) <= 0) ? 'disabled' : ''; ?>>XL <?php echo (($stocks[4] ?? 0) <= 0) ? '(Sin stock)' : ''; ?></option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="acciones-producto">
                        <button type="button" class="boton boton-primario btn-agregar-carrito" <?php echo $estaAgotado ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-bag"></i> 
                            <?php echo $estaAgotado ? 'Sin stock' : 'Agregar al carrito'; ?>
                        </button>               
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php
    include_once __DIR__ . '/../templates/footer.php';
?>
