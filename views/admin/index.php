<?php
    use Model\Producto;
    use Model\ProductoTalle;
    /** @var string|null $resultado */
    /** @var Producto[] $productos */
    /** @var ProductoTalle $productoTalles */

    // Métricas del Dashboard (KPIs)
    $totalProductos = count($productos);
    $totalDestacados = 0;
    foreach($productos as $p) {
        if(isset($p->destacado) && $p->destacado == 1) {
            $totalDestacados++;
        }
    }

    $pedidosPendientes = 0;
    $pedidosCompletados = 0;
    $pedidosEnProceso = 0;
    $totalFacturado = 0;

    foreach($pedidos as $ped) {
        $estado = trim(strtolower($ped->estado ?? ''));
        if($estado === 'pendiente') {
            $pedidosPendientes++;
        } elseif($estado === 'completado') {
            $pedidosCompletados++;
            $totalFacturado += floatval($ped->total ?? 0);
        } elseif($estado === 'en proceso') {
            $pedidosEnProceso++;
        }
    }
?>

<?php
    include_once __DIR__ . '/../templates/header.php';
?>

<main class="contenedor seccion">
    <div class="alinear-izquierda w-100">
        <a href="/" class="boton boton-secundario"><i class="fa-solid fa-arrow-left"></i> Volver a la tienda</a>
    </div>

    <h1 class="nombre-pagina">Panel de Administración</h1>
    <p class="descripcion-pagina">Administra tu tienda</p>

    <!-- Tarjetas de Información Rápida (KPIs) -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card card-productos" onclick="cambiarTabAdmin('productos')" role="button" tabindex="0" title="Ver lista de productos">
            <div class="stat-card-header">
                <span class="stat-card-titulo">Productos</span>
                <div class="stat-card-icono icono-azul">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="stat-card-valor"><?php echo $totalProductos; ?></div>
            <div class="stat-card-pie">
                <span class="badge-subtexto"><i class="fas fa-star" style="color: #f59e0b;"></i> <?php echo $totalDestacados; ?> destacados en home</span>
            </div>
        </div>

        <div class="admin-stat-card card-pendientes" onclick="cambiarTabAdmin('pedidos')" role="button" tabindex="0" title="Ver pedidos pendientes">
            <div class="stat-card-header">
                <span class="stat-card-titulo">Pedidos Pendientes</span>
                <div class="stat-card-icono icono-ambar">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-card-valor"><?php echo $pedidosPendientes; ?></div>
            <div class="stat-card-pie">
                <?php if($pedidosPendientes > 0): ?>
                    <span class="badge-alerta-pendiente"><i class="fas fa-circle-exclamation"></i> Requieren atención</span>
                <?php else: ?>
                    <span class="badge-alerta-ok"><i class="fas fa-check"></i> Al día</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-stat-card card-completados" onclick="cambiarTabAdmin('pedidos')" role="button" tabindex="0" title="Ver pedidos completados">
            <div class="stat-card-header">
                <span class="stat-card-titulo">Pedidos Completados</span>
                <div class="stat-card-icono icono-verde">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
            <div class="stat-card-valor"><?php echo $pedidosCompletados; ?></div>
            <div class="stat-card-pie">
                <span class="badge-subtexto-verde"><i class="fas fa-truck-fast"></i> Entregados con éxito</span>
            </div>
        </div>

        <div class="admin-stat-card card-ingresos" onclick="cambiarTabAdmin('pedidos')" role="button" tabindex="0" title="Ver facturación en pedidos">
            <div class="stat-card-header">
                <span class="stat-card-titulo">Ventas Completadas</span>
                <div class="stat-card-icono icono-dorado">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
            <div class="stat-card-valor">$<?php echo number_format($totalFacturado, 0, ',', '.'); ?></div>
            <div class="stat-card-pie">
                <span class="badge-subtexto"><i class="fas fa-receipt"></i> Facturación acumulada</span>
            </div>
        </div>
    </div>

    <?php
        if($resultado) {   
            $mensaje = obtenerMensaje(intval($resultado));
            if($mensaje): 
    ?>
                <p class="alerta exito"><?php echo s($mensaje); ?></p>
    <?php 
            endif; 
        }
    ?>

    <!-- Tabs de Navegación del Admin -->
    <div class="admin-tabs">
        <button type="button" class="tab-admin activo" id="tabBtnProductos" onclick="cambiarTabAdmin('productos')">
            <i class="fas fa-boxes-stacked"></i> Productos (<span id="tabCountProductos"><?php echo count($productos); ?></span>)
        </button>
        <button type="button" class="tab-admin" id="tabBtnPedidos" onclick="cambiarTabAdmin('pedidos')">
            <i class="fas fa-clipboard-list"></i> Pedidos Recibidos (<?php echo count($pedidos); ?>)
        </button>
    </div>

    <!-- Pestaña 1: Productos -->
    <div id="panelProductos" class="tab-contenido-admin activo">
        <div class="admin-productos-header">
            <a href="/admin/crear" class="boton boton-secundario"><i class="fa-solid fa-plus"></i> Nuevo Producto</a>

            <!-- Buscador idéntico al catálogo -->
            <div class="buscador-caja buscador-admin">
                <i class="fas fa-search buscador-icono"></i>
                <input type="text" id="buscadorAdminProductos" placeholder="Buscar por nombre, ID o categoría (ej. Calza, Top)..." autocomplete="off">
                <button type="button" id="btnLimpiarBuscadorAdmin" class="btn-limpiar-busqueda" style="display: none;" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <p id="contadorProductosAdmin" class="contador-admin">Mostrando <?php echo count($productos); ?> productos</p>

        <!-- Contenedor con scroll horizontal para la tabla de productos -->
        <div class="tabla-contenedor-responsive">
            <table class="tabla-productos" id="tablaProductosAdmin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Imagen</th>
                        <th>Precio</th>
                        <th>Destacado</th>
                        <th>Stock</th>
                        <th class="acciones-columna">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Fila de sin resultados dinámicos -->
                    <tr id="filaSinResultadosAdmin" style="display: none;">
                        <td colspan="8" style="text-align: center; padding: 4rem; color: #94a3b8;">
                            <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; display: block; color: #cbd5e1;"></i>
                            No se encontraron productos que coincidan con la búsqueda.
                        </td>
                    </tr>

                    <?php foreach($productos as $producto): ?>
                    <tr class="fila-producto-admin" data-id="<?php echo $producto->id; ?>" data-nombre="<?php echo strtolower(s($producto->nombre)); ?>">
                        <td><?php echo $producto->id; ?></td>
                        <td class="col-nombre"><strong><?php echo s($producto->nombre); ?></strong></td>
                        <?php switch($producto->categoria_id) {
                            case 1:
                                $categoria = 'Tops';
                                break;
                            case 2:
                                $categoria = 'Inferiores';
                                break;
                            case 3:
                                $categoria = 'Conjuntos';
                                break;
                            case 4:
                                $categoria = 'Otros';
                                break;
                            default:
                                $categoria = 'Sin categoría';
                                break;
                        } ?>
                        <td class="col-categoria"><?php echo $categoria; ?></td>
                        <td><img src="/imagenes/<?php echo $producto->imagen; ?>" alt="<?php echo s($producto->nombre); ?>" class="imagen-tabla"></td>
                        <td>$<?php echo number_format($producto->precio, 0, ',', '.'); ?></td>
                        <td style="text-align: center;">
                            <button type="button" 
                                    class="btn-toggle-destacado <?php echo ($producto->destacado == 1) ? 'activo' : ''; ?>" 
                                    id="btn-destacado-<?php echo $producto->id; ?>"
                                    onclick="toggleDestacadoProducto(<?php echo $producto->id; ?>)"
                                    title="<?php echo ($producto->destacado == 1) ? 'Quitar de destacados en la Home' : 'Marcar como destacado en la Home'; ?>">
                                <i class="<?php echo ($producto->destacado == 1) ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                <span><?php echo ($producto->destacado == 1) ? 'Destacado' : 'Normal'; ?></span>
                            </button>
                        </td>
                        <td>
                            <?php 
                                $stocks = ProductoTalle::whereAll('producto_id', $producto->id); 
                                $tallesMap = [
                                    1 => 'S',
                                    2 => 'M',
                                    3 => 'L',
                                    4 => 'XL',
                                    5 => '2XL'
                                ];
                            ?>
                            <ul class="lista-stock">
                                <?php foreach($stocks as $item): ?>
                                    <?php if($item->stock <= 0) continue; ?>
                                    <li>
                                        <?php if($item->talle_id == 6): ?>
                                            Stock: <?php echo $item->stock . 'x'; ?>
                                        <?php else: ?>
                                            Talle <?php echo $tallesMap[$item->talle_id] ?? 'Desconocido'; ?>: <?php echo $item->stock . 'x'; ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <td class="col-acciones">
                            <div class="acciones-iconos">
                                <a href="/admin/actualizar?id=<?php echo $producto->id; ?>" class="btn-accion-icono btn-editar" title="Editar producto" aria-label="Editar producto">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                    
                                <form method="POST" action="/admin/eliminar" class="form-eliminar" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');">
                                    <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
                                    <button type="submit" class="btn-accion-icono btn-eliminar" title="Eliminar producto" aria-label="Eliminar producto">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pestaña 2: Pedidos -->
    <div id="panelPedidos" class="tab-contenido-admin" style="display: none;">
        <h2 style="margin: 2.5rem 0 1.5rem;">Pedidos Recibidos</h2>

        <?php if(empty($pedidos)): ?>
            <p style="padding: 3rem; background: #fff; border-radius: 1rem; text-align: center;">No hay pedidos registrados aún.</p>
        <?php else: ?>
            <!-- Contenedor con scroll horizontal para la tabla de pedidos -->
            <div class="tabla-contenedor-responsive">
                <table class="tabla-productos tabla-pedidos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Productos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_reverse($pedidos) as $pedido): ?>
                        <tr>
                            <td><?php echo $pedido->id; ?></td>
                            <td><strong><?php echo s($pedido->nombre); ?></strong></td>
                            <td>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $pedido->telefono); ?>" target="_blank" class="link-whatsapp">
                                    <i class="fab fa-whatsapp"></i> <?php echo s($pedido->telefono); ?>
                                </a>
                            </td>
                            <td><?php echo $pedido->created_at ? date('d/m/Y H:i', strtotime($pedido->created_at)) : '-'; ?></td>
                            <td><strong>$<?php echo number_format($pedido->total, 0, ',', '.'); ?></strong></td>
                            <td class="columna-estado" onclick="abrirModalEstado(<?php echo $pedido->id; ?>, '<?php echo s($pedido->estado); ?>')">
                                <?php 
                                    $claseEstado = strtolower(str_replace(' ', '-', $pedido->estado)); 
                                ?>
                                <span class="badge-estado badge-<?php echo $claseEstado; ?>" id="badge-estado-<?php echo $pedido->id; ?>">
                                    <?php echo s($pedido->estado); ?>
                                </span>
                            </td>

                            <td class="columna-productos">
                                <?php
                                    $items = Model\PedidoProducto::whereAll('pedido_id', $pedido->id);
                                    $tallesMap = [1 => 'S', 2 => 'M', 3 => 'L', 4 => 'XL', 5 => '2XL', 6 => 'Único'];
                                ?>
                                <ul class="lista-productos-pedido">
                                    <?php foreach($items as $item): ?>
                                        <?php 
                                            $prod = Model\Producto::find($item->producto_id);
                                            $talleNombre = $tallesMap[$item->talle_id] ?? 'Único';
                                        ?>
                                        <li>
                                            <span class="item-nombre"><?php echo $prod ? s($prod->nombre) : 'Producto eliminado'; ?></span>
                                            <span class="item-talle">(Talle: <?php echo $talleNombre; ?>)</span>
                                            <span class="item-cant">x<?php echo $item->cantidad; ?></span>
                                            <span class="item-precio">$<?php echo number_format($item->precio_unitario * $item->cantidad, 0, ',', '.'); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para cambiar estado del pedido -->
    <div id="modalEstado" class="modal-estado">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3>Cambiar Estado - Pedido #<span id="modalPedidoId"></span></h3>
                <button type="button" class="btn-cerrar-modal" onclick="cerrarModalEstado()">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalInputPedidoId">
                <label for="modalSelectEstado">Seleccionar Nuevo Estado:</label>
                <select id="modalSelectEstado" class="select-estado">
                    <option value="Pendiente">Pendiente</option>
                    <option value="En Proceso">En Proceso</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="boton boton-secundario" onclick="cerrarModalEstado()">Cancelar</button>
                <button type="button" class="boton boton-primario" onclick="guardarEstadoPedido()">Guardar Cambio</button>
            </div>
        </div>
    </div>

</main>