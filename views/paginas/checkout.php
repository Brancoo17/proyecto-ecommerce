<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    $carrito = $carrito ?? $_SESSION['carrito'] ?? [];
    $total = $total ?? 0;

    include_once __DIR__ . '/../templates/header.php';
?>

<main class="contenedor seccion checkout-contenedor">
    <div class="alinear-izquierda w-100 mb-2">
        <a href="/" class="boton boton-secundario"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>
    </div>

    <h1 class="nombre-pagina">Checkout</h1>
    <p class="descripcion-pagina">Finalizá tu compra completando tus datos</p>

    <div class="checkout-contenido">
        <!-- Sección Resumen del Pedido -->
        <section class="checkout-resumen">
            <h2 class="checkout-subtitulo"><i class="fas fa-shopping-bag"></i> Resumen del Pedido</h2>
            
            <div class="tabla-resumen-wrapper">
                <table class="tabla-resumen">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Talle</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="checkout-productos-lista">
                        <?php if(!empty($carrito)): ?>
                            <?php foreach($carrito as $item): 
                                $precio = floatval($item['precio'] ?? 0);
                                $cantidad = intval($item['cantidad'] ?? 1);
                                $subtotal = $precio * $cantidad;
                                $talleNombre = $item['talleNombre'] ?? $item['talle'] ?? 'Único';
                            ?>
                                <tr>
                                    <td data-label="Producto">
                                        <span class="producto-nombre"><?php echo s($item['nombre'] ?? ''); ?></span>
                                    </td>
                                    <td data-label="Talle"><span class="badge-talle"><?php echo s($talleNombre); ?></span></td>
                                    <td data-label="Cantidad"><?php echo $cantidad; ?></td>
                                    <td data-label="Precio Unit.">$<?php echo number_format($precio, 0, ',', '.'); ?></td>
                                    <td data-label="Subtotal" class="precio-subtotal">$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Precio Total -->
            <div class="checkout-total-box">
                <span class="total-label">Total a pagar:</span>
                <span class="total-monto" id="checkout-total">$<?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
        </section>

        <!-- Formulario del comprador -->
        <section class="checkout-form-seccion">
            <h2 class="checkout-subtitulo"><i class="fas fa-user-check"></i> Datos del Comprador</h2>

            <form class="formulario checkout-formulario" id="form-checkout" action="#" method="POST">
                <div class="campo-grupo">
                    <label for="nombre">Nombre Completo</label>
                    <div class="input-icono">
                        <i class="fas fa-user"></i>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingresá tu nombre y apellido" required>
                    </div>
                </div>

                <div class="campo-grupo">
                    <label for="telefono">Teléfono / WhatsApp</label>
                    <div class="input-icono">
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="telefono" name="telefono" placeholder="Ej: 11 2345 6789" required>
                    </div>
                </div>

                <div class="checkout-acciones">
                    <button type="button" class="boton-whatsapp" onclick="enviarPorWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Enviar pedido por WhatsApp
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php
    include_once __DIR__ . '/../templates/footer.php';
?>