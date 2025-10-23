<?php
// conexion.php
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ------------------------
// Simular estudiante
// ------------------------
$estudiante_id = 1; // ID del estudiante para probar
$nombre_estudiante = "Juan Pérez"; // nombre del estudiante para mostrar
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Cursos - Estudiante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { display: flex; min-height: 100vh; margin: 0; }
    .sidebar { width: 220px; background: #343a40; color: #fff; }
    .sidebar a { color: #fff; text-decoration: none; display: block; padding: 12px 15px; }
    .sidebar a:hover { background: #495057; }
    .content { flex: 1; padding: 20px; }
    .table thead { background-color: #007bff; color: #fff; }
</style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center">Bienvenido, <?= htmlspecialchars($nombre_estudiante) ?></h4>
    <hr>
    <a href="panel_estudiante.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h2 class="mb-4">Mis Cursos Inscritos</h2>

    <?php
    // Obtener cursos inscritos del estudiante
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
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
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
</div>

</body>
</html>
