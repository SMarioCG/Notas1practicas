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
    display: flex;
    align-items: center;
    gap: 10px;
}
.content h2::before {
    content: "";
    font-size: 1.3em;
}

/* === TABLA === */
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
.table td, .table th {
    vertical-align: middle !important;
    text-align: center;
    padding: 12px;
}
.table td:first-child {
    font-weight: 500;
}

/* === ALERTA === */
.alert-info {
    background-color: rgba(0,67,131,0.1);
    border: 1px solid #004383;
    color: #004383;
    border-radius: 10px;
    padding: 15px;
}

/* === PIE OPCIONAL === */
footer {
    text-align: center;
    color: #777;
    margin-top: 40px;
    font-size: 0.9em;
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

