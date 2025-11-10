<?php
session_start();

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "notasregional3";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Validar sesión de estudiante
if(!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'Estudiante'){
    header("Location: login.php");
    exit;
}

// Obtener datos del estudiante según el correo
$correo_estudiante = $_SESSION['correo'];
$stmt = $pdo->prepare("SELECT id, nombre FROM estudiantes WHERE correo = ?");
$stmt->execute([$correo_estudiante]);
$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$estudiante){
    die("Estudiante no encontrado.");
}

$nombre_estudiante = $estudiante['nombre'];
$estudiante_id = $estudiante['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Estudiante</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }

/* === BODY === */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    color: #333;
    display: flex;
    min-height: 100vh;
}

/* === MENÚ LATERAL === */
nav {
    width: 200px;
    background: #004383;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 30px;
    box-shadow: 2px 0 12px rgba(0,0,0,0.4);
}
nav h2 {
    color: #ffb300;
    font-size: 1.3em;
    margin-bottom: 20px;
}
nav a {
    display: block;
    padding: 14px 18px;
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    margin-top: 15px;
    border-left: 4px solid transparent;
    width: 100%;
    text-align: center;
    transition: all 0.3s ease;
}
nav a:hover {
    background: rgba(255,255,255,0.1);
    border-left: 4px solid #ffb300;
    transform: translateX(5px);
}

/* === ENCABEZADO === */
header {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    margin-bottom: 40px;
    height: 100px;
}

/* Logo a la izquierda */
.logo {
    position: absolute;
    left: 30px;
}
.logo img {
    height: 80px;
    width: auto;
}

/* Texto de bienvenida */
header .bienvenida {
    background-color: #004383;
    color: #fff;
    padding: 12px 30px;
    border-radius: 30px;
    font-size: 1.5em;
    font-weight: bold;
    text-align: center;
    white-space: nowrap;
}

/* === CONTENIDO PRINCIPAL === */
main {
    margin-left: 200px;
    padding: 40px;
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    width: calc(100% - 200px);
    align-items: start;
}

/* === TARJETAS - MISMO TAMAÑO === */
.card {
    background: #ffffff;
    padding: 30px 25px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    text-align: center;
    transition: all 0.3s ease;
    border-top: 5px solid #004383;
    cursor: pointer;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.25);
    border-top-color: #ffb300;
}

.card h3 {
    margin-bottom: 15px;
    font-size: 1.4em;
    color: #004383;
    font-weight: 700;
}

.card p {
    color: #555;
    line-height: 1.5;
    font-size: 1em;
    margin: 0;
}

/* === PIE DE PÁGINA OPCIONAL === */
footer {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: 40px;
    color: #888;
    font-size: 0.9em;
}
</style>
</head>
<body>

<nav>
    <a href="login.php">Cerrar Sesión</a>
</nav>

<main>
    <header>
        <div class="logo">
            <img src="https://moria.aurens.com/organizations/362029ae-4545-4e01-a1d9-5a79a6e6f493/logos/26b681-regional.png" alt="Logo Regional">
        </div>
        <div class="bienvenida">Bienvenido, <?= htmlspecialchars($nombre_estudiante) ?></div>
    </header>

    <div class="card" onclick="window.location.href='cursos_panel_estudiante.php?estudiante_id=<?= $estudiante_id ?>'">
        <h3>Mis Cursos</h3>
        <p>Consulta los cursos en los que estás inscrito este semestre.</p>
    </div>

    <div class="card" onclick="window.location.href='notas_panel_estudiante.php?estudiante_id=<?= $estudiante_id ?>'">
        <h3>Mis Notas</h3>
        <p>Visualiza tus calificaciones, promedios y resultados por materia.</p>
    </div>

    <div class="card" onclick="window.location.href='calendario_examen_estudiantes.php?estudiante_id=<?= $estudiante_id ?>'">
        <h3>Calendario Académico</h3>
        <p>Consulta las fechas de exámenes, entregas y eventos importantes.</p>
    </div>

    <div class="card" onclick="window.location.href='inscripciones.php?estudiante_id=<?= $estudiante_id ?>'">
        <h3>Inscripciones</h3>
        <p>Revisa el estado de tus inscripciones y los cursos disponibles.</p>
    </div>
</main>

</body>
</html>
