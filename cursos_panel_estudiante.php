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
$nombre_estudiante = "Estudiante Regional"; // nombre del estudiante para mostrar
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Cursos - Estudiante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { 
        display: flex; 
        min-height: 100vh; 
        margin: 0; 
        background: linear-gradient(135deg, #1e3c72, #2a5298, #3a6fd9);
        font-family: 'Segoe UI', sans-serif;
    }
    .sidebar { 
        width: 240px; 
        background: #1e3c72; 
        color: #fff;
        border-right: 3px solid #ffffff;
    }
    .sidebar a { 
        color: #fff; 
        text-decoration: none; 
        display: block; 
        padding: 12px 15px; 
        margin: 8px 10px;
        border-radius: 8px;
        background: rgba(255,255,255,0.1);
        transition: all 0.3s ease;
    }
    .sidebar a:hover { 
        background: #ffffff;
        color: #1e3c72;
        transform: translateX(5px);
    }
    .content { 
        flex: 1; 
        padding: 30px;
        background: #ffffff;
        margin: 20px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .table thead { 
        background: #1e3c72; 
        color: #fff; 
    }
    .table th {
        border: none;
        padding: 15px;
        font-weight: 600;
    }
    .table td {
        padding: 15px;
        vertical-align: middle;
        border-color: #dee2e6;
    }
    h2, h4 {
        color: #1e3c72;
        font-weight: 700;
    }
    .sidebar h4 {
        color: #ffffff;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .alert-info {
        background: #d1ecf1;
        border: 1px solid #1e3c72;
        color: #1e3c72;
        border-radius: 8px;
        padding: 15px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(30, 60, 114, 0.1);
    }
</style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center">Bienvenido, <?= htmlspecialchars($nombre_estudiante) ?></h4>
    <hr style="border-color: #ffffff;">
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
                    <td class="fw-bold"><?= htmlspecialchars($curso['materia']) ?></td>
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