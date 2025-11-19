<?php
session_start();
include('barra_sup.php'); // ✅ No tocar
include('conexion.php');

// Validar que sea vendedor
if(!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'vendedor'){
    header("Location: login.php");
    exit;
}

// Obtener datos del vendedor
$codigo = $_SESSION['codigo'];
$query = "SELECT * FROM vendedores WHERE codigo_vendedor='$codigo'";
$result = mysqli_query($conexion, $query);
$vendedor = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil Vendedor | BoliviaMarket</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* 🔹 RESET & TIPOGRAFÍA */
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#f4f6f8; padding-top:110px; color:#1e293b; }

/* 🔹 CONTENEDOR PRINCIPAL */
.container { max-width:1400px; margin:0 auto; display:flex; flex-wrap:wrap; gap:20px; padding:20px; }

/* 🔹 SECCIONES */
.section { background:#fff; border-radius:15px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,0.08); }
.section h2 { margin-bottom:20px; font-size:1.4em; color:#111827; }

/* 🔹 PERFIL */
img.profile-pic { width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:3px solid #2563EB; }
.profile-info p { margin:6px 0; font-size:14px; }

/* 🔹 BOTONES */
button { padding:10px 18px; border:none; border-radius:10px; cursor:pointer; font-weight:600; transition:0.3s; }
button:hover { transform:translateY(-2px); }

button.edit-btn { background:#2563EB; color:white; }
button.edit-btn:hover { background:#1e40af; }

button.delete-btn { background:#ef4444; color:white; }
button.delete-btn:hover { background:#b91c1c; }

button.add-btn { background:#16a34a; color:white; }
button.add-btn:hover { background:#15803d; }

/* 🔹 TABLAS */
table { width:100%; border-collapse:collapse; margin-top:10px; font-size:14px; }
table th, table td { padding:10px; text-align:center; border-bottom:1px solid #e5e7eb; }
table th { background:#f3f4f6; font-weight:600; }
table tr:hover { background:#f1f5f9; }

/* 🔹 CLIENTES */
ul.client-list { list-style:none; padding:0; }
ul.client-list li { display:flex; align-items:center; margin-bottom:12px; background:#f9fafb; padding:6px 10px; border-radius:8px; }
ul.client-list li img { width:40px; height:40px; border-radius:50%; object-fit:cover; margin-right:10px; }

/* 🔹 FLEX */
.flex-column { display:flex; flex-direction:column; gap:15px; }

/* 🔹 MAPA */
.map-container { width:100%; height:250px; border-radius:12px; margin-top:10px; }

/* 🔹 CHAT FLOTANTE */
.chat-float {
    position: fixed;
    bottom:20px;
    right:20px;
    width:320px;
    max-height:420px;
    background:#fff;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    display:flex;
    flex-direction:column;
    overflow:hidden;
    z-index:9999;
}

.chat-header {
    background:#2563EB;
    color:#fff;
    padding:12px 15px;
    cursor:pointer;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.chat-header h3 { font-size:16px; }
.chat-header .toggle-btn { font-size:18px; cursor:pointer; }

.chat-content {
    flex:1;
    padding:10px;
    background:#f1f3f5;
    display:flex;
    flex-direction:column;
    overflow-y:auto;
}

.message {
    max-width:75%;
    padding:8px 12px;
    border-radius:15px;
    margin-bottom:8px;
    font-size:14px;
    word-wrap:break-word;
}

.message.sent { background:#2563EB; color:#fff; align-self:flex-end; border-bottom-right-radius:0; }
.message.received { background:#e5e5ea; color:#111827; align-self:flex-start; border-bottom-left-radius:0; }

.chat-input-container {
    display:flex;
    border-top:1px solid #d1d5db;
}

.chat-input-container input {
    flex:1;
    padding:10px;
    border:none;
    outline:none;
    font-size:14px;
}

.chat-input-container button { background:#2563EB; color:white; border:none; padding:10px 15px; cursor:pointer; transition:0.3s; }
.chat-input-container button:hover { background:#1e40af; }

/* 🔹 RESPONSIVE */
@media(max-width:1200px){ .container{flex-direction:column;} }
@media(max-width:768px){
    .chat-float{ width:90%; bottom:10px; right:5%; max-height:350px; }
}
</style>

<!-- Google Maps -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA_Mw4zV5gxNVKldh34UeQIzzidnmvLx7c"></script>
<script>
let map, marker;
function initMap() {
    const defaultLocation = { 
        lat: parseFloat(<?= json_encode($vendedor['latitud']) ?>), 
        lng: parseFloat(<?= json_encode($vendedor['longitud']) ?>) 
    };
    map = new google.maps.Map(document.getElementById("map"), { center: defaultLocation, zoom: 13 });
    marker = new google.maps.Marker({ position: defaultLocation, map: map, draggable:true, title:"Ubicación de tu tienda" });
    google.maps.event.addListener(marker, 'dragend', function(){
        document.getElementById('latitud').value = marker.getPosition().lat();
        document.getElementById('longitud').value = marker.getPosition().lng();
    });
}
</script>
</head>
<body onload="initMap()">

<div class="container">

    <!-- PERFIL -->
    <div style="flex:1; min-width:250px;">
        <div class="section flex-column">
            <img src="<?= $vendedor['foto_perfil'] ?? 'default.png' ?>" alt="Perfil" class="profile-pic">
            <h3><?= htmlspecialchars($vendedor['nombre']) ?></h3>
            <div class="profile-info">
                <p><strong>Tel:</strong> <?= htmlspecialchars($vendedor['telefono']) ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($vendedor['correo']) ?></p>
                <p><strong>Sectores:</strong> <?= htmlspecialchars($vendedor['sector']) ?></p>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($vendedor['descripcion']) ?></p>
            </div>
            <div id="map" class="map-container"></div>
            <input type="hidden" id="latitud" value="<?= $vendedor['latitud'] ?>" name="latitud">
            <input type="hidden" id="longitud" value="<?= $vendedor['longitud'] ?>" name="longitud">
        </div>
    </div>

    <!-- PRODUCTOS Y PEDIDOS -->
    <div style="flex:2; min-width:500px; display:flex; flex-direction:column; gap:20px;">

        <!-- PRODUCTOS -->
        <div class="section">
            <h2>Productos</h2>
            <button class="add-btn" onclick="window.location.href='nuevo_producto.php'">➕ Agregar Producto</button>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio Mayor</th>
                        <th>Precio Menor</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $prodQuery = "SELECT * FROM productos WHERE codigo_vendedor='$codigo'";
                    $prodResult = mysqli_query($conexion, $prodQuery);
                    while($prod = mysqli_fetch_assoc($prodResult)){
                        echo "<tr>
                                <td>".htmlspecialchars($prod['nombre'])."</td>
                                <td>".htmlspecialchars($prod['precio_mayor'])."</td>
                                <td>".htmlspecialchars($prod['precio_menor'])."</td>
                                <td>".htmlspecialchars($prod['stock'])."</td>
                                <td>
                                    <button class='edit-btn' onclick=\"window.location.href='editar_producto.php?id=".$prod['id']."'\">✏️ Editar</button>
                                    <button class='delete-btn' onclick=\"window.location.href='eliminar_producto.php?id=".$prod['id']."'\">🗑️ Eliminar</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- PEDIDOS -->
        <div class="section">
            <h2>Pedidos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $pedidosQuery = "SELECT * FROM pedidos WHERE codigo_vendedor='$codigo' ORDER BY fecha DESC";
                    $pedidosResult = mysqli_query($conexion, $pedidosQuery);
                    while($pedido = mysqli_fetch_assoc($pedidosResult)){
                        echo "<tr>
                                <td>".$pedido['id']."</td>
                                <td>".$pedido['cliente']."</td>
                                <td>".$pedido['estado']."</td>
                                <td>".$pedido['fecha']."</td>
                                <td>
                                    <button class='edit-btn' onclick=\"window.location.href='detalle_pedido.php?id=".$pedido['id']."'\">Ver</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CLIENTES RECIENTES -->
    <div style="flex:1; min-width:250px;">
        <div class="section">
            <h2>Clientes Recientes 🛒</h2>
            <ul class="client-list">
                <?php
                $clientesQuery = "SELECT c.nombre, c.foto, COUNT(p.id) as total_pedidos
                                  FROM clientes c
                                  JOIN pedidos p ON c.codigo_cliente = p.codigo_cliente
                                  WHERE p.codigo_vendedor='$codigo'
                                  GROUP BY c.codigo_cliente
                                  ORDER BY MAX(p.fecha) DESC
                                  LIMIT 10";
                $clientesResult = mysqli_query($conexion, $clientesQuery);
                while($cliente = mysqli_fetch_assoc($clientesResult)){
                    echo "<li>
                            <img src='".($cliente['foto'] ?? 'default_user.png')."'>
                            <div>
                                <strong>".htmlspecialchars($cliente['nombre'])."</strong><br>
                                Pedidos: ".$cliente['total_pedidos']."
                            </div>
                          </li>";
                }
                ?>
            </ul>
        </div>
    </div>

</div>

<!-- CHAT FLOTANTE -->
<div class="chat-float" id="chat-float">
    <div class="chat-header" onclick="toggleChat()">
        <h3>Chat Clientes</h3>
        <span class="toggle-btn" id="toggle-btn">−</span>
    </div>
    <div class="chat-content" id="chat-box">
        <p class="message received">💬 Selecciona un pedido para iniciar el chat.</p>
    </div>
    <div class="chat-input-container">
        <input type="text" id="chat-input" placeholder="Escribe un mensaje...">
        <button onclick="enviarMensaje()">Enviar</button>
    </div>
</div>

<script>
function enviarMensaje(){
    const mensaje = document.getElementById('chat-input').value;
    if(mensaje.trim() !== ''){
        const chatBox = document.getElementById('chat-box');
        const p = document.createElement('div');
        p.className = 'message sent';
        p.textContent = mensaje;
        chatBox.appendChild(p);
        document.getElementById('chat-input').value='';
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

// Minimizar chat
function toggleChat(){
    const chatContent = document.querySelector('.chat-content');
    const chatInput = document.querySelector('.chat-input-container');
    const toggleBtn = document.getElementById('toggle-btn');

    if(chatContent.style.display === 'none'){
        chatContent.style.display = 'flex';
        chatInput.style.display = 'flex';
        toggleBtn.textContent = '−';
    } else {
        chatContent.style.display = 'none';
        chatInput.style.display = 'none';
        toggleBtn.textContent = '+';
    }
}
</script>

</body>
</html>
