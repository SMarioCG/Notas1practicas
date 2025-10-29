<?php
// ======== CONEXIÓN A LA BASE DE DATOS ========
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "pequeñocesar2025";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ======== CONSULTAR CALENDARIO GENERAL (ASIGNADO POR ADMIN) ========
$stmt = $pdo->query("
    SELECT 
        tipo_registro,
        tipo_examen,
        fecha,
        hora
    FROM calendario_examenes
    WHERE id_catedratico IS NULL
    ORDER BY fecha ASC, hora ASC
");
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Calendario Académico - Estudiante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { display: flex; min-height: 100vh; margin: 0; background: #f8f9fa; }
    .sidebar {
        width: 220px;
        background: #343a40;
        color: #fff;
        flex-shrink: 0;
    }
    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 12px 15px;
    }
    .sidebar a:hover {
        background: #495057;
    }
    .content {
        flex: 1;
        padding: 30px;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .table thead {
        background-color: #007bff;
        color: white;
    }
</style>
</head>
<body>

<!-- ======== SIDEBAR ======== -->
<div class="sidebar p-3 d-flex flex-column">
    <a href="panel_estudiante.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<!-- ======== CONTENIDO PRINCIPAL ======== -->
<div class="content">
    <h2 class="fw-bold mb-4 text-primary"> Calendario Académico</h2>
    <p class="text-muted mb-4">
        Consulta aquí las fechas de <strong>exámenes</strong>, <strong>clases</strong> y <strong>eventos importantes</strong> establecidos por la administración.
    </p>

    <div class="card p-4">
        <?php if (count($eventos) > 0): ?>
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Tipo de Registro</th>
                        <th>Tipo de Examen / Evento</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['tipo_registro']) ?></td>
                            <td><?= htmlspecialchars($e['tipo_examen'] ?? '-') ?></td>
                            <td><?= date("d/m/Y", strtotime($e['fecha'])) ?></td>
                            <td><?= date("H:i", strtotime($e['hora'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info text-center">
                No hay eventos registrados en el calendario académico por el momento.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

