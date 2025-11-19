<?php
include('conexion.php');

$sector = $_GET['sector'] ?? '';

if ($sector == '') {
  echo "<p style='text-align:center;'>Seleccione una categoría.</p>";
  exit;
}

$sql = "SELECT p.id, p.nombre, p.precio_menor, p.precio_mayor, p.imagen, p.estado, v.empresa
        FROM productos p
        JOIN vendedores v ON p.id = v.id
        WHERE p.estado = 'publicado' AND p.sector = '$sector'
        ORDER BY v.empresa";

$result = mysqli_query($conexion, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
  echo "<p style='text-align:center;'>No hay productos publicados en esta categoría.</p>";
  exit;
}

$empresas = [];
while ($row = mysqli_fetch_assoc($result)) {
  $empresas[$row['empresa']][] = $row;
}

foreach ($empresas as $empresa => $productos) {
  echo "<div class='empresa-card'>";
  echo "<h3>" . htmlspecialchars($empresa) . "</h3>";
  echo "<div class='productos-grid'>";

  foreach ($productos as $p) {
    $id = htmlspecialchars($p['id']);
    $nombre = htmlspecialchars($p['nombre']);
    $precio = floatval($p['precio_menor']);
    $imagen = htmlspecialchars($p['imagen']);
    if (!file_exists($imagen)) $imagen = "imagenes/no-disponible.png";

    echo "<div class='producto'>";
    echo "<img src='$imagen' alt='$nombre'>";
    echo "<h4>$nombre</h4>";
    echo "<p><b>Precio unidad:</b> Bs. $precio</p>";

    // Controles de cantidad + total dinámico
    echo "
      <div class='cantidad-container'>
        <button class='cantidad-btn' onclick='cambiarCantidad(\"$id\", -1, $precio)'>-</button>
        <input type='number' id='cantidad_$id' class='cantidad-input' value='1' min='1'
               oninput='actualizarTotal(\"$id\", $precio)'>
        <button class='cantidad-btn' onclick='cambiarCantidad(\"$id\", 1, $precio)'>+</button>
      </div>
      <p class='total' id='total_$id'><b>Total:</b> Bs. <span> $precio </span></p>
      <button class='btn-carrito' onclick='agregarAlCarrito(\"$id\", \"$nombre\", $precio)'>🛒 Añadir al carrito</button>
    ";

    echo "</div>";
  }

  echo "</div></div>";
}
?>

<style>
.cantidad-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 8px;
}
.cantidad-btn {
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 6px;
  width: 30px;
  height: 30px;
  font-size: 20px;
  cursor: pointer;
  transition: background 0.2s;
}
.cantidad-btn:hover {
  background-color: #1e40af;
}
.cantidad-input {
  width: 50px;
  text-align: center;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin: 0 5px;
  padding: 3px;
}
.total {
  margin-top: 6px;
  font-size: 15px;
  color: #1e293b;
}
.btn-carrito {
  background-color: #16a34a;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
  transition: background 0.3s, transform 0.2s;
}
.btn-carrito:hover {
  background-color: #15803d;
  transform: translateY(-2px);
}
</style>

<script>
function cambiarCantidad(id, cambio, precio) {
  const input = document.getElementById('cantidad_' + id);
  let valor = parseInt(input.value) || 1;
  valor = Math.max(1, valor + cambio);
  input.value = valor;
  actualizarTotal(id, precio);
}

function actualizarTotal(id, precio) {
  const cantidad = parseFloat(document.getElementById('cantidad_' + id).value) || 1;
  const total = (cantidad * precio).toFixed(2);
  document.querySelector('#total_' + id + ' span').textContent = total;
}

function agregarAlCarrito(id, nombre, precio) {
  const cantidad = parseInt(document.getElementById('cantidad_' + id).value);
  const total = (cantidad * precio).toFixed(2);
  alert(`✅ Producto añadido:\n\n${nombre}\nCantidad: ${cantidad}\nTotal: Bs. ${total}`);
  // Aquí luego puedes hacer una llamada AJAX a "carrito.php" para guardar en sesión o base de datos
}
</script>
