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
    padding-top: 100px; /* espacio para el header */
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
    left: 180px; /* comienza después del menú lateral */
    right: 0;
    height: 80px;
    background: #ffffff;
    display: flex;
    justify-content: center; /* separa el logo y el botón */
    align-items: center;
    padding: 0 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000px;
    gap: 20px; /* espacio entre logo y texto */
}

/* Etiqueta de bienvenida */
.etiqueta {
    background: #004383;
    color: #fff;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9em;
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
    left: 30px; /* fija el logo al borde izquierdo */
}
/* Main */
main {
    margin-left: 180px;
    margin-top: 100px; /* espacio debajo del header */
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
    padding: 25px;
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
.etiqueta {
    background: #004383;
    color: #fff;
    padding: 10px 25px;
    border-radius: 25px;
    font-size: 1.3em; /* 🔹 Aumenta el tamaño del texto */
    font-weight: bold;
    text-align: center;
}
</style>
</head>
<body>

<!-- Menú lateral -->
<nav>
    <a href="login.php"> Cerrar Sesión</a>
</nav>

<!-- Encabezado superior -->
<header>
    <div class="logo">
        <img src="https://moria.aurens.com/organizations/362029ae-4545-4e01-a1d9-5a79a6e6f493/logos/26b681-regional.png" alt="Logo Regional">
    </div>
    <span class="etiqueta">Bienvenido, Catedrático Regional</span>
</header>


<!-- Contenido principal -->
<main>
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

