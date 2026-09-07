document.addEventListener('DOMContentLoaded', () => {
    mostrarHeader();
    iniciarApp();
});

// Header
let lastScrollTop = 0;
let hideTimer;

function mostrarHeader() {
  const header = document.querySelector('.header-sticky');
  if (!header) return;

  header.classList.add('sticky');
  header.classList.remove('oculto');
}

function ocultarHeader() {
  const header = document.querySelector('.header-sticky');
  if (!header) return;

  header.classList.remove('sticky');
  header.classList.add('oculto');
}

function reiniciarOcultarHeader() {
  clearTimeout(hideTimer);

  hideTimer = setTimeout(() => {
    if (window.scrollY > 80) {
      ocultarHeader();
    }
  }, 1800);
}

window.addEventListener('scroll', () => {
  const header = document.querySelector('.header-sticky');
  if (!header) return;

  const scrollTop = window.scrollY || document.documentElement.scrollTop;

  if (scrollTop <= 80) {
    clearTimeout(hideTimer);
    mostrarHeader();
    lastScrollTop = 0;
    return;
  }

  if (scrollTop > lastScrollTop) {
    mostrarHeader();
    reiniciarOcultarHeader();
  } else {
    mostrarHeader();
    clearTimeout(hideTimer);
  }

  lastScrollTop = scrollTop;
}, { passive: true });

// Nombres de talles para mostrar en notificaciones y carrito
const TALLES_NOMBRES = {
    '1': 'S',
    '2': 'M',
    '3': 'L',
    '4': 'XL',
    '6': 'Único'
};

function iniciarApp() {
    iniciarMenuMobile();
    seleccionarTalles();
    configurarProductoDetalle();
    inicializarCarrito();
    cargarCheckoutPage();
    iniciarCatalogoPage();
    iniciarAdminTabsYBuscador();
    iniciarLoginTogglePassword();
}

/**
 * Inicializa el carrito desde localStorage, actualiza el contador y
 * configura los event listeners para agregar productos.
 */
function inicializarCarrito() {
    actualizarContadorCarrito();
    configurarAgregarCarritoLista();
    configurarAgregarCarritoDetalle();
    
    // Crear el DOM del carrito para que esté listo al cargar
    crearCartDOM();
}

/**
 * Obtiene el carrito actual desde localStorage
 */
function obtenerCarrito() {
    return JSON.parse(localStorage.getItem('carrito')) || [];
}

/**
 * Guarda el carrito en localStorage
 */
function guardarCarrito(carrito) {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

/**
 * Actualiza el contador visible de la cantidad total de artículos en el carrito
 */
function actualizarContadorCarrito() {
    const contador = document.querySelector('#cartCount');
    if (!contador) return;

    const carrito = obtenerCarrito();
    const totalArticulos = carrito.reduce((total, item) => total + item.cantidad, 0);
    contador.textContent = totalArticulos;
}

/**
 * Muestra una notificación Toast animada y premium en la esquina superior derecha
 * @param {string} mensaje - El texto a mostrar
 * @param {string} tipo - 'exito' | 'error'
 */
function mostrarToast(mensaje, tipo = 'exito') {
    // Buscar o crear el contenedor de toasts
    let contenedor = document.querySelector('.toast-container');
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.className = 'toast-container';
        document.body.appendChild(contenedor);
    }

    // Crear el toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;

    // Icono correspondiente
    const icono = tipo === 'exito' 
        ? '<i class="fas fa-check-circle"></i>' 
        : '<i class="fas fa-exclamation-circle"></i>';

    toast.innerHTML = `${icono}<span>${mensaje}</span>`;
    contenedor.appendChild(toast);

    // Animación de entrada (fade-in & slide-in)
    setTimeout(() => {
        toast.classList.add('mostrar');
    }, 50);

    // Desvanecer y remover después de 4 segundos
    setTimeout(() => {
        toast.classList.remove('mostrar');
        // Esperar a que termine la animación de salida para removerlo del DOM
        toast.addEventListener('transitionend', () => {
            toast.remove();
        });
    }, 2500);
}

/**
 * Intenta agregar un artículo al carrito realizando validaciones de stock
 */
function procesarAgregarAlCarrito(productoId, nombre, precio, imagen, talleId, cantidad) {
    const carrito = obtenerCarrito();

    // Buscar si ya existe el producto con ese talle específico en el carrito
    const itemExistente = carrito.find(item => item.id === productoId && item.talleId === talleId);

    // Guardar talleNombre
    const talleNombre = TALLES_NOMBRES[talleId] || 'Único';

    if (itemExistente) {
        itemExistente.cantidad += cantidad;
    } else {
        carrito.push({
            id: productoId,
            nombre: nombre,
            precio: parseFloat(precio),
            imagen: imagen,
            talleId: talleId,
            talleNombre: talleNombre,
            cantidad: cantidad
        });
    }

    guardarCarrito(carrito);
    actualizarContadorCarrito();
    mostrarToast(`¡${nombre} (${talleNombre}) agregado al carrito!`, 'exito');
    
    // Si el panel del carrito ya está abierto, volver a renderizarlo
    const panel = document.querySelector('.cart-panel');
    if (panel && panel.classList.contains('abierto')) {
        renderizarCarritoHTML();
    }
    
    return true;
}

/**
 * Configura los botones de agregar al carrito en la cuadrícula de productos (index)
 */
function configurarAgregarCarritoLista() {
    const botonesAgregar = document.querySelectorAll('.grid-productos .btn-agregar-carrito');

    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', (e) => {
            // Evitar que el clic en el botón active el enlace de la tarjeta completa
            e.preventDefault();
            e.stopPropagation();

            const tarjeta = boton.closest('.producto');
            if (!tarjeta) return;

            const productoId = parseInt(tarjeta.dataset.id);
            const nombre = tarjeta.dataset.nombre;
            const precio = tarjeta.dataset.precio;
            const imagen = tarjeta.dataset.imagen;
            const categoriaId = parseInt(tarjeta.dataset.categoria);
            const stockMap = JSON.parse(tarjeta.dataset.stock || '{}');

            let talleId = '6'; // Por defecto talle Único (Otros = categoria 4)

            // Si no es de categoría "Otros", validar que se haya seleccionado un talle
            if (categoriaId !== 4) {
                const selectorTalle = tarjeta.querySelector('select[name="talle"]');
                if (!selectorTalle || selectorTalle.value === '') {
                    mostrarToast('Por favor, seleccioná un talle antes de agregar al carrito.', 'error');
                    return;
                }
                talleId = selectorTalle.value;
            }

            // Validar stock disponible
            const stockDisponible = parseInt(stockMap[talleId]) || 0;
            if (stockDisponible <= 0) {
                mostrarToast('El talle seleccionado no tiene stock disponible.', 'error');
                return;
            }

            // Verificar si excede el stock considerando lo que ya tiene en el carrito
            const carrito = obtenerCarrito();
            const itemExistente = carrito.find(item => item.id === productoId && item.talleId === talleId);
            const cantidadEnCarrito = itemExistente ? itemExistente.cantidad : 0;

            if (cantidadEnCarrito + 1 > stockDisponible) {
                mostrarToast(`No hay suficiente stock.`, 'error');
                return;
            }

            // Procesar
            procesarAgregarAlCarrito(productoId, nombre, precio, imagen, talleId, 1);
        });
    });
}

/**
 * Configura el botón de agregar al carrito en la página de detalle del producto
 */
function configurarAgregarCarritoDetalle() {
    const detalleSeccion = document.querySelector('.detalle-producto');
    if (!detalleSeccion) return;

    const botonAgregar = detalleSeccion.querySelector('.btn-agregar-carrito');
    if (!botonAgregar) return;

    botonAgregar.addEventListener('click', () => {
        const productoId = parseInt(detalleSeccion.dataset.id);
        const nombre = detalleSeccion.dataset.nombre;
        const precio = detalleSeccion.dataset.precio;
        const imagen = detalleSeccion.dataset.imagen;
        const categoriaId = parseInt(detalleSeccion.dataset.categoria);
        const stockMap = JSON.parse(detalleSeccion.dataset.stock || '{}');

        let talleId = '6'; // Por defecto talle Único (Otros = categoria 4)

        // Si no es de categoría "Otros", validar que se haya seleccionado un talle en los botones
        if (categoriaId !== 4) {
            const botonTalleSeleccionado = detalleSeccion.querySelector('.talle-btn.seleccionado');
            if (!botonTalleSeleccionado) {
                mostrarToast('Por favor, seleccioná un talle antes de agregar al carrito.', 'error');
                return;
            }
            talleId = botonTalleSeleccionado.dataset.talle;
        }

        // Obtener la cantidad del input
        const inputCantidad = document.querySelector('#cantidadProducto');
        const cantidadAAgregar = inputCantidad ? parseInt(inputCantidad.value) || 1 : 1;

        if (cantidadAAgregar <= 0) {
            mostrarToast('La cantidad debe ser mayor a 0.', 'error');
            return;
        }

        // Validar stock disponible
        const stockDisponible = parseInt(stockMap[talleId]) || 0;
        if (stockDisponible <= 0) {
            mostrarToast('El talle seleccionado no tiene stock disponible.', 'error');
            return;
        }

        // Verificar si excede el stock considerando lo que ya tiene en el carrito
        const carrito = obtenerCarrito();
        const itemExistente = carrito.find(item => item.id === productoId && item.talleId === talleId);
        const cantidadEnCarrito = itemExistente ? itemExistente.cantidad : 0;

        if (cantidadEnCarrito + cantidadAAgregar > stockDisponible) {
            mostrarToast(`No hay suficiente stock. Quedan ${stockDisponible - cantidadEnCarrito} unidades disponibles.`, 'error');
            return;
        }

        // Procesar
        const exito = procesarAgregarAlCarrito(productoId, nombre, precio, imagen, talleId, cantidadAAgregar);
        if (exito) {
            // Reiniciar selector de cantidad a 1
            if (inputCantidad) {
                inputCantidad.value = 1;
            }
        }
    });
}

/**
 * Configura los botones de cantidad y talle en la vista de detalle
 */
function configurarProductoDetalle() {
    // 1. Botones para cantidad de productos
    const btnRestar = document.querySelector('#restarCantidad');
    const btnSumar = document.querySelector('#sumarCantidad');
    const inputCantidad = document.querySelector('#cantidadProducto');

    if (btnRestar && btnSumar && inputCantidad) {
        const minVal = parseInt(inputCantidad.getAttribute('min')) || 1;
        const maxVal = parseInt(inputCantidad.getAttribute('max')) || 10;

        btnRestar.addEventListener('click', () => {
            let actual = parseInt(inputCantidad.value) || 1;
            if (actual > minVal) {
                inputCantidad.value = actual - 1;
            }
        });

        btnSumar.addEventListener('click', () => {
            let actual = parseInt(inputCantidad.value) || 1;
            if (actual < maxVal) {
                inputCantidad.value = actual + 1;
            }
        });
    }

    // 2. Selección de talles
    const botonesTalle = document.querySelectorAll('.talle-btn');
    botonesTalle.forEach(boton => {
        boton.addEventListener('click', () => {
            if (boton.classList.contains('agotado')) return;

            botonesTalle.forEach(b => b.classList.remove('seleccionado'));
            boton.classList.add('seleccionado');
        });
    });
}

/**
 * Toggles view configurations for admin panel forms (adding or modifying stocks)
 */
function seleccionarTalles() {
    const btnConTalles = document.querySelector('#opcion-con-talles');
    const btnSinTalles = document.querySelector('#opcion-sin-talles');
    const contenedorTalles = document.querySelector('#contenedor-talles');
    const contenedorSinTalles = document.querySelector('#contenedor-sin-talles');

    if (btnConTalles && btnSinTalles && contenedorTalles && contenedorSinTalles) {
        // Verificar estado inicial en base a la visibilidad (soporte para edición)
        const isTallesHidden = window.getComputedStyle(contenedorTalles).display === 'none';
        const isSinTallesHidden = window.getComputedStyle(contenedorSinTalles).display === 'none';

        if (isTallesHidden) {
            contenedorTalles.querySelectorAll('input').forEach(input => input.setAttribute('disabled', 'true'));
        } else {
            btnConTalles.classList.add('activo');
        }

        if (isSinTallesHidden) {
            contenedorSinTalles.querySelectorAll('input').forEach(input => input.setAttribute('disabled', 'true'));
        } else {
            btnSinTalles.classList.add('activo');
        }

        // Eventos de clic para alternar
        btnConTalles.addEventListener('click', () => {
            mostrarContenedor(contenedorTalles, contenedorSinTalles, btnConTalles, btnSinTalles);
        });

        btnSinTalles.addEventListener('click', () => {
            mostrarContenedor(contenedorSinTalles, contenedorTalles, btnSinTalles, btnConTalles);
        });
    }
}

function mostrarContenedor(mostrar, ocultar, btnActivo, btnInactivo) {
    // Mostrar/Ocultar contenedores
    mostrar.style.display = 'block';
    ocultar.style.display = 'none';
    
    // Habilitar inputs del contenedor visible y deshabilitar los del oculto
    const inputsMostrar = mostrar.querySelectorAll('input');
    const inputsOcultar = ocultar.querySelectorAll('input');
    
    inputsMostrar.forEach(input => input.removeAttribute('disabled'));
    inputsOcultar.forEach(input => input.setAttribute('disabled', 'true'));
    
    // Cambiar clases de los botones para dar feedback visual
    btnActivo.classList.add('activo');
    btnInactivo.classList.remove('activo');
}

/**
 * -------------------------------------------------------------
 * SISTEMA DEL CARRITO DE COMPRAS (SIDEBAR DRAWER)
 * -------------------------------------------------------------
 */

/**
 * Crea la estructura DOM inicial del carrito si no existe
 */
function crearCartDOM() {
    if (document.querySelector('.cart-panel')) return;

    // Backdrop overlay
    const overlay = document.createElement('div');
    overlay.className = 'cart-overlay';
    
    // Panel drawer
    const panel = document.createElement('div');
    panel.className = 'cart-panel';
    
    panel.innerHTML = `
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Mi Carrito</h2>
            <button type="button" class="btn-cerrar-cart" id="cerrarCart"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-body" id="cartBody">
            <!-- Los productos se cargan dinámicamente aquí -->
        </div>
        <div class="cart-footer" id="cartFooter">
            <div class="cart-totales">
                <span>Total:</span>
                <span class="cart-precio-final" id="cartTotal">$0</span>
            </div>
            <a href="/checkout" class="boton boton-primario btn-checkout">Iniciar Compra</a>
        </div>
    `;

    document.body.appendChild(overlay);
    document.body.appendChild(panel);

    // Eventos para cerrar
    overlay.addEventListener('click', cerrarCart);
    panel.querySelector('#cerrarCart').addEventListener('click', cerrarCart);
}

/**
 * Cierra la barra lateral del carrito
 */
function cerrarCart() {
    const overlay = document.querySelector('.cart-overlay');
    const panel = document.querySelector('.cart-panel');
    if (overlay && panel) {
        overlay.classList.remove('abierto');
        panel.classList.remove('abierto');
    }
}

/**
 * Muestra o despliega la barra lateral del carrito
 */
function toggleCart() {
    let overlay = document.querySelector('.cart-overlay');
    let panel = document.querySelector('.cart-panel');

    if (!overlay || !panel) {
        crearCartDOM();
        overlay = document.querySelector('.cart-overlay');
        panel = document.querySelector('.cart-panel');
    }

    const estaAbierto = panel.classList.contains('abierto');
    if (estaAbierto) {
        overlay.classList.remove('abierto');
        panel.classList.remove('abierto');
    } else {
        renderizarCarritoHTML();
        overlay.classList.add('abierto');
        panel.classList.add('abierto');
    }
}

/**
 * Genera el HTML de los elementos del carrito y calcula el total
 */
function renderizarCarritoHTML() {
    const cartBody = document.querySelector('#cartBody');
    const cartFooter = document.querySelector('#cartFooter');
    const cartTotal = document.querySelector('#cartTotal');
    if (!cartBody || !cartFooter) return;

    const carrito = obtenerCarrito();

    if (carrito.length === 0) {
        cartBody.innerHTML = `
            <div class="cart-vacio">
                <i class="fas fa-shopping-basket"></i>
                <p>Tu carrito está vacío</p>
                <p>¡Agrega algunos productos para empezar!</p>
            </div>
        `;
        cartFooter.style.display = 'none';
        return;
    }

    cartFooter.style.display = 'block';
    cartBody.innerHTML = '';

    let total = 0;

    carrito.forEach(item => {
        const itemTotal = item.precio * item.cantidad;
        total += itemTotal;

        const cartItemDiv = document.createElement('div');
        cartItemDiv.className = 'cart-item';
        cartItemDiv.innerHTML = `
            <div class="cart-item-imagen">
                <img src="/imagenes/${item.imagen}" alt="${item.nombre}">
            </div>
            <div class="cart-item-detalles">
                <h3>${item.nombre}</h3>
                <div class="cart-item-meta">
                    <span>Talle: <strong>${item.talleNombre}</strong></span>
                    <span>Cant: <strong>${item.cantidad}</strong></span>
                </div>
                <div class="cart-item-footer">
                    <span class="cart-item-precio-total">$${formatearMoneda(itemTotal)}</span>
                </div>
            </div>
            <button type="button" class="btn-eliminar-item" data-id="${item.id}" data-talle="${item.talleId}" title="Eliminar producto">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;

        cartBody.appendChild(cartItemDiv);
    });

    if (cartTotal) {
        cartTotal.textContent = `$${formatearMoneda(total)}`;
    }

    // Configurar botones de eliminar
    const botonesEliminar = cartBody.querySelectorAll('.btn-eliminar-item');
    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', () => {
            const id = parseInt(boton.dataset.id);
            const talleId = boton.dataset.talle;
            eliminarDelCarrito(id, talleId);
        });
    });
}

/**
 * Elimina un producto específico (coincidencia de ID y talle) del carrito
 */
function eliminarDelCarrito(productoId, talleId) {
    let carrito = obtenerCarrito();
    const itemAEliminar = carrito.find(item => item.id === productoId && item.talleId === talleId);
    
    if (itemAEliminar) {
        carrito = carrito.filter(item => !(item.id === productoId && item.talleId === talleId));
        guardarCarrito(carrito);
        actualizarContadorCarrito();
        renderizarCarritoHTML();
        mostrarToast(`Se eliminó ${itemAEliminar.nombre} del carrito.`, 'error');
    }
}

/**
 * Formatea un valor numérico como moneda sin decimales
 */
function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-AR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(valor);
}

/**
 * Si estamos en la página de checkout, renderiza la lista de productos del carrito guardados en localStorage
 */
function cargarCheckoutPage() {
    const listaProductos = document.querySelector('#checkout-productos-lista');
    const elementoTotal = document.querySelector('#checkout-total');
    if (!listaProductos) return;

    const carrito = obtenerCarrito();

    if (carrito.length === 0 && (!listaProductos.children || listaProductos.children.length === 0)) {
        listaProductos.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem; color: #5b5b5b;">
                    <i class="fas fa-shopping-basket" style="font-size: 3rem; margin-bottom: 1rem; display: block; color: #e1e1e1;"></i>
                    No hay productos en tu carrito. <a href="/" style="color: #0c4e68; font-weight: bold; text-decoration: underline;">Ver productos</a>
                </td>
            </tr>
        `;
        if (elementoTotal) elementoTotal.textContent = '$0';
        return;
    }

    if (carrito.length > 0) {
        listaProductos.innerHTML = '';
        let total = 0;

        carrito.forEach(item => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Producto">
                    <span class="producto-nombre">${item.nombre}</span>
                </td>
                <td data-label="Talle"><span class="badge-talle">${item.talleNombre || 'Único'}</span></td>
                <td data-label="Cantidad">${item.cantidad}</td>
                <td data-label="Precio Unit.">$${formatearMoneda(item.precio)}</td>
                <td data-label="Subtotal" class="precio-subtotal">$${formatearMoneda(subtotal)}</td>
            `;
            listaProductos.appendChild(tr);
        });

        if (elementoTotal) {
            elementoTotal.textContent = `$${formatearMoneda(total)}`;
        }
    }
}

/**
 * Función auxiliar asíncrona para guardar el pedido en la BD en segundo plano
*/
async function guardarPedidoEnBD(datosPedido) {
    try {
        const respuesta = await fetch('/api/pedidos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosPedido)
        });

        const resultado = await respuesta.json();
        if (!resultado.ok) {
            console.error('El servidor no pudo confirmar el guardado del pedido.');
        }
    } catch (error) {
        console.error('Error al guardar el pedido en la base de datos:', error);
    }
}

/**
 * Función principal sincrónica para abrir WhatsApp y registrar el pedido
 */
function enviarPorWhatsApp() {
    const numeroVendedor = "543875978810";
    const carrito = obtenerCarrito();

    if (carrito.length === 0) {
        mostrarToast('El carrito está vacío.', 'error');
        return;
    }

    const nombre = document.querySelector('#nombre').value;
    const telefono = document.querySelector('#telefono').value;

    if (!nombre || !telefono) {
        mostrarToast('Por favor, complete todos los campos.', 'error');
        return;
    }

    const total = carrito.reduce((sum, item) => sum + item.precio * item.cantidad, 0);

    // 1. Guardar en la base de datos en segundo plano (sin bloquear la interfaz)
    guardarPedidoEnBD({
        nombre: nombre,
        telefono: telefono,
        total: total,
        productos: carrito
    });

    // 2. Construir el mensaje de WhatsApp
    let mensaje = `*Nuevo Pedido - Riada Indumentaria*\n\n`;
    mensaje += `*Cliente:* ${nombre}\n`;
    mensaje += `*Teléfono:* ${telefono}\n\n`;
    mensaje += `*Productos:*\n`;

    carrito.forEach(item => {
        mensaje += `\n- *${item.nombre}* (${item.talleNombre})`;
        mensaje += `\nTalle: ${item.talleNombre}`;
        mensaje += `\nCantidad: ${item.cantidad}`;
        mensaje += `\nPrecio: $${formatearMoneda(item.precio * item.cantidad)}`;
    });

    mensaje += `\n\n*Total del pedido: $${formatearMoneda(total)}*`;
    mensaje += `\n\n_Enviado desde el sitio web_`;

    const mensajeCodificado = encodeURIComponent(mensaje);
    const whatsappURL = `https://wa.me/${numeroVendedor}?text=${mensajeCodificado}`;

    // 3. Abrir WhatsApp inmediatamente (100% directo, el navegador NUNCA lo bloquea)
    window.open(whatsappURL, '_blank');

    // 4. Feedback, limpiar carrito y redirigir
    mostrarToast('Enviando tu pedido a WhatsApp...', 'exito');

    localStorage.removeItem('carrito');
    actualizarContadorCarrito();
    renderizarCarritoHTML();

    setTimeout(() => {
        window.location.href = "/";
    }, 500);
}

/**
 * Filtra los productos visibles en la Home según la categoría seleccionada
 * @param {string|number} categoria - ID de la categoría o 'todos'
 * @param {HTMLElement|null} boton - Elemento botón clickeado
*/
function filtrarProductos(categoria, boton = null) {
    if (boton) {
        const tabs = document.querySelectorAll('.categorias-tabs-home .tab-home');
        tabs.forEach(t => t.classList.remove('activo'));
        boton.classList.add('activo');
    }

    const productos = document.querySelectorAll('.seccion-destacados .grid-productos .producto, .productos .grid-productos .producto');

    productos.forEach(producto => {
        const categoriaProducto = producto.dataset.categoria;
        if (categoria === 'todos' || categoriaProducto == categoria) {
            producto.style.display = '';
        } else {
            producto.style.display = 'none';
        }
    });
}

/**
 * -------------------------------------------------------------
 * SISTEMA DEL CATÁLOGO (/productos): FILTROS, BUSCADOR Y ORDEN
 * -------------------------------------------------------------
 */
let categoriaCatalogoSeleccionada = 'todos';

function iniciarCatalogoPage() {
    const grid = document.querySelector('#gridCatalogo');
    if (!grid) return;

    const buscador = document.querySelector('#buscadorProductos');
    const btnLimpiar = document.querySelector('#btnLimpiarBuscador');

    if (buscador) {
        buscador.addEventListener('input', () => {
            if (btnLimpiar) {
                btnLimpiar.style.display = buscador.value.trim() !== '' ? 'block' : 'none';
            }
            aplicarFiltrosCatalogo();
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            if (buscador) {
                buscador.value = '';
                btnLimpiar.style.display = 'none';
                aplicarFiltrosCatalogo();
            }
        });
    }

    // Comprobar parámetros de la URL para preseleccionar categoría (ej: /productos?categoria=1)
    const urlParams = new URLSearchParams(window.location.search);
    const catParam = urlParams.get('categoria');
    if (catParam) {
        filtrarProductosCatalogo(catParam);
    }
}

function filtrarProductosCatalogo(categoria) {
    categoriaCatalogoSeleccionada = categoria;

    // Actualizar clase activa en los botones de categoría
    const tabs = document.querySelectorAll('.categorias-tabs .tab-categoria');
    tabs.forEach(tab => {
        if (tab.dataset.categoria == categoria) {
            tab.classList.add('activo');
        } else {
            tab.classList.remove('activo');
        }
    });

    aplicarFiltrosCatalogo();
}

function aplicarFiltrosCatalogo() {
    const grid = document.querySelector('#gridCatalogo');
    if (!grid) return;

    const productos = grid.querySelectorAll('.producto');
    const inputBuscador = document.querySelector('#buscadorProductos');
    const query = inputBuscador ? inputBuscador.value.toLowerCase().trim() : '';

    let productosVisibles = 0;

    productos.forEach(producto => {
        const categoriaProducto = producto.dataset.categoria;
        const nombreProducto = (producto.dataset.nombre || '').toLowerCase();

        const coincideCategoria = (categoriaCatalogoSeleccionada === 'todos' || categoriaProducto == categoriaCatalogoSeleccionada);
        const coincideBusqueda = (query === '' || nombreProducto.includes(query));

        if (coincideCategoria && coincideBusqueda) {
            producto.style.display = '';
            productosVisibles++;
        } else {
            producto.style.display = 'none';
        }
    });

    // Actualizar contador
    const contador = document.querySelector('#contadorProductos');
    if (contador) {
        contador.textContent = `Mostrando ${productosVisibles} ${productosVisibles === 1 ? 'producto' : 'productos'}`;
    }

    // Mensaje de sin resultados
    const sinResultados = document.querySelector('#sinResultados');
    if (sinResultados) {
        sinResultados.style.display = productosVisibles === 0 ? 'block' : 'none';
    }
}

function ordenarProductosLista(criterio) {
    const grid = document.querySelector('#gridCatalogo');
    if (!grid) return;

    const productos = Array.from(grid.querySelectorAll('.producto'));

    productos.sort((a, b) => {
        const precioA = parseFloat(a.dataset.precio) || 0;
        const precioB = parseFloat(b.dataset.precio) || 0;
        const nombreA = (a.dataset.nombre || '').toLowerCase();
        const nombreB = (b.dataset.nombre || '').toLowerCase();
        const idA = parseInt(a.dataset.id) || 0;
        const idB = parseInt(b.dataset.id) || 0;

        if (criterio === 'precio-menor') {
            return precioA - precioB;
        } else if (criterio === 'precio-mayor') {
            return precioB - precioA;
        } else if (criterio === 'nombre-az') {
            return nombreA.localeCompare(nombreB);
        } else {
            return idA - idB;
        }
    });

    productos.forEach(p => grid.appendChild(p));
}

function resetearFiltros() {
    const buscador = document.querySelector('#buscadorProductos');
    const btnLimpiar = document.querySelector('#btnLimpiarBuscador');
    if (buscador) buscador.value = '';
    if (btnLimpiar) btnLimpiar.style.display = 'none';
    filtrarProductosCatalogo('todos');
}

/**
 * Control del menú responsive en móviles
 */
function toggleMenuMobile() {
    const nav = document.querySelector('#mainNav');
    if (nav) {
        nav.classList.toggle('menu-abierto');
    }
}

function iniciarMenuMobile() {
    document.addEventListener('click', (e) => {
        const nav = document.querySelector('#mainNav');
        const btn = document.querySelector('#mobileToggleBtn');
        if (!nav || !btn) return;

        if (!nav.contains(e.target) && !btn.contains(e.target) && nav.classList.contains('menu-abierto')) {
            nav.classList.remove('menu-abierto');
        }
    });
}

/**
 * Abre el modal de estado para un pedido en particular
 */
function abrirModalEstado(id, estadoActual) {
    const modal = document.querySelector('#modalEstado');
    const modalPedidoId = document.querySelector('#modalPedidoId');
    const modalInputPedidoId = document.querySelector('#modalInputPedidoId');
    const selectEstado = document.querySelector('#modalSelectEstado');

    if (modal && modalPedidoId && modalInputPedidoId && selectEstado) {
        modalPedidoId.textContent = id;
        modalInputPedidoId.value = id;
        selectEstado.value = estadoActual;
        modal.classList.add('activo');
    }
}

/**
 * Cierra el modal de estado
 */
function cerrarModalEstado() {
    const modal = document.querySelector('#modalEstado');
    if (modal) modal.classList.remove('activo');
}

/**
 * Guarda el nuevo estado del pedido en la base de datos vía API
 */
async function guardarEstadoPedido() {
    const id = document.querySelector('#modalInputPedidoId').value;
    const nuevoEstado = document.querySelector('#modalSelectEstado').value;

    if (!id || !nuevoEstado) return;

    try {
        const respuesta = await fetch('/api/pedidos/estado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: parseInt(id),
                estado: nuevoEstado
            })
        });

        const texto = (await respuesta.text()).trim();
        let resultado;

        try {
            resultado = JSON.parse(texto);
        } catch (e) {
            console.error('La respuesta del servidor no es un JSON válido:', texto);
            mostrarToast('Respuesta inválida del servidor.', 'error');
            return;
        }

        if (resultado.ok) {
            // Actualizar la insignia en la tabla sin necesidad de recargar la página
            const badge = document.querySelector(`#badge-estado-${id}`);
            if (badge) {
                badge.textContent = nuevoEstado;
                const claseEstado = nuevoEstado.toLowerCase().replace(/ /g, '-');
                badge.className = `badge-estado badge-${claseEstado}`;
            }
            mostrarToast('Estado actualizado correctamente.', 'exito');
            cerrarModalEstado();
        } else {
            mostrarToast('No se pudo actualizar el estado.', 'error');
        }
    } catch (error) {
        console.error('Error al actualizar estado:', error);
        mostrarToast('Error de conexión al actualizar estado.', 'error');
    }
}


// Vincular funciones globalmente
/**
 * Control de pestañas y buscador en tiempo real para el Panel de Admin
 */
function cambiarTabAdmin(tab) {
    const btnProductos = document.querySelector('#tabBtnProductos');
    const btnPedidos = document.querySelector('#tabBtnPedidos');
    const panelProductos = document.querySelector('#panelProductos');
    const panelPedidos = document.querySelector('#panelPedidos');

    if (!panelProductos || !panelPedidos) return;

    if (tab === 'pedidos') {
        panelProductos.style.display = 'none';
        panelPedidos.style.display = 'block';
        if (btnProductos) btnProductos.classList.remove('activo');
        if (btnPedidos) btnPedidos.classList.add('activo');
        window.location.hash = 'pedidos';
    } else {
        panelProductos.style.display = 'block';
        panelPedidos.style.display = 'none';
        if (btnProductos) btnProductos.classList.add('activo');
        if (btnPedidos) btnPedidos.classList.remove('activo');
        window.location.hash = 'productos';
    }
}

function iniciarAdminTabsYBuscador() {
    // 1. Revisar hash en la URL (ej: #pedidos)
    if (window.location.hash === '#pedidos') {
        cambiarTabAdmin('pedidos');
    }

    // 2. Buscador en tiempo real de productos en Admin
    const buscador = document.querySelector('#buscadorAdminProductos');
    const btnLimpiar = document.querySelector('#btnLimpiarBuscadorAdmin');
    const tabla = document.querySelector('#tablaProductosAdmin');
    const contador = document.querySelector('#contadorProductosAdmin');
    const filaSinResultados = document.querySelector('#filaSinResultadosAdmin');

    if (buscador && tabla) {
        const filas = tabla.querySelectorAll('tbody tr.fila-producto-admin');

        const filtrarTabla = () => {
            const query = buscador.value.toLowerCase().trim();
            let visibles = 0;

            if (btnLimpiar) {
                btnLimpiar.style.display = query !== '' ? 'block' : 'none';
            }

            filas.forEach(fila => {
                const id = fila.dataset.id || '';
                const nombre = fila.dataset.nombre || '';
                const categoria = fila.querySelector('.col-categoria') ? fila.querySelector('.col-categoria').textContent.toLowerCase() : '';

                if (query === '' || id.includes(query) || nombre.includes(query) || categoria.includes(query)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            if (contador) {
                contador.textContent = `Mostrando ${visibles} ${visibles === 1 ? 'producto' : 'productos'}`;
            }

            if (filaSinResultados) {
                filaSinResultados.style.display = visibles === 0 ? '' : 'none';
            }
        };

        buscador.addEventListener('input', filtrarTabla);

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                buscador.value = '';
                btnLimpiar.style.display = 'none';
                filtrarTabla();
            });
        }
    }
}

/**
 * Alterna el estado destacado de un producto en el Panel de Admin vía API
 */
async function toggleDestacadoProducto(id) {
    const boton = document.querySelector(`#btn-destacado-${id}`);
    if (!boton) return;

    try {
        const respuesta = await fetch('/api/productos/destacado', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: parseInt(id) })
        });

        const resultado = await respuesta.json();
        if (resultado.ok) {
            const esDestacado = resultado.destacado === 1;
            boton.classList.toggle('activo', esDestacado);
            boton.innerHTML = esDestacado 
                ? '<i class="fas fa-star"></i> <span>Destacado</span>' 
                : '<i class="far fa-star"></i> <span>Normal</span>';
            boton.title = esDestacado ? 'Quitar de destacados en la Home' : 'Marcar como destacado en la Home';
            mostrarToast(esDestacado ? '⭐ Producto marcado como destacado en la Home' : 'Producto quitado de destacados', 'exito');
        } else {
            mostrarToast(resultado.mensaje || 'No se pudo actualizar', 'error');
        }
    } catch (e) {
        mostrarToast('Error de conexión', 'error');
    }
}

// Vincular funciones globalmente
window.abrirModalEstado = abrirModalEstado;
window.cerrarModalEstado = cerrarModalEstado;
window.guardarEstadoPedido = guardarEstadoPedido;
window.cambiarTabAdmin = cambiarTabAdmin;
window.toggleDestacadoProducto = toggleDestacadoProducto;

window.toggleCart = toggleCart;
window.filtrarProductos = filtrarProductos;
window.enviarPorWhatsApp = enviarPorWhatsApp;
window.toggleMenuMobile = toggleMenuMobile;
window.filtrarProductosCatalogo = filtrarProductosCatalogo;
window.ordenarProductosLista = ordenarProductosLista;
window.resetearFiltros = resetearFiltros;
window.iniciarLoginTogglePassword = iniciarLoginTogglePassword;

/**
 * Configura la funcionalidad de mostrar u ocultar la contraseña en el login
 */
function iniciarLoginTogglePassword() {
    const btnToggle = document.querySelector('#toggle-password');
    const inputPassword = document.querySelector('#password');
    const iconoOjo = document.querySelector('#icono-ojo');

    if (!btnToggle || !inputPassword || !iconoOjo) return;

    btnToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const esPassword = inputPassword.type === 'password';

        inputPassword.type = esPassword ? 'text' : 'password';

        if (esPassword) {
            iconoOjo.classList.remove('fa-eye');
            iconoOjo.classList.add('fa-eye-slash');
            btnToggle.setAttribute('aria-label', 'Ocultar contraseña');
            btnToggle.setAttribute('title', 'Ocultar contraseña');
        } else {
            iconoOjo.classList.remove('fa-eye-slash');
            iconoOjo.classList.add('fa-eye');
            btnToggle.setAttribute('aria-label', 'Mostrar contraseña');
            btnToggle.setAttribute('title', 'Mostrar contraseña');
        }

        inputPassword.focus();
    });
}




