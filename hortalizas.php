<?php
session_start();

/* -------------------------------
   AGREGAR PRODUCTO (AJAX)
-------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'agregar') {

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $id       = $_POST['id'];
    $nombre   = $_POST['nombre'];
    $precio   = floatval($_POST['precio']);
    $cantidad = intval($_POST['cantidad']);
    $unidad   = $_POST['unidad'];
    $imagen   = $_POST['imagen'];

    if (!isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] = [
            "nombre"   => $nombre,
            "precio"   => $precio,
            "cantidad" => $cantidad,
            "unidad"   => $unidad,
            "imagen"   => $imagen
        ];
    } else {
        $_SESSION['carrito'][$id]["cantidad"] += $cantidad;
    }

    echo "OK";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Hortalizas</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    background:#eef2f7;
    font-family:'Poppins',sans-serif;
    padding:30px;
}

h1{
    text-align:center;
    font-size:2.8em;
    margin-bottom:35px;
    color:#1e293b;
    font-weight:700;
    letter-spacing:1px;
}

/* Buscador */
.buscador-box{
    max-width:1100px;
    margin:0 auto 35px auto;
    display:flex;
    gap:20px;
}

.buscador-box input{
    flex:1;
    padding:15px;
    border:1px solid #cbd5e1;
    border-radius:12px;
    font-size:1.1em;
    background:white;
    outline:none;
    transition:0.2s;
}
.buscador-box input:focus{
    border-color:#2563eb;
    box-shadow:0 0 5px rgba(37,99,235,0.3);
}

.buscador-box button{
    background:#2563eb;
    color:white;
    border:none;
    border-radius:12px;
    padding:0 30px;
    font-size:1.1em;
    font-weight:600;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:10px;
    transition:0.2s;
}
.buscador-box button:hover{
    background:#1d4ed8;
}

/* Grid productos */
.productos-grid{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
    gap:35px;
}

/* Tarjetas */
.producto{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 4px 14px rgba(0,0,0,0.08);
    transition:0.3s;
    text-align:center;
    border:1px solid #e5e7eb;
}

.producto:hover{
    transform:translateY(-8px);
    box-shadow:0 12px 24px rgba(0,0,0,0.12);
}

.producto img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:15px;
}

/* Texto */
.producto h3{
    margin-top:15px;
    font-size:1.3em;
    font-weight:600;
    color:#1e293b;
}

.producto p{
    margin:6px 0;
    color:#475569;
    font-size:1em;
}

/* Selects e inputs */
.producto select,
.producto input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #d1d5db;
    margin-top:5px;
    font-size:1em;
    outline:none;
    transition:0.2s;
}
.producto select:focus,
.producto input:focus{
    border-color:#2563eb;
}

/* Botón agregar */
.btn-add{
    margin-top:15px;
    background:#16a34a;
    color:white;
    border:none;
    padding:12px;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
    font-size:1em;
    transition:0.2s;
    width:100%;
}
.btn-add:hover{
    background:#15803d;
}

</style>
</head>

<body>

<h1>Hortalizas</h1>

<!-- BUSCADOR -->
<div class="buscador-box">
    <input id="buscador" oninput="filtrarProductos()" placeholder="Buscar producto...">
    <button onclick="window.location.href='carrito.php'">🛒 Ver carrito</button>
</div>

<!-- PRODUCTOS -->
<div class="productos-grid">

<?php
$productos = [
    ["id"=>"lechuga1","nombre"=>"Lechuga","precio"=>2.5,"imagen"=>"imagenes/lechuga.jpg","unidades"=>["Unidad"]],
    ["id"=>"papa1","nombre"=>"Papa","precio"=>12,"imagen"=>"imagenes/papa.jpg","unidades"=>["Cuarta","Arroba","Quintal"]],
    ["id"=>"tomate1","nombre"=>"Tomate","precio"=>3,"imagen"=>"imagenes/tomate.jpg","unidades"=>["Libra","Cuarta","Caja"]],
    ["id"=>"zanahoria1","nombre"=>"Zanahoria","precio"=>10,"imagen"=>"imagenes/zanahoria.jpg","unidades"=>["Cuarta","Arroba"]]
];

foreach($productos as $p): ?>

<div class="producto" data-filter="<?= strtolower($p['nombre']) ?>">

    <img src="<?= $p['imagen'] ?>">
    <h3><?= $p['nombre'] ?></h3>
    <p><b>Precio:</b> Bs. <?= $p['precio'] ?></p>

    <label>Unidad:</label>
    <select id="unidad_<?= $p['id'] ?>">
        <?php foreach($p['unidades'] as $u): ?>
            <option value="<?= $u ?>"><?= $u ?></option>
        <?php endforeach; ?>
    </select>

    <label>Cantidad:</label>
    <input type="number" id="cantidad_<?= $p['id'] ?>" value="1" min="1">

    <button class="btn-add"
        onclick="addToCart('<?= $p['id'] ?>','<?= $p['nombre'] ?>',
        <?= $p['precio'] ?>,'<?= $p['imagen'] ?>')">
        🛒 Agregar al carrito
    </button>

</div>

<?php endforeach; ?>
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
        }
    });
}
</script>

</body>
</html>
