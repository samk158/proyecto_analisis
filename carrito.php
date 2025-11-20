<?php
session_start();
include('barra_sup.php'); // si te da problema, puedes comentarlo temporalmente
// include('conexion.php'); // por ahora no es necesario para el carrito

// Inicializar carrito en sesión
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejo de acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // ✅ Agregar producto al carrito
    if ($accion === 'agregar') {
        $id       = $_POST['id'] ?? '';
        $nombre   = $_POST['nombre'] ?? '';
        $precio   = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
        $cantidad = isset($_POST['cantidad']) ? max(1, intval($_POST['cantidad'])) : 1;
        $imagen   = $_POST['imagen'] ?? 'imagenes/no-disponible.png';

        if ($id !== '' && $nombre !== '' && $precio > 0) {
            if (isset($_SESSION['carrito'][$id])) {
                // Si ya existe, solo sumamos la cantidad
                $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
            } else {
                $_SESSION['carrito'][$id] = [
                    'nombre'   => $nombre,
                    'precio'   => $precio,
                    'cantidad' => $cantidad,
                    'imagen'   => $imagen
                ];
            }
        }

        header('Location: carrito.php?msg=added');
        exit;
    }

    // ✅ Actualizar cantidades
    if ($accion === 'actualizar') {
        if (isset($_POST['cantidad']) && is_array($_POST['cantidad'])) {
            foreach ($_POST['cantidad'] as $id => $cant) {
                $cant = max(1, intval($cant));
                if (isset($_SESSION['carrito'][$id])) {
                    $_SESSION['carrito'][$id]['cantidad'] = $cant;
                }
            }
        }
        header('Location: carrito.php?msg=updated');
        exit;
    }

    // ✅ Eliminar un producto
    if ($accion === 'eliminar') {
        $id = $_POST['id'] ?? '';
        if ($id !== '' && isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }
        header('Location: carrito.php?msg=removed');
        exit;
    }

    // ✅ Vaciar todo el carrito
    if ($accion === 'vaciar') {
        unset($_SESSION['carrito']);
        $_SESSION['carrito'] = [];
        header('Location: carrito.php?msg=cleared');
        exit;
    }
}

// Calcular total
$totalGeneral = 0;
foreach ($_SESSION['carrito'] as $id => $item) {
    $totalGeneral += $item['precio'] * $item['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carrito de Compras | BoliviaMarket</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f3f4f6;
    margin:0;
    padding-top:120px;
}
.contenedor{
    max-width:1100px;
    margin:0 auto;
    padding:20px;
}
h1{
    text-align:center;
    color:#111827;
    margin-bottom:10px;
}
.sub{
    text-align:center;
    color:#6b7280;
    margin-bottom:25px;
}

/* Tarjeta principal */
.carrito{
    background:white;
    border-radius:18px;
    padding:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

/* Productos */
.item{
    display:flex;
    gap:15px;
    padding:12px 0;
    border-bottom:1px solid #e5e7eb;
    align-items:center;
}
.item:last-child{
    border-bottom:none;
}
.item-img img{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
}
.item-info{
    flex:1;
}
.item-info h3{
    margin:0;
    font-size:1rem;
    color:#111827;
}
.item-info p{
    margin:4px 0;
    color:#6b7280;
    font-size:0.9rem;
}

/* Cantidad */
.cantidad{
    display:flex;
    align-items:center;
    gap:5px;
}
.cantidad input{
    width:55px;
    text-align:center;
    padding:6px 8px;
    border-radius:8px;
    border:1px solid #d1d5db;
}
.cantidad span{
    font-size:0.85rem;
}

/* Botones */
.btn{
    border:none;
    border-radius:999px;
    padding:8px 13px;
    font-size:0.9rem;
    font-weight:600;
    cursor:pointer;
    transition:0.2s;
}
.btn-primario{
    background:#2563eb;
    color:white;
}
.btn-primario:hover{
    background:#1d4ed8;
}
.btn-peligro{
    background:#ef4444;
    color:white;
}
.btn-peligro:hover{
    background:#dc2626;
}
.btn-link{
    background:transparent;
    color:#6b7280;
    padding:0;
}

/* Pie del carrito */
.footer-carrito{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    margin-top:20px;
    flex-wrap:wrap;
    gap:15px;
}
.total{
    font-size:1.1rem;
    color:#111827;
}
.total span{
    font-weight:700;
}

/* Mensaje vacío */
.vacio{
    text-align:center;
    color:#6b7280;
    padding:30px 10px;
}

/* Responsive */
@media(max-width:768px){
    .item{
        flex-direction:column;
        align-items:flex-start;
    }
    .footer-carrito{
        flex-direction:column;
        align-items:flex-start;
    }
}
</style>
</head>
<body>
<div class="contenedor">
    <h1>🛒 Tu carrito</h1>
    <p class="sub">Revisa los productos que añadiste desde BoliviaMarket antes de confirmar tu compra.</p>

    <div class="carrito">
        <?php if(empty($_SESSION['carrito'])): ?>
            <div class="vacio">
                <p>No tienes productos en el carrito.</p>
                <button class="btn btn-primario" onclick="window.location.href='index.php'">⬅ Seguir comprando</button>
            </div>
        <?php else: ?>
            <!-- Formulario para actualizar cantidades -->
            <form method="POST" action="carrito.php">
                <input type="hidden" name="accion" value="actualizar">
                
                <?php foreach($_SESSION['carrito'] as $id => $item): 
                    $nombre = htmlspecialchars($item['nombre']);
                    $precio = number_format($item['precio'],2);
                    $cantidad = intval($item['cantidad']);
                    $subtotal = number_format($item['precio'] * $item['cantidad'],2);
                    $imagen = htmlspecialchars($item['imagen']);
                ?>
                <div class="item">
                    <div class="item-img">
                        <img src="<?= $imagen ?>" alt="<?= $nombre ?>">
                    </div>
                    <div class="item-info">
                        <h3><?= $nombre ?></h3>
                        <p>Precio unidad: <strong>Bs. <?= $precio ?></strong></p>
                        <p>Subtotal: <strong>Bs. <?= $subtotal ?></strong></p>
                    </div>
                    <div>
                        <div class="cantidad">
                            <label>Cant.</label>
                            <input type="number" name="cantidad[<?= $id ?>]" value="<?= $cantidad ?>" min="1">
                        </div>
                        <form method="POST" action="carrito.php" style="margin-top:8px;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $id ?>">
                        </form>
                    </div>
                    <div>
                        <form method="POST" action="carrito.php">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button type="submit" class="btn btn-peligro">🗑 Eliminar</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="footer-carrito">
                    <div class="total">
                        Total del pedido: <span>Bs. <?= number_format($totalGeneral,2) ?></span>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primario">💾 Actualizar cantidades</button>
                    </div>
                </div>
            </form>

            <div class="footer-carrito" style="margin-top:15px; border-top:1px solid #e5e7eb; padding-top:15px;">
                <form method="POST" action="carrito.php">
                    <input type="hidden" name="accion" value="vaciar">
                    <button type="submit" class="btn btn-peligro">🧹 Vaciar carrito</button>
                </form>
                <button class="btn btn-primario" onclick="window.location.href='checkout.php'">
                    ✅ Proceder al pago
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
