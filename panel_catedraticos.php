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
    background: linear-gradient(135deg, #1c1c2b, #2a2a3d, #3a3a50);
    color: #fff;
    display: flex;
    min-height: 100vh;
}

/* Menú lateral */
nav {
    width: 180px;
    background: rgba(20,20,30,0.95);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding-top: 20px;
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
    background: linear-gradient(90deg, #1a2a6c, #b21f1f);
    transform: translateX(5px);
    border-left: 4px solid #fff;
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
    color: #ffd700;
}

/* Cards */
.card {
    background: rgba(255,255,255,0.05);
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.6);
}
.card h3 {
    margin-bottom: 10px;
    font-size: 1.4em;
    color: #ffd700;
}
.card p {
    color: #ccc;
    line-height: 1.5;
}
</style>
</head>
<body>

<nav>
    <a href="login.php"> Cerrar Sesión</a>
</nav>

<main>
<header> Bienvenido, Catedrático Regional</header>

<div class="card"  onclick="window.location.href='http://localhost:3000/subir_notas.php'" style="cursor: pointer;">
    <h3> Subir Notas</h3>
    <p>Registrar y actualizar calificaciones de los estudiantes de tus cursos asignados.</p>
</div>

<div class="card" onclick ="window.location.href='http://localhost:3000/mis_cursos._catedratico.php'" style="cursor: pointer;">
    <h3> Mis Cursos</h3>
    <p>Visualizar los cursos y materias que tienes a cargo este semestre.</p>
</div>

<div class="card" onclick ="window.location.href='http://localhost:3000/visu_estudiantes_catedraticos.php'" style="cursor: pointer;">
    <h3> Estudiantes</h3>
    <p>Ver la lista de estudiantes inscritos en tus cursos y su desempeño académico.</p>
</div>


<div class="card" onclick ="window.location.href='http://localhost:3000/calendario_catedratico.php'" style="cursor: pointer;">
    <h3> Ver calendario de exámenes</h3>
    <p>Fechas de exámenes y entregas de los cursos que impartes.</p>
</div>

</main>

</body>
</html>

