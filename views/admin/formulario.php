<?php
/** @var \Model\Producto $producto */
?>

<?php
    // Generamos un mapa rápido de talle_id => cantidad de stock
    $stockMap = [];
    if (isset($productoTalles) && is_array($productoTalles)) {
        foreach ($productoTalles as $pTalle) {
            $stockMap[$pTalle->talle_id] = $pTalle->stock;
        }
    }

    // Determinamos cuál sección mostrar inicialmente
    $tieneTalles = false;
    $tieneSinTalles = false;
    if (!empty($stockMap)) {
        if (isset($stockMap[6])) {
            $tieneSinTalles = true;
        } else {
            $tieneTalles = true;
        }
    }
?>


<fieldset>
    <legend>Información General</legend>

    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="producto[nombre]" placeholder="Nombre del Producto" value="<?php echo s($producto->nombre); ?>">

    <label for="categoria_id">Categoria:</label>
    <select name="producto[categoria_id]" id="categoria">
        <option value="" disabled <?php echo !$producto->categoria_id ? 'selected' : ''; ?>>-- Seleccione --</option>
        <option value="1" <?php echo $producto->categoria_id == 1 ? 'selected' : ''; ?>>Superior</option>
        <option value="2" <?php echo $producto->categoria_id == 2 ? 'selected' : ''; ?>>Inferior</option>
        <option value="3" <?php echo $producto->categoria_id == 3 ? 'selected' : ''; ?>>Conjuntos</option>
        <option value="4" <?php echo $producto->categoria_id == 4 ? 'selected' : ''; ?>>Otros</option>
    </select>

    <label for="precio">Precio:</label>
    <input type="number" id="precio" name="producto[precio]" placeholder="Precio del Producto" value="<?php echo s($producto->precio); ?>">

    <label for="imagen">Imagen:</label>
    <input type="file" id="imagen" accept="image/jpeg, image/png" name="producto[imagen]">

    <?php if($producto->imagen): ?>
        <img src="/imagenes/<?php echo $producto->imagen; ?>" alt="Imagen del Producto" class="imagen-small">
    <?php endif; ?>

    <div class="campo-destacado" style="margin: 2rem 0; display: flex; align-items: center; gap: 1.2rem; background: rgba(248, 223, 177, 0.25); padding: 1.2rem 1.6rem; border-radius: 1rem; border: 1px solid #f8dfb1;">
        <input type="hidden" name="producto[destacado]" value="0">
        <input type="checkbox" id="destacado" name="producto[destacado]" value="1" <?php echo ($producto->destacado == 1) ? 'checked' : ''; ?> style="width: 2rem; height: 2rem; margin: 0; cursor: pointer;">
        <label for="destacado" style="margin: 0; cursor: pointer; font-weight: 700; color: #000000ff; font-size: 1.5rem;">
            <i class="fas fa-star" style="color: #f59e0b;"></i> Producto Destacado (se mostrará en la Home Page)
        </label>
    </div>

</fieldset>

<fieldset>
    <legend>Stock/Talles</legend>
    
    <label>Seleccione una opción para continuar:</label>

    <div class="opciones">
        <button type="button" class="boton boton-primario" id="opcion-con-talles">Producto con Talles</button>
        <button type="button" class="boton boton-primario" id="opcion-sin-talles">Producto sin Talles</button>
    </div>

    <div id="contenedor-talles" style="display: <?php echo $tieneTalles ? 'block' : 'none'; ?>;">

        <label>Talle: S</label>
        <input type="number" name="stock[1]" placeholder="Stock S" value="<?php echo $stockMap[1] ?? ''; ?>">

        <label>Talle: M</label>
        <input type="number" name="stock[2]" placeholder="Stock M" value="<?php echo $stockMap[2] ?? ''; ?>">

        <label>Talle: L</label>
        <input type="number" name="stock[3]" placeholder="Stock L" value="<?php echo $stockMap[3] ?? ''; ?>">

        <label>Talle: XL</label>
        <input type="number" name="stock[4]" placeholder="Stock XL" value="<?php echo $stockMap[4] ?? ''; ?>">

    </div>

    <div id="contenedor-sin-talles" style="display: <?php echo $tieneSinTalles ? 'block' : 'none'; ?>;">
        <label>Stock:</label>
        <input type="number" name="stock[6]" placeholder="Stock" value="<?php echo $stockMap[6] ?? ''; ?>">
    </div>
    
</fieldset>

