<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "notasregional3";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar codificación
$conexion->set_charset("utf8mb4");
?>
