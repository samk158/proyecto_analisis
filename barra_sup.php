<?php
session_start();
include('conexion.php');

$nombre_usuario = '';
$tipo_usuario = 'cliente';

if (isset($_SESSION['codigo'])) {

    $codigo = $_SESSION['codigo'];

    $sql_cliente = "SELECT nombre FROM clientes WHERE codigo_cliente='$codigo' LIMIT 1";
    $res_cliente = mysqli_query($conexion, $sql_cliente);

    if (mysqli_num_rows($res_cliente) > 0) {
        $row = mysqli_fetch_assoc($res_cliente);
        $nombre_usuario = $row['nombre'];
        $tipo_usuario = 'cliente';
    } else {
        $sql_vendedor = "SELECT nombre FROM vendedores WHERE codigo_vendedor='$codigo' LIMIT 1";
        $res_vendedor = mysqli_query($conexion, $sql_vendedor);

        if (mysqli_num_rows($res_vendedor) > 0) {
            $row = mysqli_fetch_assoc($res_vendedor);
            $nombre_usuario = $row['nombre'];
            $tipo_usuario = 'vendedor';
        }
    }
}
?>

<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Poppins',sans-serif; }

.barra-superior {
    width:100%;
    background: linear-gradient(135deg,#1E3A8A,#2563EB);
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

.logo {
    font-size:1.6em;
    font-weight:700;
    cursor:pointer;
}

.menu-contenedor {
    display:flex;
    align-items:center;
    gap:12px;
}

button {
    padding:6px 12px;
    border:1px solid rgba(255,255,255,0.3);
    border-radius:6px;
    background:rgba(255,255,255,0.1);
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover {
    background:rgba(255,255,255,0.3);
    transform:scale(1.05);
}

.icono-carrito {
    cursor:pointer;
    font-size:1.4em;
    transition:0.3s;
}
.icono-carrito:hover { transform:scale(1.2); }
</style>

<header class="barra-superior">

    <div class="logo" onclick="window.location.href='index.php'">
        <?= $nombre_usuario ? explode(' ', $nombre_usuario)[0] : "Mi Tienda"; ?>
    </div>

    <div class="menu-contenedor" id="menu">

        <?php if($tipo_usuario == 'cliente'): ?>

            <button onclick="location.href='index.php'">Inicio</button>
            <button onclick="location.href='vender.php'">Vender</button>
            <button onclick="location.href='soporte.php'">Soporte</button>
            <button onclick="location.href='ofertas.php'">Ofertas</button>
            <button onclick="location.href='novedades.php'">Novedades</button>
            <button onclick="location.href='categorias.php'">Categorías</button>
            <button onclick="location.href='ayuda.php'">Ayuda</button>

            <?php if($nombre_usuario): ?>
                <button onclick="location.href='ajustes.php'">Ajustes</button>
                <button onclick="location.href='cerrar_sesion.php'">Cerrar sesión</button>
            <?php else: ?>
                <button onclick="location.href='cuenta_cliente.php'">Mi Cuenta</button>
                <button onclick="location.href='registro_cliente.php'">Registrarse</button>
            <?php endif; ?>

            <div class="icono-carrito" onclick="location.href='carrito.php'">🛒</div>

        <?php else: ?>

            <button onclick="location.href='ventas.php'">Gestión Ventas</button>
            <button onclick="location.href='almacen_vendedor.php'">Almacén</button>
            <button onclick="location.href='stock.php'">Stock</button>
            <button onclick="location.href='clientes.php'">Clientes</button>
            <button onclick="location.href='perfil_vendedor.php'">Perfil</button>
            <button onclick="location.href='productos_vendedor.php'">Productos</button>
            <button onclick="location.href='ofertas.php'">Ofertas</button>
            <button onclick="location.href='cerrar_sesion.php'">Cerrar sesión</button>

            <div class="icono-carrito" onclick="location.href='carrito.php'">🛒</div>

        <?php endif; ?>

    </div>
</header>
