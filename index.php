<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BoliviaMarket</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e3a8a, #2b4cb3);
    color: white;
    padding-top: 90px;
}

/* TITULO PRINCIPAL */
.contenedor-titulo {
    text-align: center;
    margin-top: 20px;
    animation: fadeIn 1.2s ease-in-out;
}

.contenedor-titulo h1 {
    font-size: 3rem;
    font-weight: 700;
    letter-spacing: 1px;
}

.contenedor-titulo span {
    color: #ffd43b;
}

.contenedor-titulo p {
    margin-top: -10px;
    font-size: 1.1rem;
    opacity: 0.9;
}

/* TARJETAS DE CATEGORÍAS */
.categorias {
    margin-top: 50px;
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.tarjeta {
    width: 260px;
    height: 170px;
    background: #ffffff12;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.4);
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    backdrop-filter: blur(5px);
}

.tarjeta:hover {
    transform: translateY(-8px);
    box-shadow: 0px 6px 20px rgba(0,0,0,0.5);
}

.tarjeta img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.75);
}

.tarjeta-texto {
    position: absolute;
    bottom: 10px;
    width: 100%;
    text-align: center;
    font-weight: 600;
    background: rgba(0,0,0,0.4);
    padding: 5px 0;
    border-radius: 8px;
}

/* Animación */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

</head>
<body>

<?php include("barra_sup.php"); ?>

<!-- TITULO PRINCIPAL -->
<div class="contenedor-titulo">
    <h1>Bienvenido a <span>BoliviaMarket</span> 🇧🇴</h1>
    <p>Encuentra productos bolivianos con confianza, calidad y frescura.</p>
</div>

<!-- TARJETAS DE CATEGORÍAS -->
<div class="categorias">

    <div class="tarjeta" onclick="window.location.href='hortalizas.php'">
        <img src="imagenes/hortalizas.jpg">
        <div class="tarjeta-texto">Hortalizas</div>
    </div>

    <div class="tarjeta" onclick="window.location.href='bebidas.php'">
        <img src="imagenes/bebida.jpg">
        <div class="tarjeta-texto">Bebidas</div>
    </div>

    <div class="tarjeta" onclick="window.location.href='alimentos.php'">
        <img src="imagenes/comida.jpg">
        <div class="tarjeta-texto">Alimentos</div>
    </div>

</div>

</body>
</html>
