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
<title>Mis Cursos - Cursos Asignados</title>
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
    .card { 
        transition: 0.3s; 
        border: 2px solid #1e3c72;
        border-radius: 12px;
        height: 100%;
    }
    .card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 8px 20px rgba(30,60,114,0.3);
        border-color: #3a6fd9;
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
    .card-title {
        color: #1e3c72;
        font-weight: 700;
        font-size: 1.3rem;
    }
    .card-text {
        color: #333333;
        margin-bottom: 8px;
    }
    .alert-info {
        background: #d1ecf1;
        border: 1px solid #1e3c72;
        color: #1e3c72;
        border-radius: 8px;
    }
</style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4>Catedrático</h4>
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
        <div class="col-md-4 mb-4">
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