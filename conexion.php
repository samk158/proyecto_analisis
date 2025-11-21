<?php
// conexion.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "p_analisisydiseño";  
$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Para caracteres especiales
mysqli_set_charset($conexion, "utf8");
?>

