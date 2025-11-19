<?php
session_start();
include('conexion.php');

$nombre_usuario = '';
$tipo_usuario = 'cliente'; // Por defecto, cualquier visitante se trata como "cliente"

// Si hay sesión, verificamos si es cliente o vendedor
if(isset($_SESSION['codigo'])){
    $codigo = $_SESSION['codigo'];

    // Verificar clientes
    $sql_cliente = "SELECT nombre FROM clientes WHERE codigo_cliente='$codigo' LIMIT 1";
    $res_cliente = mysqli_query($conexion, $sql_cliente);
    if(mysqli_num_rows($res_cliente) > 0){
        $row = mysqli_fetch_assoc($res_cliente);
        $nombre_usuario = $row['nombre'];
        $tipo_usuario = 'cliente';
    } else {
        // Verificar vendedores
        $sql_vendedor = "SELECT nombre FROM vendedores WHERE codigo_vendedor='$codigo' LIMIT 1";
        $res_vendedor = mysqli_query($conexion, $sql_vendedor);
        if(mysqli_num_rows($res_vendedor) > 0){
            $row = mysqli_fetch_assoc($res_vendedor);
            $nombre_usuario = $row['nombre'];
            $tipo_usuario = 'vendedor';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barra Superior Adaptable</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Poppins', sans-serif; background:#f5f5f5; min-height:100vh; overflow-x:hidden; padding-top:70px; }

.barra-superior {
  width:100%;
  background: linear-gradient(135deg, #1E3A8A, #2563EB);
  color:white;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:12px 24px;
  position:fixed;
  top:0;
  left:0;
  z-index:1000;
  box-shadow:0 4px 10px rgba(0,0,0,0.3);
}

.logo { font-size:1.6em; font-weight:700; cursor:pointer; user-select:none; }

.menu-toggle { display:none; background:none; border:none; color:white; font-size:1.8em; cursor:pointer; }

.menu-contenedor { display:flex; align-items:center; gap:12px; }

button { padding:6px 12px; border:1px solid rgba(255,255,255,0.3); border-radius:6px; background: rgba(255,255,255,0.1); color:white; font-weight:600; cursor:pointer; transition:0.3s; }
button:hover { background: rgba(255,255,255,0.3); transform: scale(1.05); }

input[type="search"] { padding:6px 10px; border-radius:6px; border:none; outline:none; background: rgba(255,255,255,0.15); color:white; }
input::placeholder { color:#ddd; }
input:focus { background: rgba(255,255,255,0.25); }

.icono-carrito { font-size:1.4em; cursor:pointer; transition:transform 0.3s; }
.icono-carrito:hover { transform: scale(1.2); }

/* ------- VISTA MÓVIL ------- */
@media (max-width:850px) {
  .menu-toggle { display:block; }
  .menu-contenedor { 
    display:none; 
    flex-direction:column; 
    align-items:flex-start; 
    background: rgba(30,58,138,0.98); 
    position:fixed; 
    top:60px; 
    left:0; 
    width:100%; 
    height:calc(100vh - 60px); 
    padding:15px; 
    overflow-y:auto; 
    z-index:999; 
  }
  .menu-contenedor.show { display:flex; }
  .menu-contenedor button, .menu-contenedor input { width:100%; margin-bottom:10px; font-size:1em; }
  .menu-contenedor .icono-carrito { margin-bottom:10px; }
}
</style>
</head>
<body>

<header class="barra-superior">
  <div class="logo" onclick="window.location.href='index.php'">
    <?= $nombre_usuario != '' ? htmlspecialchars(explode(' ', $nombre_usuario)[0]) : 'Mi Tienda'; ?>
  </div>

  <button class="menu-toggle" onclick="toggleMenu()">☰</button>

  <div class="menu-contenedor" id="menu">
    <?php if($tipo_usuario == 'cliente'): ?>
        <!-- Menú para clientes o visitantes -->
        <button onclick="window.location.href='index.php'">Inicio</button>
        <button onclick="window.location.href='vender.php'">Vender</button>
        <button onclick="window.location.href='soporte.php'">Soporte</button>
        <button onclick="window.location.href='ofertas.php'">Ofertas</button>
        <button onclick="window.location.href='novedades.php'">Novedades</button>
        <button onclick="window.location.href='categorias.php'">Categorías</button>
        <button onclick="window.location.href='ayuda.php'">Ayuda</button>

        <?php if($nombre_usuario != ''): ?>
            <button onclick="window.location.href='ajustes.php'">Ajustes</button>
            <button onclick="window.location.href='cerrar_sesion.php'">Cerrar sesión</button>
        <?php else: ?>
            <button onclick="window.location.href='cuenta_cliente.php'">Mi Cuenta</button>
            <button onclick="window.location.href='registro_cliente.php'">Registrarse</button>
        <?php endif; ?>

        <div class="icono-carrito" onclick="window.location.href='carrito.php'">🛒</div>
        <input type="search" placeholder="Buscar productos...">
    <?php elseif($tipo_usuario == 'vendedor'): ?>
        <!-- Menú para vendedores -->
         <button onclick="window.location.href='ventas.php'">Gestion de Ventas</button>
        <button onclick="window.location.href='almacen_vendedor.php'">Almacén</button>
        <button onclick="window.location.href='stock.php'">Stock</button>
        <button onclick="window.location.href='clientes.php'">Clientes</button>
        <button onclick="window.location.href='perfil_vendedor.php'">Perfil</button>
        <button onclick="window.location.href='productos_vendedor.php'">Productos</button>
        <button onclick="window.location.href='ofertas.php'">Ofertas</button>
        <button onclick="window.location.href='cerrar_sesion.php'">Cerrar sesión</button>
        <div class="icono-carrito" onclick="window.location.href='carrito.php'">🛒</div>
    <?php endif; ?>
  </div>
</header>

<script>
function toggleMenu() {
  document.getElementById("menu").classList.toggle("show");
}
</script>

</body>
</html>
