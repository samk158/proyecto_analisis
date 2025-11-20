<?php
session_start();

// Si el carrito está vacío, regresar
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<script>alert('Tu carrito está vacío'); window.location='carrito.php';</script>";
    exit;
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Procesar pedido (solo interfaz)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigoOrden = "ORD" . rand(1000, 9999);

    $modoEntrega = $_POST['modo'];

    $direccion = ($modoEntrega === "delivery") ? $_POST['direccion'] : "Recojo en tienda - SIN DIRECCIÓN";

    $_SESSION['orden'] = [
        'codigo' => $codigoOrden,
        'fecha' => date("Y-m-d H:i:s"),
        'cliente' => $_POST['nombre'],
        'telefono' => $_POST['telefono'],
        'direccion' => $direccion,
        'modo' => $modoEntrega,
        'items' => $_SESSION['carrito'],
        'total' => $total,
        'estado' => 'PENDIENTE'
    ];

    // Vaciar carrito
    $_SESSION['carrito'] = [];

    header("Location: orden_entrega.php");
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
body{
    font-family:'Poppins',sans-serif;
    background:#eef2f7;
    margin:0;
    padding:30px;
}

.container{
    max-width:750px;
    margin:0 auto;
    background:white;
    padding:35px;
    border-radius:18px;
    box-shadow:0 10px 35px rgba(0,0,0,0.12);
}

h2{
    text-align:center;
    font-size:2rem;
    margin-bottom:20px;
    color:#1e293b;
}

h3{
    margin-top:25px;
    color:#1e293b;
}

.item{
    border-bottom:1px solid #ddd;
    padding:10px 0;
}

label{
    font-weight:600;
}

input, textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    margin-top:5px;
    font-size:1rem;
    outline:none;
}

input:focus, textarea:focus{
    border-color:#2563eb;
    box-shadow:0 0 5px rgba(37,99,235,0.4);
}

button{
    margin-top:25px;
    width:100%;
    padding:15px;
    background:#2563eb;
    border:none;
    color:white;
    font-size:17px;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    transition:0.2s;
}

button:hover{
    background:#1e40af;
}

.radio-box{
    display:flex;
    gap:25px;
    margin-top:10px;
}

.opcion{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:600;
}

#cajaDireccion{
    margin-top:10px;
}
</style>
</head>
<body>

<div class="container">
    <h2>Confirmar Pedido</h2>

    <!-- Productos -->
    <h3>🛒 Productos</h3>
    <?php foreach($_SESSION['carrito'] as $item): ?>
        <div class="item">
            <strong><?= htmlspecialchars($item['nombre']) ?></strong><br>
            Cantidad: <?= $item['cantidad'] ?><br>
            Subtotal: Bs. <?= number_format($item['precio'] * $item['cantidad'],2) ?>
        </div>
    <?php endforeach; ?>

    <h3>Total a pagar: <span style="color:green; font-weight:bold;">Bs. <?= number_format($total,2) ?></span></h3>

    <!-- Método de Entrega -->
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

        <!-- Datos del Cliente -->
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
    const caja = document.getElementById("cajaDireccion");
    const direccion = caja.querySelector("textarea");

    if(mostrar){
        caja.style.display = "block";
        direccion.required = true;
    } else {
        caja.style.display = "none";
        direccion.required = false;
    }
}
</script>

</body>
</html>
