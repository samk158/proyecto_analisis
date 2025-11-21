<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<script>alert('Tu carrito está vacío'); window.location='carrito.php';</script>";
    exit;
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Procesar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $modoEntrega = $_POST['modo'];

    // si es delivery, dirección del formulario; si no, texto fijo
    $direccion_form = ($modoEntrega === "delivery")
        ? mysqli_real_escape_string($conexion, $_POST['direccion'])
        : "Recojo en tienda - SIN DIRECCIÓN";

    // 1) Obtener o crear cliente
    if (isset($_SESSION['id_cliente'])) {
        $id_cliente = (int)$_SESSION['id_cliente'];
    } else {
        $codigo_cliente = "cli".time();

        $sqlNuevo = "INSERT INTO clientes (nombre, telefono, correo, codigo_cliente, direccion_entrega)
                     VALUES ('$nombre','$telefono','','$codigo_cliente','$direccion_form')";
        mysqli_query($conexion, $sqlNuevo);
        $id_cliente = mysqli_insert_id($conexion);

        $_SESSION['id_cliente'] = $id_cliente;
        $_SESSION['codigo']     = $codigo_cliente;
    }

    // 2) Insertar venta
    $sqlVenta = "INSERT INTO ventas (id_cliente, total, estado, estado_entrega)
                 VALUES ($id_cliente, $total, 'pendiente', 'Pendiente')";
    mysqli_query($conexion, $sqlVenta);
    $id_venta = mysqli_insert_id($conexion);

    // 3) Insertar detalle_ventas + seguimiento_pedidos
    $codigo_cliente = $_SESSION['codigo'] ?? '';

    foreach ($_SESSION['carrito'] as $id_prod => $it) {

        $id_prod   = (int)$id_prod;
        $cantidad  = (int)$it['cantidad'];
        $precio    = (float)$it['precio'];

        mysqli_query($conexion,
            "INSERT INTO detalle_ventas (id_venta,id_producto,cantidad,precio)
             VALUES ($id_venta,$id_prod,$cantidad,$precio)");

        // obtener vendedor del producto
        $resP = mysqli_query($conexion,
            "SELECT codigo_vendedor FROM productos WHERE id=$id_prod LIMIT 1");
        $rowP = mysqli_fetch_assoc($resP);
        $codigo_vendedor = $rowP['codigo_vendedor'] ?? '';

        mysqli_query($conexion,
            "INSERT INTO seguimiento_pedidos (id_venta,id_producto,codigo_vendedor,codigo_cliente,estado)
             VALUES ($id_venta,$id_prod,'$codigo_vendedor','$codigo_cliente','Pendiente')");
    }

    // 4) Guardar orden en sesión (para mostrar bonito)
    $_SESSION['orden'] = [
        'id_venta'  => $id_venta,
        'codigo'    => "ORD".$id_venta,
        'fecha'     => date("Y-m-d H:i:s"),
        'cliente'   => $nombre,
        'telefono'  => $telefono,
        'direccion' => $direccion_form,
        'modo'      => $modoEntrega,
        'items'     => $_SESSION['carrito'],
        'total'     => $total,
        'estado'    => 'PENDIENTE'
    ];

    // Vaciar carrito
    $_SESSION['carrito'] = [];

    header("Location: orden_entrega.php?id_venta=".$id_venta);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Checkout | BoliviaMarket</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f3f4f6;margin:0;padding:20px;}
.container{
    max-width:700px;margin:0 auto;background:white;
    padding:25px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,0.1);
}
h2{text-align:center;margin-bottom:10px;}
h3{margin-top:25px;}
.item{border-bottom:1px solid #ddd;padding:10px 0;}
label{font-weight:600;}
input, textarea{
    width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-top:5px;
}
button{
    margin-top:20px;width:100%;padding:12px;background:#2563eb;border:none;
    color:white;font-size:16px;border-radius:10px;cursor:pointer;font-weight:600;
}
button:hover{background:#1e40af;}
.radio-box{display:flex;gap:20px;margin-top:10px;}
.opcion{display:flex;align-items:center;gap:8px;font-weight:600;}
</style>
</head>
<body>

<div class="container">
    <h2>Confirmar Pedido</h2>

    <h3>🛒 Productos</h3>
    <?php foreach($_SESSION['carrito'] as $item): ?>
        <div class="item">
            <strong><?= htmlspecialchars($item['nombre']) ?></strong><br>
            Cantidad: <?= $item['cantidad'] ?><br>
            Subtotal: Bs. <?= number_format($item['precio'] * $item['cantidad'],2) ?>
        </div>
    <?php endforeach; ?>

    <h3>Total a pagar: <span style="color:green;">Bs. <?= number_format($total,2) ?></span></h3>

    <h3>📦 Método de entrega</h3>

    <form method="POST">
        <div class="radio-box">
            <label class="opcion">
                <input type="radio" name="modo" value="delivery" checked onclick="mostrarDireccion(true)">
                Delivery
            </label>
            <label class="opcion">
                <input type="radio" name="modo" value="tienda" onclick="mostrarDireccion(false)">
                Recojo en tienda
            </label>
        </div>

        <h3>👤 Datos del cliente</h3>

        <label>Nombre completo</label>
        <input type="text" name="nombre" required>

        <label>Teléfono</label>
        <input type="text" name="telefono" required>

        <div id="cajaDireccion">
            <label>Dirección de entrega</label>
            <textarea name="direccion" required></textarea>
        </div>

        <button type="submit">Confirmar Pedido</button>
    </form>
</div>

<script>
function mostrarDireccion(mostrar){
    document.getElementById("cajaDireccion").style.display = mostrar ? "block" : "none";
}
</script>

</body>
</html>
