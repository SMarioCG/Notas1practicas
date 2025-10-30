<?php
// conexion.php
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();

$_SESSION['id_catedratico'] = 1; // Simular sesión
$catedratico_id = $_SESSION['id_catedratico'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Catedrático</title>
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
    <h4>CATEDRÁTICO</h4>
    <hr class="border-light">
    <a href="panel_catedraticos.php">Volver al Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h2>Subir Notas</h2>

    <!-- Mostrar cursos como cards -->
    <div class="row">
    <?php
    $stmt = $pdo->prepare("
        SELECT c.id, m.nombre AS materia, c.semestre 
        FROM cursos c
        JOIN materias m ON c.id_materia = m.id
        WHERE c.id_catedratico = ?
    ");
    $stmt->execute([$catedratico_id]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($cursos as $curso):
    ?>
        <div class="col-md-4 mb-3">
            <div class="card" onclick="window.location='?curso_id=<?= $curso['id'] ?>'">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($curso['materia']) ?></h5>
                    <p class="card-text">Semestre: <?= htmlspecialchars($curso['semestre']) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <?php if(isset($_GET['curso_id']) && $_GET['curso_id'] != ''): 
        $curso_id = $_GET['curso_id'];

        $stmt = $pdo->prepare("
            SELECT i.id AS id_inscripcion, e.nombre AS estudiante, n.id AS nota_id, 
                   n.zona, n.fase_1, n.fase_2, n.fase_final, n.observaciones
            FROM inscripciones i
            JOIN estudiantes e ON i.id_estudiante = e.id
            LEFT JOIN notas n ON n.id_inscripcion = i.id
            WHERE i.id_curso = ?
        ");
        $stmt->execute([$curso_id]);
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="mt-4">
        <h4 style="color:#004383;"> Agregar Notas - 
            <span style="color:#ffb300;">
                <?= htmlspecialchars($cursos[array_search($curso_id, array_column($cursos, 'id'))]['materia']) ?>
            </span>
        </h4>

        <form method="POST">
            <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Zona</th>
                        <th>Fase 1</th>
                        <th>Fase 2</th>
                        <th>Fase Final</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($estudiantes as $est): ?>
                    <tr>
                        <td><?= htmlspecialchars($est['estudiante']) ?></td>
                        <?php if($est['nota_id']): ?>
                            <td><input type="number" value="<?= $est['zona'] ?>" class="form-control" readonly></td>
                            <td><input type="number" value="<?= $est['fase_1'] ?>" class="form-control" readonly></td>
                            <td><input type="number" value="<?= $est['fase_2'] ?>" class="form-control" readonly></td>
                            <td><input type="number" value="<?= $est['fase_final'] ?>" class="form-control" readonly></td>
                            <td><input type="text" value="<?= htmlspecialchars($est['observaciones']) ?>" class="form-control" readonly></td>
                        <?php else: ?>
                            <td><input type="number" step="0.01" name="notas[<?= $est['id_inscripcion'] ?>][zona]" class="form-control" required></td>
                            <td><input type="number" step="0.01" name="notas[<?= $est['id_inscripcion'] ?>][fase_1]" class="form-control" required></td>
                            <td><input type="number" step="0.01" name="notas[<?= $est['id_inscripcion'] ?>][fase_2]" class="form-control" required></td>
                            <td><input type="number" step="0.01" name="notas[<?= $est['id_inscripcion'] ?>][fase_final]" class="form-control" required></td>
                            <td><input type="text" name="notas[<?= $est['id_inscripcion'] ?>][observaciones]" class="form-control"></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="guardar_notas" class="btn btn-success mt-3">💾 Guardar Notas Nuevas</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php
if(isset($_POST['guardar_notas'])) {
    $notas = $_POST['notas'];
    foreach($notas as $id_inscripcion => $data) {
        $stmt = $pdo->prepare("SELECT id FROM notas WHERE id_inscripcion = ?");
        $stmt->execute([$id_inscripcion]);
        if(!$stmt->fetchColumn()) {
            $stmt = $pdo->prepare("
                INSERT INTO notas (id_inscripcion, zona, fase_1, fase_2, fase_final, observaciones)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $id_inscripcion,
                $data['zona'],
                $data['fase_1'],
                $data['fase_2'],
                $data['fase_final'],
                $data['observaciones']
            ]);
        }
    }
    echo "<script>alert('Notas agregadas correctamente'); window.location='';</script>";
}
?>
