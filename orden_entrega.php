<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['orden']) && !isset($_GET['id_venta'])) {
    echo "<script>alert('No hay orden generada'); window.location='index.php';</script>";
    exit;
}

if (isset($_GET['id_venta'])) {
    $id_venta = (int)$_GET['id_venta'];

    // Datos de venta + cliente
    $sqlV = "SELECT v.*, c.nombre, c.telefono, c.direccion_entrega
             FROM ventas v
             JOIN clientes c ON v.id_cliente = c.id
             WHERE v.id = $id_venta";
    $resV = mysqli_query($conexion, $sqlV);
    $v = mysqli_fetch_assoc($resV);

    // Items
    $items = [];
    $resD = mysqli_query($conexion,
        "SELECT d.*, p.nombre 
         FROM detalle_ventas d
         JOIN productos p ON p.id = d.id_producto
         WHERE d.id_venta = $id_venta");
    while($row = mysqli_fetch_assoc($resD)){
        $items[] = $row;
    }

    $orden = [
        'codigo'   => "ORD".$id_venta,
        'fecha'    => $v['fecha'],
        'cliente'  => $v['nombre'],
        'telefono' => $v['telefono'],
        'direccion'=> $v['direccion_entrega'],
        'estado'   => $v['estado_entrega'],
        'total'    => $v['total'],
        'items'    => $items
    ];

} else {
    // desde sesión
    $orden = $_SESSION['orden'];
    $orden['codigo'] = $orden['codigo'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Orden de Entrega</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f3f4f6;
    margin:0;padding:20px;
}
.container{
    max-width:700px;
    margin:0 auto;
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
}
h2{text-align:center;}
.item{border-bottom:1px solid #ddd;padding:10px 0;}
button{
    margin-top:20px;
    width:100%;
    padding:12px;
    background:#2563eb;
    border:none;
    color:white;
    font-size:16px;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}
</style>
</head>
<body>

<div class="container">
    <h2>📦 Orden de Entrega</h2>

    <p><b>Código:</b> <?= htmlspecialchars($orden['codigo']) ?></p>
    <p><b>Fecha:</b> <?= htmlspecialchars($orden['fecha']) ?></p>
    <p><b>Cliente:</b> <?= htmlspecialchars($orden['cliente']) ?></p>
    <p><b>Teléfono:</b> <?= htmlspecialchars($orden['telefono']) ?></p>
    <p><b>Dirección:</b> <?= nl2br(htmlspecialchars($orden['direccion'])) ?></p>
    <p><b>Estado:</b> <?= htmlspecialchars($orden['estado'] ?? 'Pendiente') ?></p>

    <h3>🛒 Productos</h3>
    <?php foreach($orden['items'] as $item): ?>
        <div class="item">
            <b><?= htmlspecialchars($item['nombre']) ?></b><br>
            Cantidad: <?= $item['cantidad'] ?><br>
            Subtotal: Bs. <?= number_format($item['cantidad'] * $item['precio'],2) ?>
        </div>
    <?php endforeach; ?>

    <h3>Total: <span style="color:green;">Bs. <?= number_format($orden['total'],2) ?></span></h3>

    <button onclick="window.location.href='index.php'">Aceptar y volver al inicio</button>
</div>

</body>
</html>
