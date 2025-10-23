<?php
// ======== CONEXIÓN A LA BASE DE DATOS ========
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ======== SIMULAR ESTUDIANTE (SIN LOGIN) ========
// Si luego agregas login, reemplaza con $_SESSION['id_estudiante']
$estudiante_id = 1;

// ======== OBTENER NOMBRE DEL ESTUDIANTE ========
$stmt = $pdo->prepare("SELECT nombre, apellido FROM estudiantes WHERE id = ?");
$stmt->execute([$estudiante_id]);
$est = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_estudiante = $est ? $est['nombre'] . " " . $est['apellido'] : "Estudiante";

// ======== CONSULTAR NOTAS DEL ESTUDIANTE ========
$stmt = $pdo->prepare("
    SELECT 
        m.nombre AS materia,
        c.semestre,
        COALESCE(n.nota_final, 0) AS nota_final,
        COALESCE(n.observaciones, '-') AS observaciones
    FROM inscripciones i
    INNER JOIN cursos c ON i.id_curso = c.id
    INNER JOIN materias m ON c.id_materia = m.id
    LEFT JOIN notas n ON n.id_inscripcion = i.id
    WHERE i.id_estudiante = ?
    ORDER BY c.semestre, m.nombre
");
$stmt->execute([$estudiante_id]);
$notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ======== CALCULAR PROMEDIO GENERAL ========
$promedio = 0;
if (count($notas) > 0) {
    $total = array_sum(array_column($notas, 'nota_final'));
    $promedio = round($total / count($notas), 2);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Notas - Estudiante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { display: flex; min-height: 100vh; margin: 0; background-color: #f8f9fa; }
    .sidebar {
        width: 240px;
        background: #343a40;
        color: #fff;
        flex-shrink: 0;
    }
    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 12px 15px;
        border-radius: 5px;
    }
    .sidebar a:hover { background: #495057; }
    .content { flex: 1; padding: 30px; }
    .card {
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .table thead { background: #007bff; color: white; }
</style>
</head>
<body>

<!-- ======== SIDEBAR ======== -->
<div class="sidebar p-3 d-flex flex-column">
    <h5 class="text-center mb-3"> <?= htmlspecialchars($nombre_estudiante) ?></h5>
    <hr>
    <a href="panel_estudiante.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<!-- ======== CONTENIDO PRINCIPAL ======== -->
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Mis Notas</h2>
        <span class="text-secondary">Consulta tus calificaciones y tu promedio general</span>
    </div>

    <div class="card p-4 mb-4 text-center">
        <h4 class="text-primary mb-2">Promedio General</h4>
        <h1 class="fw-bold display-5"><?= number_format($promedio, 2) ?></h1>
    </div>

    <div class="card p-4">
        <h4 class="mb-3 text-primary">Calificaciones por Materia y Semestre</h4>
        <?php if (count($notas) > 0): ?>
            <table class="table table-bordered table-hover text-center">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Semestre</th>
                        <th>Nota Final</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas as $n): ?>
                        <tr>
                            <td><?= htmlspecialchars($n['materia']) ?></td>
                            <td><?= htmlspecialchars($n['semestre']) ?></td>
                            <td><?= htmlspecialchars($n['nota_final']) ?></td>
                            <td><?= htmlspecialchars($n['observaciones']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                No se encontraron calificaciones registradas.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
