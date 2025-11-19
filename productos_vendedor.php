<?php
session_start();
include('barra_sup.php'); 
include('conexion.php');

if(!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'vendedor'){
    header("Location: login.php");
    exit;
}

$codigo_vendedor = $_SESSION['codigo'];

// Sectores disponibles
$sectores = ['Alimentos','Bebidas','Limpieza','Tecnología','Ropa','Hogar','Juguetes','Papelería','Deportes','Belleza'];

// Agregar o actualizar productos
if(isset($_POST['new_id'])){
    $nombre = mysqli_real_escape_string($conexion,$_POST['nombre']);
    $codigo_producto = mysqli_real_escape_string($conexion,$_POST['codigo_producto']);
    $cantidad = intval($_POST['cantidad']);
    $precio_menor = floatval($_POST['precio_menor']);
    $precio_mayor = floatval($_POST['precio_mayor']);
    $sector = mysqli_real_escape_string($conexion,$_POST['sector']);
    $imagen = '';
    $estado = 'no publicado';

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error']==0){
        $carpeta_subida='uploads/';
        if(!is_dir($carpeta_subida)) mkdir($carpeta_subida,0755,true);
        $nombre_archivo = time().'_'.basename($_FILES['imagen']['name']);
        $ruta_archivo = $carpeta_subida.$nombre_archivo;
        if(move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta_archivo)) $imagen=$ruta_archivo;
    }

    mysqli_query($conexion,"INSERT INTO productos 
        (codigo_vendedor,nombre,codigo_producto,cantidad,precio_menor,precio_mayor,sector,imagen,estado)
        VALUES ('$codigo_vendedor','$nombre','$codigo_producto','$cantidad','$precio_menor','$precio_mayor','$sector','$imagen','$estado')");
    header("Location: productos_vendedor.php");
    exit;
}

if(isset($_POST['update_id'])){
    $id = intval($_POST['update_id']);
    $nombre = mysqli_real_escape_string($conexion,$_POST['nombre']);
    $codigo_producto = mysqli_real_escape_string($conexion,$_POST['codigo_producto']);
    $cantidad = intval($_POST['cantidad']);
    $precio_menor = floatval($_POST['precio_menor']);
    $precio_mayor = floatval($_POST['precio_mayor']);
    $sector = mysqli_real_escape_string($conexion,$_POST['sector']);
    $imagen = $_POST['current_image'];
    $estado = $_POST['estado'] ?? 'no publicado';

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error']==0){
        $carpeta_subida='uploads/';
        if(!is_dir($carpeta_subida)) mkdir($carpeta_subida,0755,true);
        $nombre_archivo = time().'_'.basename($_FILES['imagen']['name']);
        $ruta_archivo = $carpeta_subida.$nombre_archivo;
        if(move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta_archivo)) $imagen=$ruta_archivo;
    }

    mysqli_query($conexion,"UPDATE productos SET 
        nombre='$nombre',
        codigo_producto='$codigo_producto',
        cantidad='$cantidad',
        precio_menor='$precio_menor',
        precio_mayor='$precio_mayor',
        sector='$sector',
        imagen='$imagen',
        estado='$estado'
        WHERE id='$id' AND codigo_vendedor='$codigo_vendedor'");
    header("Location: productos_vendedor.php");
    exit;
}

// Toggle publish
if(isset($_POST['toggle_publish'])){
    $id = intval($_POST['id']);
    $nuevo_estado = $_POST['nuevo_estado'];
    $sector_pub = $_POST['sector_pub'];

    mysqli_query($conexion,"UPDATE productos SET estado='$nuevo_estado', sector='$sector_pub' WHERE id='$id' AND codigo_vendedor='$codigo_vendedor'");
    echo "ok";
    exit;
}

$prodQuery = "SELECT * FROM productos WHERE codigo_vendedor='$codigo_vendedor'";
$prodResult = mysqli_query($conexion,$prodQuery);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Productos | BoliviaMarket</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f9fafb;
    margin: 0;
    padding: 0;
}

/* Contenedor principal */
#productos-content { display: flex; flex-wrap: wrap; gap: 20px; padding: 20px; max-width:1400px; margin:0 auto; }
.section { background: #fff; border-radius:15px; padding:25px; box-shadow:0 4px 25px rgba(0,0,0,0.08); flex:1; min-width:300px; }

/* Formularios */
form { display:flex; flex-direction: column; gap:15px; }
.form-group { display:flex; flex-direction: column; }
label { font-weight:600; margin-bottom:5px; }
input, select { width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:0.95em; }
button { padding:10px 15px; border:none; border-radius:8px; background:#2563eb; color:white; cursor:pointer; transition:0.3s; font-weight:600; }
button:hover { background:#1e40af; }

/* Tabla tradicional */
.table-container { overflow-x:auto; }
table { width:100%; border-collapse:collapse; margin-top:10px; font-size:0.95em; }
th, td { border:1px solid #d1d5db; padding:10px; text-align:center; vertical-align:middle; }
th { background:#f1f3f5; font-weight:600; }
img.product-img { width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #d1d5db; }
.actions button { margin:0 3px; font-size:0.85em; }

/* Tarjetas para móvil */
@media (max-width:768px){
    table, thead, tbody, th, td, tr { display:block; }
    thead tr { display:none; }
    tr { margin-bottom:15px; border-bottom:2px solid #eee; padding-bottom:10px; }
    td { display:flex; justify-content:space-between; align-items:center; padding:8px 5px; }
    td::before { content: attr(data-label); font-weight:600; flex:1; }
    .actions { display:flex; flex-wrap:wrap; gap:5px; justify-content:flex-end; width:100%; margin-top:5px; }
    img.product-img { width:50px; height:50px; }
}

/* Badges de estado */
.badge { display:inline-block; padding:4px 8px; border-radius:8px; font-size:12px; color:white; margin-right:5px; }
.estado-publicado { background:#16a34a; }
.estado-no-publicado { background:#f97316; }

/* Modal */
.modal { display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; }
.modal-content { background:#fff; padding:20px; border-radius:10px; width:90%; max-width:400px; box-shadow:0 4px 25px rgba(0,0,0,0.1); }
.modal-content select, .modal-content button { margin-top:10px; }
</style>
</head>
<body>

<div id="productos-content">

    <!-- Formulario -->
    <div class="section">
        <h2>Registrar Nuevo Producto</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="new_id" value="1">
            <div class="form-group"><label>Nombre:</label><input type="text" name="nombre" required></div>
            <div class="form-group"><label>Código:</label><input type="text" name="codigo_producto" required></div>
            <div class="form-group"><label>Cantidad:</label><input type="number" name="cantidad" min="0" required></div>
            <div class="form-group"><label>Precio Menor:</label><input type="number" step="0.01" name="precio_menor" min="0" required></div>
            <div class="form-group"><label>Precio Mayor:</label><input type="number" step="0.01" name="precio_mayor" min="0" required></div>
            <div class="form-group"><label>Sector:</label>
                <select name="sector" required>
                    <?php foreach($sectores as $sec): ?>
                        <option value="<?= $sec ?>"><?= $sec ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Imagen:</label><input type="file" name="imagen" accept="image/*"></div>
            <button type="submit">➕ Agregar Producto</button>
        </form>
    </div>

    <!-- Tabla de productos -->
    <div class="section">
        <h2>Productos Registrados</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Cantidad</th>
                        <th>Precio Menor</th>
                        <th>Precio Mayor</th>
                        <th>Sector</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($prod=mysqli_fetch_assoc($prodResult)): ?>
                    <tr id="prod_row_<?= $prod['id'] ?>">
                        <td data-label="Imagen"><?php if($prod['imagen']): ?><img src="<?= $prod['imagen'] ?>" class="product-img"><?php else: ?>Sin imagen<?php endif;?></td>
                        <td data-label="Nombre"><?= htmlspecialchars($prod['nombre']) ?></td>
                        <td data-label="Código"><?= htmlspecialchars($prod['codigo_producto']) ?></td>
                        <td data-label="Cantidad"><?= htmlspecialchars($prod['cantidad']) ?></td>
                        <td data-label="Precio Menor"><?= htmlspecialchars($prod['precio_menor']) ?></td>
                        <td data-label="Precio Mayor"><?= htmlspecialchars($prod['precio_mayor']) ?></td>
                        <td data-label="Sector"><?= htmlspecialchars($prod['sector'] ?? '') ?></td>
                        <td data-label="Estado"><span class="badge <?= $prod['estado']=='publicado'?'estado-publicado':'estado-no-publicado' ?>"><?= $prod['estado'] ?? 'no publicado' ?></span></td>
                        <td data-label="Acciones" class="actions">
                            <button onclick="openPublishModal(<?= $prod['id'] ?>,'<?= $prod['estado'] ?? 'no publicado' ?>')">
                                <?= ($prod['estado'] ?? 'no publicado')=='publicado'?'Dejar de publicar':'Publicar' ?>
                            </button>
                            <button onclick="openEditForm(<?= $prod['id'] ?>)">✏️ Modificar</button>
                        </td>
                    </tr>
                    <?php endwhile;?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Publicar -->
<div class="modal" id="publishModal">
    <div class="modal-content">
        <h3>Selecciona el sector para publicar</h3>
        <select id="sectorSelect">
            <?php foreach($sectores as $sec): ?>
            <option value="<?= $sec ?>"><?= $sec ?></option>
            <?php endforeach; ?>
        </select>
        <button onclick="confirmPublish()">Confirmar</button>
        <button onclick="closeModal()">Cancelar</button>
    </div>
</div>

<script>
let currentProdId=0;
let currentEstado='no publicado';

function openPublishModal(id,estado){
    currentProdId=id;
    currentEstado=estado;
    document.getElementById('publishModal').style.display='flex';
}

function closeModal(){
    document.getElementById('publishModal').style.display='none';
}

function confirmPublish(){
    const sector=document.getElementById('sectorSelect').value;
    let nuevo_estado=currentEstado=='publicado'?'no publicado':'publicado';

    const xhr=new XMLHttpRequest();
    xhr.open('POST','productos_vendedor.php',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        if(this.responseText=='ok'){
            document.getElementById('estado_'+currentProdId).innerHTML='<span class="badge '+(nuevo_estado=='publicado'?'estado-publicado':'estado-no-publicado')+'">'+nuevo_estado+'</span>';
            const btn=document.querySelector('#prod_row_'+currentProdId+' .actions button');
            btn.innerText=nuevo_estado=='publicado'?'Dejar de publicar':'Publicar';
            closeModal();
        }
    }
    xhr.send('toggle_publish=1&id='+currentProdId+'&nuevo_estado='+nuevo_estado+'&sector_pub='+sector);
}

function openEditForm(id){
    alert('Función de modificar producto '+id+' (puedes implementar un modal o formulario inline)');
}
</script>

</body>
</html>
