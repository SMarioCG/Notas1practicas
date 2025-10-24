<?php
//prototipo 
$nombre_estudiante = "Estudiante Regional";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Estudiante</title>
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
    width: 200px;
    background: #1e3c72;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 40px;
    box-shadow: 3px 0 20px rgba(0,0,0,0.3);
    border-right: 3px solid #ffffff;
}
nav a {
    display:block;
    padding:15px 20px;
    color:#ffffff;
    text-decoration:none;
    font-weight:500;
    margin: 12px 0;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    width: 90%;
    text-align: center;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}
nav a:hover {
    background: #ffffff;
    color: #1e3c72;
    transform: translateX(8px);
    border-left: 4px solid #1e3c72;
    box-shadow: 0 5px 15px rgba(255,255,255,0.2);
}

/* Main */
main {
    margin-left: 200px;
    padding: 50px;
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    width: calc(100% - 200px);
    align-content: start;
}

/* Header */
header {
    grid-column: 1/-1;
    text-align: center;
    font-size: 2.5em;
    font-weight: bold;
    margin-bottom: 50px;
    color: #ffffff;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
    padding: 30px;
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
    border: 2px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
}

/* Cards */
.card {
    background: #ffffff;
    padding: 35px 30px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    text-align: center;
    transition: all 0.4s ease;
    cursor: pointer;
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(30,60,114,0.4);
    border-color: #1e3c72;
}
.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #1e3c72, #3a6fd9);
}
.card h3 {
    margin-bottom: 15px;
    font-size: 1.6em;
    font-weight: 700;
    color: #1e3c72;
}
.card p {
    color: #444;
    line-height: 1.6;
    font-weight: 500;
    font-size: 1.05em;
}
</style>
</head>
<body>

<nav>
    <a href="login.php">Cerrar Sesión</a>
</nav>

<main>
<header>Bienvenido, <?php echo $nombre_estudiante; ?></header>

 <div class="card" onclick="window.location.href='http://localhost:3000/cursos_panel_estudiante.php'">
    <h3>Mis Cursos</h3>
    <p>Consultar los cursos en los que estás inscrito este semestre.</p>
</div>

<div class="card" onclick="window.location.href='http://localhost:3000/notas_panel_estudiante.php'">
    <h3>Mis Notas</h3>
    <p>Ver tus calificaciones por materia y semestre, y tu promedio general.</p>
</div>

<div class="card" onclick="window.location.href='http://localhost:3000/calendario_examen_estudiantes.php'">
    <h3>Calendario Académico</h3>
    <p>Revisar fechas de exámenes y actividades importantes.</p>
</div>

</main>

</body>
</html>