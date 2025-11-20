<?php
session_start();

if (!isset($_SESSION['orden'])) {
    echo "<script>alert('No hay orden generada'); window.location='index.php';</script>";
    exit;
}

$orden = $_SESSION['orden'];
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
.item{
    border-bottom:1px solid #ddd;
    padding:10px 0;
}
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

    <p><b>Código:</b> <?= $orden['codigo'] ?></p>
    <p><b>Fecha:</b> <?= $orden['fecha'] ?></p>
    <p><b>Cliente:</b> <?= htmlspecialchars($orden['cliente']) ?></p>
    <p><b>Teléfono:</b> <?= htmlspecialchars($orden['telefono']) ?></p>
    <p><b>Dirección:</b> <?= nl2br(htmlspecialchars($orden['direccion'])) ?></p>
    <p><b>Estado:</b> <?= $orden['estado'] ?></p>

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
