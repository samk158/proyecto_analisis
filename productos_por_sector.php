<?php
session_start();

// Detectar sector
$sector = $_GET['sector'] ?? '';

if ($sector !== 'hortalizas') {
    echo "<p style='text-align:center;'>No hay productos en esta categoría.</p>";
    exit;
}

// Productos estáticos (sin BD)
$productos = [
    [
        "id" => "lechuga1",
        "nombre" => "Lechuga",
        "precio" => 2.5,
        "imagen" => "imagenes/lechuga.jpg",
        "unidades" => ["Unidad"]
    ],
    [
        "id" => "papa1",
        "nombre" => "Papa",
        "precio" => 12,
        "imagen" => "imagenes/papa.jpg",
        "unidades" => ["Cuarta", "Arroba", "Quintal"]
    ],
    [
        "id" => "tomate1",
        "nombre" => "Tomate",
        "precio" => 3,
        "imagen" => "imagenes/tomate.jpg",
        "unidades" => ["Libra", "Cuarta", "Caja"]
    ],
    [
        "id" => "zanahoria1",
        "nombre" => "Zanahoria",
        "precio" => 10,
        "imagen" => "imagenes/zanahoria.jpg",
        "unidades" => ["Cuarta", "Arroba"]
    ]
];
?>

<style>
#lista-productos{
    width:100%;
    padding:10px;
}
.buscador-box{
    width:100%;
    display:flex;
    gap:15px;
    margin-bottom:20px;
}
.buscador-box input{
    flex:1;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}
.buscador-box button{
    padding:10px 20px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.productos-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
    gap:20px;
}

.producto{
    background:white;
    border-radius:12px;
    padding:12px;
    text-align:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
.producto img{
    width:100%;
    height:160px;
    object-fit:cover;
    border-radius:8px;
}
.btn-add{
    background:#16a34a;
    padding:10px;
    width:100%;
    border:none;
    border-radius:8px;
    color:white;
    font-weight:bold;
    cursor:pointer;
}
</style>

<div id="lista-productos">

    <div class="buscador-box">
        <input id="buscador" oninput="filtrarProductos()" placeholder="Buscar producto...">
        <button class="btn-carrito" onclick="abrirCarrito()">
            🛒 Ver carrito
        </button>
    </div>

    <h2>Hortalizas</h2>
    <hr><br>

    <div class="productos-grid">

        <?php foreach ($productos as $p): ?>
            <div class="producto" data-filter="<?= strtolower($p['nombre']) ?>">
                <img src="<?= $p['imagen'] ?>">
                <h4><?= $p['nombre'] ?></h4>
                <p>Precio base: Bs. <?= $p['precio'] ?></p>

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
                    <?= $p['precio'] ?>,'<?= $p['imagen'] ?>')">🛒 Agregar al carrito</button>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<script>
function filtrarProductos(){
    const filtro = document.getElementById("buscador").value.toLowerCase();
    document.querySelectorAll(".producto").forEach(p=>{
        p.style.display = p.dataset.filter.includes(filtro) ? "block" : "none";
    });
}

function abrirCarrito(){
    window.location.href = "carrito.php";
}

// --- AGREGAR Y REDIRIGIR ---
function addToCart(id,nombre,precio,imagen){

    const cantidad = document.getElementById("cantidad_"+id).value;
    const unidad = document.getElementById("unidad_"+id").value;

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
