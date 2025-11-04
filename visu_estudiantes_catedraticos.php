<?php
// conexion.php
$host = "localhost";
$db = "notasregional3";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();

// Validar sesión catedrático
if(!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Catedrático'){
    header("Location: login.php");
    exit;
}

$catedratico_id = $_SESSION['id'];
$curso_seleccionado = $_GET['curso_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estudiantes - Panel Catedrático</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { display: flex; min-height: 100vh; margin: 0; font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
.sidebar { width: 220px; background: #004383; color: #fff; }
.sidebar h4 { text-align: center; margin-top: 20px; color: #ffb300; font-weight: bold; }
.sidebar a { color: #fff; text-decoration: none; display: block; padding: 12px 15px; border-left: 4px solid transparent; transition: 0.3s; }
.sidebar a:hover { background: rgba(255,255,255,0.1); border-left: 4px solid #ffb300; }
.content { flex: 1; padding: 30px; }
.content h2 { color: #004383; font-weight: bold; margin-bottom: 25px; }
.card { cursor: pointer; transition: 0.3s; border: none; background: #004383; margin-bottom: 15px; }
.card:hover { transform: scale(1.05); box-shadow: 0 6px 15px rgba(0,0,0,0.3); }
.card .card-body { color: #fff; }
.card-title { color: #ffb300; font-weight: bold; }
table thead { background-color: #004383; color: #fff; }
table tbody tr:hover { background-color: #e9f0ff; }
.btn-success { background-color: #ffb300; border: none; color: #000; font-weight: 600; transition: 0.3s; }
.btn-success:hover { background-color: #e6a000; color: #fff; }
.btn-primary { margin: 2px; }
</style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center"><?= htmlspecialchars($_SESSION['nombre']) ?></h4>
    <hr>
    <a href="panel_catedraticos.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h2>Mis Estudiantes y Cursos</h2>

    <?php
    // Obtener cursos del catedrático
    $stmt_cursos = $pdo->prepare("
        SELECT c.id, m.nombre AS materia, c.semestre
        FROM cursos c
        JOIN materias m ON c.id_materia = m.id
        WHERE c.id_catedratico = ?
        ORDER BY c.semestre
    ");
    $stmt_cursos->execute([$catedratico_id]);
    $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

    if(!$cursos) {
        echo "<p class='alert alert-info'>No tienes cursos asignados.</p>";
    } else {
        echo "<div class='mb-4'>";
        echo "<h4>Selecciona un curso:</h4>";
        foreach($cursos as $curso) {
            $activo = ($curso_seleccionado == $curso['id']) ? 'btn-primary' : 'btn-outline-primary';
            echo "<a href='?curso_id={$curso['id']}' class='btn $activo'>";
            echo htmlspecialchars($curso['materia']) . " - Semestre " . htmlspecialchars($curso['semestre']);
            echo "</a>";
        }
        echo "</div>";
    }

    // Mostrar estudiantes si hay curso seleccionado
    if($curso_seleccionado){
        $stmt_est = $pdo->prepare("
            SELECT e.id, e.nombre, e.apellido, e.correo
            FROM inscripciones i
            JOIN estudiantes e ON i.id_estudiante = e.id
            JOIN cursos c ON i.id_curso = c.id
            WHERE i.id_curso = ? AND c.id_catedratico = ?
            ORDER BY e.nombre
        ");
        $stmt_est->execute([$curso_seleccionado, $catedratico_id]);
        $estudiantes = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

        if(!$estudiantes){
            echo "<p class='alert alert-info'>No hay estudiantes inscritos en este curso.</p>";
        } else {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-hover'>";
            echo "<thead><tr>
                <th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th></tr></thead><tbody>";
            foreach($estudiantes as $est){
                echo "<tr>";
                echo "<td>".htmlspecialchars($est['id'])."</td>";
                echo "<td>".htmlspecialchars($est['nombre'])."</td>";
                echo "<td>".htmlspecialchars($est['apellido'])."</td>";
                echo "<td>".htmlspecialchars($est['correo'])."</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        }
    }
    ?>

</div>
</body>
</html>

