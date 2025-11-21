<?php
session_start();
include('conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Hortalizas</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{
    background:#f4f7fb;
    font-family:'Poppins',sans-serif;
    padding:30px;
}
h1{
    text-align:center;
    font-size:2.8em;
    margin-bottom:25px;
    color:#1e293b;
    font-weight:700;
}
.buscador-box{
    max-width:1100px;
    margin:0 auto 30px auto;
    display:flex;
    gap:15px;
}
.buscador-box input{
    flex:1;
    padding:15px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:1.1em;
    background:white;
}
.buscador-box button{
    background:#2563eb;
    color:white;
    border:none;
    border-radius:10px;
    padding:0 25px;
    font-size:1.1em;
    cursor:pointer;
    font-weight:600;
}
.productos-grid{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
    gap:30px;
}
.producto{
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.3s ease;
    text-align:center;
}
.producto:hover{
    transform:translateY(-5px);
    box-shadow:0 8px 18px rgba(0,0,0,0.15);
}
.producto img{
    width:100%;
    height:190px;
    object-fit:cover;
    border-radius:12px;
}
.producto h3{
    margin-top:15px;
    font-size:1.3em;
    font-weight:600;
    color:#1e293b;
}
.producto p{ margin:6px 0; color:#475569;font-size:1em; }
.producto select,
.producto input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #d1d5db;
    margin-top:5px;
    font-size:1em;
}
.btn-add{
    margin-top:12px;
    background:#16a34a;
    color:white;
    border:none;
    padding:12px;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    font-size:1em;
}
</style>
</head>
<body>

<?php include('barra_sup.php'); ?>

<h1>Hortalizas</h1>

<div class="buscador-box">
    <input id="buscador" oninput="filtrarProductos()" placeholder="Buscar producto...">
    <button onclick="window.location.href='carrito.php'">🛒 Ver carrito</button>
</div>

<div class="productos-grid">

<?php
$sql = "SELECT id, nombre, precio_menor, imagen 
        FROM productos 
        WHERE sector='Hortalizas' AND estado='publicado'";
$res = mysqli_query($conexion, $sql);

while($p = mysqli_fetch_assoc($res)): ?>

    <div class="producto" data-filter="<?= strtolower($p['nombre']) ?>">
        <img src="<?= htmlspecialchars($p['imagen']) ?>">
        <h3><?= htmlspecialchars($p['nombre']) ?></h3>
        <p><b>Precio:</b> Bs. <?= number_format($p['precio_menor'],2) ?></p>

        <label>Unidad:</label>
        <select id="unidad_<?= $p['id'] ?>">
            <option value="Unidad">Unidad</option>
        </select>

        <label>Cantidad:</label>
        <input type="number" id="cantidad_<?= $p['id'] ?>" value="1" min="1">

        <button class="btn-add"
            onclick="addToCart('<?= $p['id'] ?>','<?= htmlspecialchars($p['nombre'],ENT_QUOTES) ?>',
            <?= $p['precio_menor'] ?>,'<?= htmlspecialchars($p['imagen'],ENT_QUOTES) ?>')">
            🛒 Agregar al carrito
        </button>
    </div>

<?php endwhile; ?>

</div>

<script>
function filtrarProductos(){
    const f = document.getElementById("buscador").value.toLowerCase();
    document.querySelectorAll(".producto").forEach(p=>{
        p.style.display = p.dataset.filter.includes(f) ? "block" : "none";
    });
}

function addToCart(id,nombre,precio,imagen){
    const cantidad = document.getElementById("cantidad_"+id).value;
    const unidad   = document.getElementById("unidad_"+id).value;

    fetch("carrito.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`accion=agregar&id=${id}&nombre=${encodeURIComponent(nombre)}&precio=${precio}&cantidad=${cantidad}&unidad=${encodeURIComponent(unidad)}&imagen=${encodeURIComponent(imagen)}`
    })
    .then(r=>r.text())
    .then(res=>{
        if(res.trim()==="OK"){
            alert("Producto añadido ✓");
        } else {
            alert("Error al agregar");
            console.log(res);
        }
    });
}
</script>

</body>
</html>
