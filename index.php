<?php include('conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BoliviaMarket | Compra y Venta de Productos</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* 🔹 ESTILOS GENERALES */
* { margin:0; padding:0; box-sizing:border-box; }
html, body { min-height:100vh; background:#f5f5f5; color:#333; font-family:'Poppins', sans-serif; -webkit-overflow-scrolling: touch; }
body { display:block !important; padding-top:120px; }

/* 🔹 SECCIÓN DE BIENVENIDA */
.bienvenida { text-align:center; margin-top:60px; margin-bottom:60px; padding:0 20px; animation:fadeIn 1.2s ease-in-out; }
.bienvenida h1 { font-size:3em; font-weight:700; color:#1e293b; letter-spacing:-0.5px; }
.bienvenida h1 span { color:#2563eb; }
.bienvenida p { font-size:1.15em; color:#475569; max-width:800px; margin:15px auto 0; line-height:1.6; }
.bienvenida strong { color:#1e40af; }
.bienvenida em { font-style:normal; color:#0f172a; }
@keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

/* 🔹 BOTONES DE CATEGORÍAS */
.menu-categorias { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:25px; width:90%; max-width:1200px; margin:0 auto; padding-bottom:20px; }
.categoria-btn { position:relative; overflow:hidden; height:180px; border-radius:15px; border:none; cursor:pointer; display:flex; align-items:flex-end; justify-content:center; color:white; font-weight:600; font-size:1.2em; text-shadow:1px 1px 4px rgba(0,0,0,0.6); background-color:#2563EB; transition:transform 0.3s, box-shadow 0.3s; }
.categoria-btn:hover { transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.3); }
.categoria-btn .bg-img { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; opacity:0.6; transition:opacity 0.3s, transform 0.3s; z-index:0; }
.categoria-btn:hover .bg-img { opacity:0.85; transform:scale(1.05); }
.categoria-btn .btn-text { position:relative; z-index:1; padding:10px 15px; text-align:center; }

@media (max-width:600px) {
  .bienvenida h1 { font-size:2.2em; }
  .bienvenida p { font-size:1em; }
  .categoria-btn { height:150px; font-size:1em; }
  .menu-categorias { grid-template-columns:1fr; gap:20px; }
}

/* 🔹 MODAL */
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px); justify-content:center; align-items:center; z-index:2000; }
.modal-content { background:#fff; width:90%; max-width:950px; max-height:80vh; overflow-y:auto; border-radius:15px; padding:20px; position:relative; box-shadow:0 10px 30px rgba(0,0,0,0.4); animation:fadeIn 0.3s ease-in-out; }
.close { position:absolute; top:10px; right:15px; font-size:28px; cursor:pointer; color:#888; }
.close:hover { color:#000; }

/* 🔹 PRODUCTOS EN MODAL */
.empresa-card { background-color:#f9fafb; border-radius:12px; margin-bottom:20px; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
.empresa-card h3 { border-bottom:2px solid #e1e1e1; padding-bottom:5px; margin-bottom:10px; color:#1e293b; }
.productos-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:15px; }
.producto { background-color:white; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:10px; text-align:center; transition: transform 0.2s; }
.producto:hover { transform: translateY(-4px); }
.producto img { width:100%; height:130px; object-fit:cover; border-radius:8px; }
.producto h4 { font-size:16px; color:#1f2937; margin:8px 0 4px; }
.producto p { color:#555; font-size:14px; margin:2px 0; }

/* 🔹 CANTIDAD Y CARRITO */
.cantidad-container { display:flex; align-items:center; justify-content:center; margin-top:8px; }
.cantidad-btn { background-color:#2563eb; color:white; border:none; border-radius:6px; width:30px; height:30px; font-size:20px; cursor:pointer; margin:0 2px; }
.cantidad-btn:hover { background-color:#1e40af; }
.cantidad-input { width:50px; text-align:center; font-size:16px; border:1px solid #ccc; border-radius:6px; margin:0 5px; padding:3px; }
.total { margin-top:6px; font-size:15px; color:#1e293b; }
.btn-carrito { background-color:#16a34a; color:white; border:none; padding:8px 12px; border-radius:8px; font-weight:600; cursor:pointer; margin-top:10px; transition:background 0.3s, transform 0.2s; }
.btn-carrito:hover { background-color:#15803d; transform:translateY(-2px); }

</style>
</head>
<body>

<?php include('barra_sup.php'); ?>

<div class="bienvenida">
  <h1>Bienvenido a <span>BoliviaMarket</span> 🇧🇴</h1>
  <p>
    La plataforma digital que conecta <strong>productos bolivianos</strong> con todo el país.
    Descubre artículos únicos desde la <em>Amazonía</em> hasta el <em>Altiplano</em>, con confianza y calidad.
    <br><br><strong>Compra, apoya y haz crecer el comercio nacional.</strong>
  </p>
</div>

<!-- 🔹 BOTONES DE CATEGORÍAS -->
<div class="menu-categorias">
  <button class="categoria-btn" onclick="mostrarProductos('alimentos')"><img src="imagenes/comida.jpg" class="bg-img"><span class="btn-text">Alimentos</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('bebidas')"><img src="imagenes/bebida.jpg" class="bg-img"><span class="btn-text">Bebidas</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('herramientas')"><img src="imagenes/herramientas.jpg" class="bg-img"><span class="btn-text">Herramientas y Ferretería</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('hogar')"><img src="imagenes/limpieza.jpg" class="bg-img"><span class="btn-text">Hogar y Muebles</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('tecnologia')"><img src="imagenes/tecnologia.jpeg" class="bg-img"><span class="btn-text">Tecnología y Electrónica</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('ropa')"><img src="imagenes/ropa.jpg" class="bg-img"><span class="btn-text">Ropa y Accesorios</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('deportes')"><img src="imagenes/deportes.jpg" class="bg-img"><span class="btn-text">Deportes y Aire Libre</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('juguetes')"><img src="imagenes/juguetes.jpg" class="bg-img"><span class="btn-text">Juguetes y Entretenimiento</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('belleza')"><img src="imagenes/cosmeticos.jpg" class="bg-img"><span class="btn-text">Belleza y Cuidado Personal</span></button>
  <button class="categoria-btn" onclick="mostrarProductos('autos')"><img src="imagenes/auto.jpg" class="bg-img"><span class="btn-text">Autos y Motocicletas</span></button>
</div>

<!-- 🔹 MODAL -->
<div id="modalProductos" class="modal" onclick="cerrarModal(event)">
  <div class="modal-content" onclick="event.stopPropagation()">
    <span class="close" onclick="cerrarModal()">&times;</span>
    <div id="contenidoProductos">
      <p style="text-align:center;">Seleccione una categoría.</p>
    </div>
  </div>
</div>

<!-- 🔹 JAVASCRIPT -->
<script>
function mostrarProductos(sector) {
  const modal = document.getElementById('modalProductos');
  const cont = document.getElementById('contenidoProductos');
  cont.innerHTML = "<p style='text-align:center;'>Cargando productos...</p>";
  modal.style.display = 'flex';

  fetch('productos_por_sector.php?sector=' + sector)
    .then(res => res.text())
    .then(data => cont.innerHTML = data)
    .catch(() => cont.innerHTML = "<p>Error al cargar productos.</p>");
}

function cerrarModal() {
  document.getElementById('modalProductos').style.display = 'none';
}

// BOTONES DE CANTIDAD Y CARRITO
document.addEventListener('click', function(e) {
  // BOTONES - / +
  if (e.target.classList.contains('cantidad-btn')) {
    const id = e.target.dataset.id;
    const precio = parseFloat(e.target.dataset.precio);
    const accion = e.target.dataset.accion;
    const input = document.getElementById('cantidad_' + id);
    let valor = parseInt(input.value) || 1;
    if (accion === '+') valor += 1;
    if (accion === '-') valor = Math.max(1, valor - 1);
    input.value = valor;
    actualizarTotal(id, precio);
  }

  // BOTÓN AÑADIR AL CARRITO
  if (e.target.classList.contains('btn-carrito')) {
    const id = e.target.dataset.id;
    const nombre = e.target.dataset.nombre;
    const precio = parseFloat(e.target.dataset.precio);
    const cantidad = parseInt(document.getElementById('cantidad_' + id).value);
    const total = (cantidad * precio).toFixed(2);
    alert(`✅ Producto añadido:\n\n${nombre}\nCantidad: ${cantidad}\nTotal: Bs. ${total}`);
  }
});

// ACTUALIZAR TOTAL
function actualizarTotal(id, precio) {
  const cantidad = parseInt(document.getElementById('cantidad_' + id).value) || 1;
  const total = (cantidad * precio).toFixed(2);
  document.querySelector('#total_' + id + ' span').textContent = total;
}
</script>

</body>
</html>
