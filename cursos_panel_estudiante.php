<?php
// conexion.php
$host = "localhost";
$db = "notasregional3";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

session_start();

// Validar sesión estudiante
if(!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Estudiante'){
    header("Location: login.php");
    exit;
}

// Obtener datos del estudiante logueado
$estudiante_id = $_SESSION['id'];
$nombre_estudiante = $_SESSION['nombre'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Cursos - Estudiante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* === ESTILOS GENERALES === */
body {
    display: flex;
    min-height: 100vh;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    color: #333;
}

/* === MENÚ LATERAL === */
.sidebar {
    width: 230px;
    background: #004383;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 25px 20px;
    box-shadow: 3px 0 10px rgba(0,0,0,0.3);
}
.sidebar h4 {
    font-weight: 700;
    font-size: 1.2em;
    text-align: center;
    color: #ffb300;
    margin-bottom: 15px;
}
.sidebar hr {
    border: none;
    height: 2px;
    background: rgba(255,255,255,0.3);
    margin: 10px 0 20px 0;
}
.sidebar a {
    color: #fff;
    text-decoration: none;
    display: block;
    padding: 12px 15px;
    margin-bottom: 8px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.sidebar a:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffb300;
    transform: translateX(4px);
}

/* === CONTENIDO === */
.content {
    flex: 1;
    padding: 40px;
}
.content h2 {
    color: #004383;
    font-weight: 700;
    margin-bottom: 25px;
}
.table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}
.table thead {
    background-color: #004383;
    color: #fff;
    text-transform: uppercase;
    font-size: 0.9em;
}
.table tbody tr:hover {
    background-color: rgba(0,67,131,0.05);
}
.alert-info {
    background-color: rgba(0,67,131,0.1);
    border: 1px solid #004383;
    color: #004383;
    border-radius: 10px;
    padding: 15px;
}
footer {
    text-align: center;
    color: #777;
    margin-top: 40px;
    font-size: 0.9em;
}
</style>
</head>
<body>

<!-- === SIDEBAR === -->
<div class="sidebar">
    <h4>Bienvenido<br><?= htmlspecialchars($nombre_estudiante) ?></h4>
    <hr>
    <a href="panel_estudiante.php"> Volver al Panel</a>
    <a href="login.php"> Cerrar Sesión</a>
</div>

<!-- === CONTENIDO === -->
<div class="content">
    <h2>Mis Cursos Inscritos</h2>

    <?php
    // Consultar los cursos del estudiante autenticado
    $stmt = $pdo->prepare("
        SELECT 
            m.nombre AS materia,
            m.codigo,
            m.creditos,
            ca.nombre AS carrera,
            c.semestre
        FROM inscripciones i
        JOIN cursos c ON i.id_curso = c.id
        JOIN materias m ON c.id_materia = m.id
        JOIN carreras ca ON m.id_carrera = ca.id
        WHERE i.id_estudiante = ?
        ORDER BY c.semestre, m.nombre
    ");
    $stmt->execute([$estudiante_id]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if(count($cursos) == 0): ?>
        <p class="alert alert-info">No estás inscrito en ningún curso este semestre.</p>
    <?php else: ?>
    <div class="table-container mt-3">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Código</th>
                    <th>Créditos</th>
                    <th>Carrera</th>
                    <th>Semestre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cursos as $curso): ?>
                <tr>
                    <td><?= htmlspecialchars($curso['materia']) ?></td>
                    <td><?= htmlspecialchars($curso['codigo']) ?></td>
                    <td><?= htmlspecialchars($curso['creditos']) ?></td>
                    <td><?= htmlspecialchars($curso['carrera']) ?></td>
                    <td><?= htmlspecialchars($curso['semestre']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <footer>© 2025 Sistema Académico Regional</footer>
</div>

</body>
</html>
