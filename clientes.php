<?php
session_start();
include('barra_sup.php'); // ✅ No tocar
include('conexion.php');

// Validar que sea vendedor
if(!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'vendedor'){
    header("Location: login.php");
    exit;
}

$id = $_SESSION['codigo']; // ID del vendedor

// Registrar nuevo cliente
if(isset($_POST['new_cliente'])){
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);

    // Verificar si el cliente ya existe
    $check = mysqli_query($conexion,"SELECT * FROM clientes WHERE correo='$correo'");
    if(mysqli_num_rows($check) > 0){
        $error = "El cliente ya está registrado.";
    } else {
        mysqli_query($conexion,"INSERT INTO clientes (nombre, correo, telefono) VALUES ('$nombre','$correo','$telefono')");
        $success = "Cliente registrado correctamente.";
    }
}

// Editar cliente
if(isset($_POST['edit_cliente'])){
    $id_cliente = intval($_POST['edit_cliente']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);

    mysqli_query($conexion,"UPDATE clientes SET nombre='$nombre', correo='$correo', telefono='$telefono' WHERE id='$id_cliente'");
    $success = "Cliente actualizado correctamente.";
}

// Obtener todos los clientes
$clientesQuery = "SELECT * FROM clientes ORDER BY nombre ASC";
$clientesResult = mysqli_query($conexion, $clientesQuery);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clientes | BoliviaMarket</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f9fafb; margin:0; padding:20px; }
h2 { color:#1e293b; text-align:center; margin-bottom:15px; }
.section { background:#fff; border-radius:15px; padding:20px; margin-bottom:20px; box-shadow:0 4px 25px rgba(0,0,0,0.08); }
form { display:flex; flex-direction:column; gap:12px; }
input { padding:10px; border:1px solid #d1d5db; border-radius:8px; }
button { padding:10px; border:none; border-radius:8px; background:#2563eb; color:white; font-weight:600; cursor:pointer; transition:0.3s; }
button:hover { background:#1e40af; }
.table-container { overflow-x:auto; }
table { width:100%; border-collapse:collapse; }
th,td { border:1px solid #d1d5db; padding:10px; text-align:center; }
th { background:#f1f3f5; font-weight:600; }
/* Mensajes */
.success { background:#16a34a; color:white; padding:10px; border-radius:6px; margin-bottom:10px; text-align:center; }
.error { background:#dc2626; color:white; padding:10px; border-radius:6px; margin-bottom:10px; text-align:center; }
/* Responsive móvil */
@media(max-width:768px){
    table,th,td { font-size:0.85em; }
    th:nth-child(2), td:nth-child(2) { display:none; } /* ocultar correo en móvil */
}
</style>
</head>
<body>

<h2>Gestión de Clientes</h2>

<?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

<!-- Registrar nuevo cliente -->
<div class="section">
<h3>Registrar Nuevo Cliente</h3>
<form method="POST">
    <input type="hidden" name="new_cliente" value="1">
    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="text" name="telefono" placeholder="Teléfono" required>
    <button type="submit">➕ Registrar Cliente</button>
</form>
</div>

<!-- Tabla clientes -->
<div class="section">
<h3>Clientes Registrados</h3>
<div class="table-container">
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
        </tr>
    </thead>
    <tbody>
        <?php while($cliente=mysqli_fetch_assoc($clientesResult)): ?>
        <tr onclick="openEditForm(<?= $cliente['id'] ?>,'<?= htmlspecialchars($cliente['nombre']) ?>','<?= htmlspecialchars($cliente['correo']) ?>','<?= htmlspecialchars($cliente['telefono']) ?>')" style="cursor:pointer;">
            <td><?= htmlspecialchars($cliente['nombre']) ?></td>
            <td><?= htmlspecialchars($cliente['correo']) ?></td>
            <td><?= htmlspecialchars($cliente['telefono']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
</div>

<!-- Modal Editar Cliente -->
<div id="editModal" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
<div style="background:#fff;padding:20px;border-radius:10px;max-width:400px;width:90%;">
<h3>Editar Cliente</h3>
<form method="POST" id="editForm">
    <input type="hidden" name="edit_cliente" id="edit_id">
    <input type="text" name="nombre" id="edit_nombre" placeholder="Nombre completo" required>
    <input type="email" name="correo" id="edit_correo" placeholder="Correo electrónico" required>
    <input type="text" name="telefono" id="edit_telefono" placeholder="Teléfono" required>
    <button type="submit">💾 Guardar Cambios</button>
    <button type="button" onclick="closeEditModal()" style="background:#dc2626;">Cancelar</button>
</form>
</div>
</div>

<script>
function openEditForm(id,nombre,correo,telefono){
    document.getElementById('edit_id').value=id;
    document.getElementById('edit_nombre').value=nombre;
    document.getElementById('edit_correo').value=correo;
    document.getElementById('edit_telefono').value=telefono;
    document.getElementById('editModal').style.display='flex';
}
function closeEditModal(){
    document.getElementById('editModal').style.display='none';
}
</script>

</body>
</html>
