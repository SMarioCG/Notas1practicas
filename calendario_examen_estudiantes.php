<?php
// ======== CONEXIÓN A LA BASE DE DATOS ========
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "";
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
        flex-shrink: 0;
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
    .card {
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border: 2px solid #1e3c72;
        overflow: hidden;
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .table thead {
        background: #1e3c72;
        color: white;
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
    h2 {
        color: #1e3c72;
        font-weight: 700;
    }
    .alert-info {
        background: #d1ecf1;
        border: 1px solid #1e3c72;
        color: #1e3c72;
        border-radius: 8px;
        padding: 15px;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(30, 60, 114, 0.05);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(30, 60, 114, 0.1);
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
    <h2 class="fw-bold mb-4">Calendario Académico</h2>
    <p class="text-muted mb-4">
        Consulta aquí las fechas de <strong>exámenes</strong>, <strong>clases</strong> y <strong>eventos importantes</strong> establecidos por la administración.
    </p>

    <div class="card p-4">
        <?php if (count($eventos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center">
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
                                <td class="fw-bold"><?= htmlspecialchars($e['tipo_registro']) ?></td>
                                <td><?= htmlspecialchars($e['tipo_examen'] ?? '-') ?></td>
                                <td><?= date("d/m/Y", strtotime($e['fecha'])) ?></td>
                                <td><?= date("H:i", strtotime($e['hora'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                No hay eventos registrados en el calendario académico por el momento.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>