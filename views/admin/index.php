<?php
    use Model\Producto;
    use Model\ProductoTalle;
    /** @var string|null $resultado */
    /** @var Producto[] $productos */
    /** @var ProductoTalle $productoTalles */
?>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="/"><h1><i class="fas fa-dumbbell"></i> Riada <span>Indumentaria</span></h1></a>
            </div>
            <a href="/logout" class="boton boton-rojo"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>
</header>

<main class="contenedor seccion">
    <div class="alinear-izquierda w-100">
        <a href="/" class="boton boton-secundario"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <h1 class="nombre-pagina">Panel de Administración</h1>
    <p class="descripcion-pagina">Administra tu tienda</p>

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

    <a href="/admin/crear" class="boton boton-secundario"><i class="fa-solid fa-plus"></i> Nuevo Producto</a>

    <h2>Productos</h2>

    <table class="tabla-productos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Stock</th>
                <th class="acciones-columna">Acciones</th>
            </tr>
        </thead>

        <tbody> <!-- Mostrar los Resultados -->
            <?php foreach($productos as $producto): ?>
            <tr>
                <td><?php echo $producto->id; ?></td>
                <td><?php echo $producto->nombre; ?></td>
                <?php switch($producto->categoria_id) {
                    case 1:
                        $categoria = 'Tops';
                        break;
                    case 2:
                        $categoria = 'Bottoms';
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
                <td><?php echo $categoria; ?></td>
                <td><img src="/imagenes/<?php echo $producto->imagen; ?>" class="imagen-tabla"></td>
                <td>$<?php echo $producto->precio; ?></td>
                <td>
                    <?php 
                        // 1. Buscamos en la base de datos el stock específico de este producto
                        $stocks = ProductoTalle::whereAll('producto_id', $producto->id); 
                        
                        // 2. Mapeamos los IDs de los talles del 1 al 5 a sus nombres
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
                            <?php if($item->stock <= 0) continue; // Opcional: no muestra talles sin stock ?>
                            
                            <li>
                                <?php if($item->talle_id == 6): ?>
                                    <!-- Si el talle_id es 6, significa que es talle único o no lleva talle -->
                                    Stock: <?php echo $item->stock . 'x'; ?>
                                <?php else: ?>
                                    <!-- Si es otro talle, mostramos su respectiva letra mapeada -->
                                    Talle <?php echo $tallesMap[$item->talle_id] ?? 'Desconocido'; ?>: <?php echo $item->stock . 'x'; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>

                <td>
                    <a href="/admin/actualizar?id=<?php echo $producto->id; ?>" class="boton boton-amarillo">Actualizar</a>
                        
                    <form method="POST" class="w-100" action="/admin/eliminar">
                        <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
                        <input type="submit" value="Eliminar" class="boton boton-rojo">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <h2 style="margin-top: 4rem;">Pedidos Recibidos</h2>

    <?php if(empty($pedidos)): ?>
        <p>No hay pedidos registrados aún.</p>
    <?php else: ?>
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
    <?php endif; ?>

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