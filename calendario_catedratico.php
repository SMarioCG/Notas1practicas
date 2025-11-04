
<?php
// conexion.php
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "admin123";
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
        background: #f8f9fa;
        font-family: 'Segoe UI', sans-serif;
    }

    /* === MENÚ LATERAL === */
    .sidebar { 
        width: 220px; 
        background: #004383; 
        color: #fff; 
    }
    .sidebar h4 {
        text-align: center;
        margin-top: 20px;
        color: #ffb300; /* Mostaza */
        font-weight: bold;
    }
    .sidebar a { 
        color: #fff; 
        text-decoration: none; 
        display: block; 
        padding: 12px 15px; 
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .sidebar a:hover { 
        background: rgba(255,255,255,0.1);
        border-left: 4px solid #ffb300; 
    }

    /* === CONTENIDO === */
    .content { 
        flex: 1; 
        padding: 30px; 
    }
    .content h2 {
        color: #004383;
        font-weight: bold;
        margin-bottom: 25px;
    }

    /* === TARJETAS DE CURSOS === */
    .card { 
        cursor: pointer; 
        transition: 0.3s; 
        border: none;
        background: #004383; 
    }
    .card:hover { 
        transform: scale(1.05); 
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    }
    .card .card-body {
        color: #fff;
    }
    .card-title {
        color: #ffb300; /* Mostaza para destacar nombres */
        font-weight: bold;
    }

    /* === TABLA === */
    table thead {
        background-color: #004383;
        color: #fff;
    }
    table tbody tr:hover {
        background-color: #e9f0ff;
    }

    /* === BOTÓN GUARDAR === */
    .btn-success {
        background-color: #ffb300;
        border: none;
        color: #000;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-success:hover {
        background-color: #e6a000;
        color: #fff;
    }

</style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center">Catedrático</h4>
    <hr>
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
                    <td><?= htmlspecialchars($ev['tipo_registro']) ?></td>
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
