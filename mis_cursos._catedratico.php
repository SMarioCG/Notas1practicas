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
<title>Mis Cursos - Cursos Asignados</title>
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
    <a href="panel_catedraticos.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h2 class="mb-4">Mis Cursos - Este Semestre</h2>

    <div class="row">
    <?php
    // Obtener cursos asignados al catedrático
    $stmt = $pdo->prepare("
        SELECT c.id AS id_curso, m.nombre AS materia, m.codigo, c.semestre, ca.nombre AS carrera
        FROM cursos c
        JOIN materias m ON c.id_materia = m.id
        JOIN carreras ca ON m.id_carrera = ca.id
        WHERE c.id_catedratico = ?
        ORDER BY c.semestre
    ");
    $stmt->execute([$catedratico_id]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($cursos) == 0){
        echo "<p class='alert alert-info'>No tienes cursos asignados este semestre.</p>";
    } else {
        foreach($cursos as $curso):
    ?>
        <div class="col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($curso['materia']) ?></h5>
                    <p class="card-text"><strong>Código:</strong> <?= htmlspecialchars($curso['codigo']) ?></p>
                    <p class="card-text"><strong>Semestre:</strong> <?= htmlspecialchars($curso['semestre']) ?></p>
                    <p class="card-text"><strong>Carrera:</strong> <?= htmlspecialchars($curso['carrera']) ?></p>
                </div>
            </div>
        </div>
    <?php 
        endforeach;
    }
    ?>
    </div>
</div>

</body>
</html>
