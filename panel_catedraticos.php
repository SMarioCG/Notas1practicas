<?php
session_start();

// Simular sesión de catedrático para vista previa
$_SESSION['nombre'] = 'Catedrático Juan Pérez';
$_SESSION['rol'] = 'catedratico';
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
    background: linear-gradient(135deg, #1e3c72, #2a5298, #3a6fd9);
    color: #fff;
    display: flex;
    min-height: 100vh;
}

/* Menú lateral */
nav {
    width: 180px;
    background: #1e3c72;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding-top: 20px;
    box-shadow: 2px 0 15px rgba(0,0,0,0.3);
    border-right: 3px solid #ffffff;
}
nav a {
    display:block;
    padding:15px 20px;
    color:#ffffff;
    text-decoration:none;
    font-weight:500;
    margin-top: 20px;
    border-left: 4px solid transparent;
    transition: all 0.3s;
    width: 100%;
    text-align: center;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    margin: 10px 5px;
}
nav a:hover {
    background: #ffffff;
    color: #1e3c72;
    transform: translateX(5px);
    border-left: 4px solid #1e3c72;
}

/* Main */
main {
    margin-left: 180px;
    padding: 40px;
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    width: calc(100% - 180px);
}

/* Header */
header {
    grid-column: 1/-1;
    text-align: center;
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 40px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 15px;
    border: 2px solid #ffffff;
}

/* Cards */
.card {
    background: #ffffff;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    border: 2px solid #1e3c72;
}
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(30,60,114,0.4);
    border-color: #3a6fd9;
}
.card h3 {
    margin-bottom: 10px;
    font-size: 1.4em;
    color: #1e3c72;
}
.card p {
    color: #333333;
    line-height: 1.5;
    font-weight: 500;
}
</style>
</head>
<body>

<nav>
    <a href="login.php">Cerrar Sesión</a>
</nav>

<main>
<header>Bienvenido, Catedrático Regional</header>

<div class="card" onclick="window.location.href='http://localhost:3000/subir_notas.php'">
    <h3>Subir Notas</h3>
    <p>Registrar y actualizar calificaciones de los estudiantes de tus cursos asignados.</p>
</div>

<div class="card" onclick="window.location.href='http://localhost:3000/mis_cursos._catedratico.php'">
    <h3>Mis Cursos</h3>
    <p>Visualizar los cursos y materias que tienes a cargo este semestre.</p>
</div>

<div class="card" onclick="window.location.href='http://localhost:3000/visu_estudiantes_catedraticos.php'">
    <h3>Estudiantes</h3>
    <p>Ver la lista de estudiantes inscritos en tus cursos y su desempeño académico.</p>
</div>

<div class="card" onclick="window.location.href='http://localhost:3000/calendario_catedratico.php'">
    <h3>Ver calendario de exámenes</h3>
    <p>Fechas de exámenes y entregas de los cursos que impartes.</p>
</div>

</main>

</body>
</html>