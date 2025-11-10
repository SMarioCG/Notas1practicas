<?php
session_start();
include("conexion.php");

// Verificar sesión y rol
if(!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Catedrático'){
    header("Location: login.php");
    exit;
}

$catedratico_id = $_SESSION['id']; // ID del catedrático que inició sesión
$catedratico_nombre = $_SESSION['nombre'];

// Consultar cursos asignados a este catedrático
$query = $conexion->prepare("SELECT c.*, m.nombre AS materia 
                             FROM cursos c
                             INNER JOIN materias m ON c.id_materia = m.id
                             WHERE c.id_catedratico = ?");
$query->bind_param("i", $catedratico_id);
$query->execute();
$result = $query->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Catedrático</title>
<style>
* {margin:0; padding:0; box-sizing:border-box;}

/* Body */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #fff;
    color: #fff;
    display: flex;
    min-height: 100vh;
}

/* Menú lateral */
nav {
    width: 180px;
    background: rgba(6, 34, 126, 0.95);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding-top: 100px;
    box-shadow: 2px 0 15px rgba(0,0,0,0.5);
}
nav a {
    display:block;
    padding:15px 20px;
    color:#fff;
    text-decoration:none;
    font-weight:500;
    margin-top: 20px;
    border-left: 4px solid transparent; 
    transition: all 0.3s;
    width: 100%;
    text-align: center;
}
nav a:hover {
    background: linear-gradient(90deg, #1a2a6c, #ff5500ff);
    transform: translateX(5px);
    border-left: 4px solid #000000ff;
}

/* Header superior blanco */
header {
    position: fixed;
    top: 0;
    left: 180px;
    right: 0;
    height: 80px;
    background: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000px;
    gap: 20px;
}

/* Etiqueta de bienvenida */
.etiqueta {
    background: #004383;
    color: #fff;
    padding: 10px 25px;
    border-radius: 25px;
    font-size: 1.3em;
    font-weight: bold;
    text-align: center;
}

/* Botón cerrar sesión */
header a {
    text-decoration: none;
    background: #06227e;
    color: #fff;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
}
header a:hover {
    background: #ff5500;
}

/* Logo */
.logo img {
    height: 65px;
    width: auto;
}
.logo {
    position: absolute;
    left: 30px;
}

/* Main */
main {
    margin-left: 180px;
    margin-top: 100px;
    padding: 40px;
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    width: calc(100% - 180px);
}

/* Tarjetas del panel */
.card {
    background: linear-gradient(135deg, #06227e, #1a3cbf);
    padding: 30px 25px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    cursor: pointer;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.6);
    background: linear-gradient(135deg, #ff8400, #ff5500);
}
.card h3 {
    margin-bottom: 10px;
    font-size: 1.4em;
    color: #ffffff;
}
.card p {
    color: #e0e0e0;
    line-height: 1.5;
}
</style>
</head>
<body>

<!-- Menú lateral -->
<nav>
    <a href="login.php">Cerrar Sesión</a>
</nav>

<!-- Encabezado superior -->
<header>
    <div class="logo">
        <img src="https://moria.aurens.com/organizations/362029ae-4545-4e01-a1d9-5a79a6e6f493/logos/26b681-regional.png" alt="Logo Regional">
    </div>
    <span class="etiqueta">Bienvenido Catedrático, <?= htmlspecialchars($catedratico_nombre) ?></span>
</header>

<!-- Contenido principal -->
<main>
    <div class="card" onclick="window.location.href='subir_notas.php'">
        <h3>Subir Notas</h3>
        <p>Registrar y actualizar calificaciones de los estudiantes de tus cursos asignados.</p>
    </div>

    <div class="card" onclick="window.location.href='http://localhost:3000/mis_cursos._catedratico.php'">
        <h3>Mis Cursos</h3>
        <p>
            <?php 
            if(count($cursos) > 0){
                foreach($cursos as $curso){
                    echo htmlspecialchars($curso['materia']) . "<br>";
                }
            } else {
                echo "No tienes cursos asignados.";
            }
            ?>
        </p>
    </div>

    <div class="card" onclick="window.location.href='visu_estudiantes_catedraticos.php'">
        <h3>Estudiantes</h3>
        <p>Ver la lista de estudiantes inscritos en tus cursos y su desempeño académico.</p>
    </div>

    <div class="card" onclick="window.location.href='calendario_catedratico.php'">
        <h3>Ver calendario de exámenes</h3>
        <p>Fechas de exámenes y entregas de los cursos que impartes.</p>
    </div>
</main>

</body>
</html>


