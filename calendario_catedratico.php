<?php
// conexion.php
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();

// Simular sesión del catedrático
$_SESSION['id_catedratico'] = 1;
$catedratico_id = $_SESSION['id_catedratico'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Calendario de Exámenes - Dashboard</title>
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
        width: 220px; 
        background: #1e3c72; 
        color: #fff;
        border-right: 3px solid #ffffff;
    }
    .sidebar a { 
        color: #fff; 
        text-decoration: none; 
        display: block; 
        padding: 12px 15px; 
        margin: 5px 10px;
        border-radius: 8px;
        background: rgba(255,255,255,0.1);
        transition: all 0.3s;
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
    h2 {
        color: #1e3c72;
        font-weight: 700;
        margin-bottom: 30px;
    }
    .sidebar h4 {
        color: #ffffff;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .table thead {
        background: #1e3c72;
        color: #ffffff;
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
    .table-hover tbody tr:hover {
        background-color: rgba(30, 60, 114, 0.1);
    }
    .alert-info {
        background: #d1ecf1;
        border: 1px solid #1e3c72;
        color: #1e3c72;
        border-radius: 8px;
        padding: 15px;
    }
</style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4>Catedrático</h4>
    <hr style="border-color: #ffffff;">
    <a href="panel_catedraticos.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h2 class="mb-4">Calendario de Exámenes y Eventos</h2>

    <?php
    // Obtener registros asignados al catedrático
    $stmt = $pdo->prepare("
        SELECT ce.id, ce.tipo_registro, ce.tipo_examen, ce.fecha, ce.hora,
               a.nombre AS admin_nombre
        FROM calendario_examenes ce
        LEFT JOIN administradores a ON ce.id_administrador = a.id
        WHERE ce.id_catedratico = ?
        ORDER BY ce.fecha, ce.hora
    ");
    $stmt->execute([$catedratico_id]);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($eventos) == 0){
        echo "<p class='alert alert-info'>No tienes exámenes ni eventos asignados.</p>";
    } else {
    ?>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Tipo de Registro</th>
                    <th>Tipo de Examen</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Asignado por</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($eventos as $ev): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($ev['tipo_registro']) ?></td>
                    <td><?= $ev['tipo_examen'] ?? '-' ?></td>
                    <td><?= date('d/m/Y', strtotime($ev['fecha'])) ?></td>
                    <td><?= substr($ev['hora'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($ev['admin_nombre'] ?? 'Administrador') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php } ?>

</div>

</body>
</html>