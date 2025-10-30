<?php
// ======== CONEXIÓN A LA BASE DE DATOS ========
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$nombre_estudiante = '';
$notas = [];
$promedio = 0;
$error = '';

// ======== PROCESAR CORREO INGRESADO ========
if(isset($_POST['consultar'])){
    $correo = trim($_POST['correo'] ?? '');
    if($correo){
        // Buscar estudiante por correo
        $stmt = $pdo->prepare("SELECT id, nombre, apellido FROM estudiantes WHERE correo_estudiante = ?");
        $stmt->execute([$correo]);
        $est = $stmt->fetch(PDO::FETCH_ASSOC);

        if($est){
            $estudiante_id = $est['id'];
            $nombre_estudiante = $est['nombre'] . " " . $est['apellido'];

            // Consultar notas
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

            // Calcular promedio
            if(count($notas) > 0){
                $total = array_sum(array_column($notas, 'nota_final'));
                $promedio = round($total / count($notas), 2);
            }
        } else {
            $error = "No se encontró ningún estudiante con ese correo.";
        }
    } else {
        $error = "Ingrese un correo válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Consultar Notas - Estudiante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    display: flex;
    min-height: 100vh;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    color: #333;
}
.sidebar {
    width: 230px;
    background: #004383;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 25px 20px;
    box-shadow: 3px 0 10px rgba(0,0,0,0.3);
}
.sidebar h4 { font-weight:700; font-size:1.2em; text-align:center; color:#ffb300; margin-bottom:15px; }
.sidebar hr { border:none; height:2px; background: rgba(255,255,255,0.3); margin:10px 0 20px; }
.sidebar a { color:#fff; text-decoration:none; display:block; padding:12px 15px; margin-bottom:8px; border-radius:8px; font-weight:500; transition: all 0.3s ease;}
.sidebar a:hover { background: rgba(255,255,255,0.15); color:#ffb300; transform:translateX(4px); }

.content { flex:1; padding:40px; }
.content h2 { color:#004383; font-weight:700; margin-bottom:25px; display:flex; align-items:center; gap:10px; }

.card { background:#fff; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); padding:25px; margin-bottom:30px; }
.card:hover { transform: translateY(-3px); }

form input, form button { padding:10px; border-radius:8px; border:1px solid #ccc; outline:none; }
form input { flex:1; margin-right:10px; }
form button { background:#004383; color:#ffb300; font-weight:600; cursor:pointer; transition: all 0.3s; }
form button:hover { background:#0059a0; color:#fff; }

.table-container { overflow-x:auto; }
.table thead { background-color:#004383; color:#fff; text-transform:uppercase; font-size:0.9em; }
.table tbody tr:hover { background-color: rgba(0,67,131,0.05); }
.table td, .table th { vertical-align:middle !important; text-align:center; padding:12px; }
.table td:first-child { font-weight:500; }

.alert { border-radius:10px; padding:15px; }
.alert-warning { background: rgba(255,193,7,0.1); border:1px solid #ffb300; color:#004383; }
</style>
</head>
<body>

<div class="sidebar">
    <h4>Consultar Notas</h4>
    <hr>
    <a href="panel_estudiante.php">Volver a panel principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h2>📋 Consulta tus notas</h2>

    <div class="card d-flex flex-row mb-4">
        <form method="post" class="d-flex w-100">
            <input type="email" name="correo" placeholder="Ingresa tu correo" required>
            <button type="submit" name="consultar">Consultar</button>
        </form>
    </div>

    <?php if($error): ?>
        <div class="alert alert-warning text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if($nombre_estudiante): ?>
        <div class="card p-4 mb-4 text-center">
            <h4 class="text-primary mb-2">Estudiante: <?= htmlspecialchars($nombre_estudiante) ?></h4>
            <h4 class="text-primary mb-2">Promedio General: <?= number_format($promedio,2) ?></h4>
        </div>

        <div class="card p-4 table-container">
            <h4 class="mb-3 text-primary">Calificaciones por Materia y Semestre</h4>
            <?php if(count($notas)>0): ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Semestre</th>
                        <th>Nota Final</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notas as $n): ?>
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
                <div class="alert alert-warning text-center">No se encontraron calificaciones registradas.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
