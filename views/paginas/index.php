<?php
    /**
     * @var Model\Producto[] $productos
     * @var int $totalProductos
     * @var array $stockMap
     */

    include_once __DIR__ . '/../templates/header.php';
?>

<!-- Hero Section Moderno -->
<section class="hero-moderno">
    <div class="hero-overlay"></div>
    <div class="container hero-container">
        <div class="hero-content">
            <span class="hero-badge-pill">
                <i class="fas fa-fire-flame-curved"></i> Colección 2026 | Alto Rendimiento
            </span>
            <h1 class="hero-title">
                Superá tus límites con <span class="text-destacado">estilo y confort</span>
            </h1>
            <p class="hero-subtitle">
                Ropa deportiva de máxima durabilidad, elasticidad y transpirabilidad. Diseñada para acompañarte en cada repetición y en tu día a día.
            </p>
            <div class="hero-acciones">
                <a href="/productos" class="boton boton-primario hero-btn">
                    <i class="fas fa-layer-group"></i> Ver Catálogo Completo
                </a>
                <a href="#destacados" class="boton boton-outline hero-btn">
                    <i class="fas fa-star"></i> Ver Destacados
                </a>
            </div>

            <!-- Píldoras de confianza rápida -->
            <div class="hero-stats-row">
                <div class="stat-pill">
                    <i class="fas fa-truck-fast"></i>
                    <span>Envíos en Salta - Capital</span>
                </div>
                <div class="stat-pill">
                    <i class="fas fa-shield-halved"></i>
                    <span>Calidad garantizada</span>
                </div>
                <div class="stat-pill">
                    <i class="fab fa-whatsapp"></i>
                    <span>Atención personalizada</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Beneficios / Propuesta de Valor -->
<section class="seccion-beneficios">
    <div class="container">
        <div class="grid-beneficios">
            <div class="tarjeta-beneficio">
                <div class="beneficio-icono">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <div class="beneficio-info">
                    <h3>Envíos Rápidos</h3>
                    <p>Entregas y envíos en Salta - Capital de forma rápida y segura directo a tu domicilio o punto de encuentro.</p>
                </div>
            </div>

            <div class="tarjeta-beneficio">
                <div class="beneficio-icono">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="beneficio-info">
                    <h3>Telas Técnicas</h3>
                    <p>Prendas elásticas, respirables y reforzadas para soportar tus entrenamientos más exigentes.</p>
                </div>
            </div>

            <div class="tarjeta-beneficio">
                <div class="beneficio-icono">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="beneficio-info">
                    <h3>Atención Inmediata</h3>
                    <p>¿Dudas con tu talle o modelo? Te asesoramos en minutos por WhatsApp antes de comprar.</p>
                </div>
            </div>

            <div class="tarjeta-beneficio">
                <div class="beneficio-icono">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <div class="beneficio-info">
                    <h3>Garantía de Cambio</h3>
                    <p>Tu satisfacción y comodidad son nuestra prioridad absoluta en cada prenda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Explorar por Categorías -->
<section class="seccion-categorias-destacadas">
    <div class="container">
        <div class="seccion-encabezado">
            <span class="subtitulo-seccion"><i class="fas fa-shapes"></i> Variedad & Diseño</span>
            <h2>Explorá por Categorías</h2>
            <p>Encontrá la indumentaria perfecta según lo que estás buscando</p>
        </div>

        <div class="grid-categorias-cards">
            <a href="/productos?categoria=1" class="categoria-card cat-tops">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-content">
                    <i class="fas fa-tshirt"></i>
                    <h3>Tops & Remeras</h3>
                    <span>Ver colección <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <a href="/productos?categoria=2" class="categoria-card cat-bottoms">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-content">
                    <svg class="icono-pantalones-svg" viewBox="0 0 24 24" fill="currentColor" width="3.2rem" height="3.2rem">
                        <path d="M18 2H6a1 1 0 0 0-1 1.07l1.3 18.2A1 1 0 0 0 7.3 22h3.2a1 1 0 0 0 1-.9L12 12.5l.5 8.6a1 1 0 0 0 1 .9h3.2a1 1 0 0 0 1-.73L19 3.07A1 1 0 0 0 18 2zm-7.7 18H7.5L6.4 4h4.8l.9 16zm6.2 0h-2.8l.9-16h4.8l-1.1 16h-1.8z"/>
                    </svg>
                    <h3>Pantalones, Calzas & Shorts</h3>
                    <span>Ver colección <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <a href="/productos?categoria=3" class="categoria-card cat-conjuntos">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-content">
                    <i class="fas fa-person-running"></i>
                    <h3>Conjuntos Deportivos</h3>
                    <span>Ver colección <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <a href="/productos?categoria=4" class="categoria-card cat-otros">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-content">
                    <i class="fas fa-bag-shopping"></i>
                    <h3>Accesorios & Otros</h3>
                    <span>Ver colección <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Productos Destacados (Limitados a 4-6 en la Home) -->
<section class="productos seccion-destacados" id="destacados">
    <div class="container">
        <div class="seccion-encabezado">
            <span class="subtitulo-seccion"><i class="fas fa-star"></i> Lo Más Pedido</span>
            <h2>Productos Destacados</h2>
            <p>Una selección especial de nuestras prendas favoritas para entrenar con estilo</p>
        </div>

        <!-- Filtro Rápido en Home -->
        <div class="categorias-tabs-home">
            <button type="button" class="tab-home activo" onclick="filtrarProductos('todos', this)">Todos</button>
            <button type="button" class="tab-home" onclick="filtrarProductos(1, this)">Tops</button>
            <button type="button" class="tab-home" onclick="filtrarProductos(2, this)">Pantalones & Calzas</button>
            <button type="button" class="tab-home" onclick="filtrarProductos(3, this)">Conjuntos</button>
            <button type="button" class="tab-home" onclick="filtrarProductos(4, this)">Otros</button>
        </div>

        <div class="grid-productos">
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
                        <span class="badge-destacado"><i class="fas fa-fire"></i> Destacado</span>
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
                                <label for="talle-home-<?php echo $producto->id; ?>"><i class="fas fa-ruler-horizontal"></i> Talle:</label>
                                <select name="talle" id="talle-home-<?php echo $producto->id; ?>" class="select-talle-moderno">
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

        <!-- Botón Ver Todo el Catálogo -->
        <div class="ver-todo-wrapper">
            <a href="/productos" class="boton boton-primario btn-ver-todo">
                <span>Ver todos los productos (<?php echo $totalProductos ?? count($productos); ?>)</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Banner de Performance / Calidad de la Marca -->
<section class="seccion-performance-banner">
    <div class="container">
        <div class="performance-card">
            <div class="performance-content">
                <span class="performance-tag"><i class="fas fa-check-circle"></i> Calidad Riada</span>
                <h2>Entrená sin límites ni restricciones</h2>
                <p>Nuestras prendas combinan soporte anatómico, costuras planas anti-roce y compresión inteligente para que te sientas segura y con energía en cada movimiento.</p>
                <div class="performance-checks">
                    <div class="check-item"><i class="fas fa-check"></i> Telas transpirables de secado rápido</div>
                    <div class="check-item"><i class="fas fa-check"></i> Ajuste que no se baja ni trasluce</div>
                    <div class="check-item"><i class="fas fa-check"></i> Diseños modernos y versátiles</div>
                </div>
                <a href="/productos" class="boton boton-primario">
                    <i class="fas fa-bag-shopping"></i> Explorar Tienda Completa
                </a>
            </div>
            <div class="performance-visual">
                <div class="visual-badge">
                    <i class="fas fa-heart"></i>
                    <strong>+1.000</strong>
                    <span>Clientes conformes</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonios / Opiniones -->
<section class="seccion-testimonios">
    <div class="container">
        <div class="seccion-encabezado">
            <span class="subtitulo-seccion"><i class="fas fa-comments"></i> Comunidad Riada</span>
            <h2>Lo que dicen quienes ya entrenan con nosotros</h2>
            <p>La experiencia y satisfacción de nuestros clientes nos impulsa día a día</p>
        </div>

        <div class="grid-testimonios">
            <div class="tarjeta-testimonio">
                <div class="estrellas">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonio-texto">"Las calzas tienen una tela increíble, no se transparentan para nada y se ajustan perfecto sin apretar de más. Llegó súper rápido a mi casa."</p>
                <div class="testimonio-autor">
                    <div class="autor-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <strong>Camila G.</strong>
                        <span>Compradora verificada</span>
                    </div>
                </div>
            </div>

            <div class="tarjeta-testimonio">
                <div class="estrellas">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonio-texto">"Compré un conjunto deportivo y la calidad superó mis expectativas. Me asesoraron con los talles por WhatsApp de 10. ¡Súper recomendables!"</p>
                <div class="testimonio-autor">
                    <div class="autor-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <strong>Agustina M.</strong>
                        <span>Compradora verificada</span>
                    </div>
                </div>
            </div>

            <div class="tarjeta-testimonio">
                <div class="estrellas">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testimonio-texto">"Excelente relación calidad-precio. Las remeras y buzos son muy cómodos tanto para el gimnasio como para salir. Ya es mi tercera compra."</p>
                <div class="testimonio-autor">
                    <div class="autor-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <strong>Lucas P.</strong>
                        <span>Comprador verificado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Banner Instagram / Comunidad -->
<section class="seccion-instagram">
    <div class="container">
        <div class="instagram-banner">
            <div class="ig-icon"><i class="fab fa-instagram"></i></div>
            <div class="ig-text">
                <h3>Sumate a nuestra comunidad deportiva</h3>
                <p>Seguinos en <strong>@riada_indumentaria</strong> para enterarte de nuevos ingresos, sorteos y tips de entrenamiento.</p>
            </div>
            <a href="https://www.instagram.com/riada_indumentaria/" target="_blank" rel="noopener noreferrer" class="boton boton-instagram">
                <i class="fab fa-instagram"></i> Seguir en Instagram
            </a>
        </div>
    </div>
</section>

<?php 
    include_once __DIR__ . '/../templates/footer.php';
?>