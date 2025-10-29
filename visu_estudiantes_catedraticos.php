<?php
// conexion.php
$host = "localhost";
$db = "notasregional2";
$user = "root";
$pass = "pequeñocesar2025";
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();

// Simular sesión del catedrático
$_SESSION['id_catedratico'] = 1;
$catedratico_id = $_SESSION['id_catedratico'];

// Verificar si se ha seleccionado un curso
$curso_seleccionado = isset($_GET['curso_id']) ? $_GET['curso_id'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estudiantes - Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { display: flex; min-height: 100vh; margin: 0; }
    .sidebar { width: 220px; background: #343a40; color: #fff; }
    .sidebar a { color: #fff; text-decoration: none; display: block; padding: 12px 15px; }
    .sidebar a:hover { background: #495057; }
    .content { flex: 1; padding: 20px; }
    .table thead { background-color: #007bff; color: #fff; }
    .curso-btn { margin: 5px; }
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
    <h2 class="mb-4">Estudiantes Inscritos y Desempeño Académico</h2>

    <?php
    // Obtener todos los cursos del catedrático
    $stmt_cursos = $pdo->prepare("
        SELECT c.id, m.nombre AS materia, c.semestre
        FROM cursos c
        JOIN materias m ON c.id_materia = m.id
        WHERE c.id_catedratico = ?
        ORDER BY m.nombre
    ");
    $stmt_cursos->execute([$catedratico_id]);
    $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

    if(count($cursos) == 0){
        echo "<p class='alert alert-info'>No tienes cursos asignados.</p>";
    } else {
        // Mostrar botones de cursos
        echo "<div class='mb-4'>";
        echo "<h4>Selecciona un curso:</h4>";
        echo "<div class='d-flex flex-wrap'>";
        
        foreach($cursos as $curso) {
            $activo = ($curso_seleccionado == $curso['id']) ? 'btn-primary' : 'btn-outline-primary';
            echo "<a href='?curso_id={$curso['id']}' class='btn $activo curso-btn'>";
            echo htmlspecialchars($curso['materia']) . " - Semestre " . htmlspecialchars($curso['semestre']);
            echo "</a>";
        }
        
        echo "</div>";
        echo "</div>";
        
        // Si se ha seleccionado un curso, mostrar sus estudiantes
        if($curso_seleccionado) {
            // Obtener los estudiantes del curso seleccionado
            $stmt = $pdo->prepare("
                SELECT 
                    e.nombre AS estudiante,
                    m.nombre AS materia,
                    c.semestre,
                    n.zona, n.fase_1, n.fase_2, n.fase_final, n.nota_final, n.observaciones
                FROM inscripciones i
                JOIN estudiantes e ON i.id_estudiante = e.id
                JOIN cursos c ON i.id_curso = c.id
                JOIN materias m ON c.id_materia = m.id
                LEFT JOIN notas n ON n.id_inscripcion = i.id
                WHERE c.id = ? AND c.id_catedratico = ?
                ORDER BY e.nombre
            ");
            $stmt->execute([$curso_seleccionado, $catedratico_id]);
            $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($estudiantes) == 0){
                echo "<p class='alert alert-info'>No hay estudiantes inscritos en este curso.</p>";
            } else {
                // Encontrar el nombre del curso seleccionado
                $curso_actual = null;
                foreach($cursos as $curso) {
                    if($curso['id'] == $curso_seleccionado) {
                        $curso_actual = $curso;
                        break;
                    }
                }
                
                echo "<h4>Estudiantes de: " . htmlspecialchars($curso_actual['materia']) . " - Semestre " . htmlspecialchars($curso_actual['semestre']) . "</h4>";
    ?>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Materia</th>
                    <th>Semestre</th>
                    <th>Zona</th>
                    <th>Fase 1</th>
                    <th>Fase 2</th>
                    <th>Fase Final</th>
                    <th>Nota Final</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($estudiantes as $est): ?>
                <tr>
                    <td><?= htmlspecialchars($est['estudiante']) ?></td>
                    <td><?= htmlspecialchars($est['materia']) ?></td>
                    <td><?= htmlspecialchars($est['semestre']) ?></td>
                    <td><?= $est['zona'] !== null ? $est['zona'] : '-' ?></td>
                    <td><?= $est['fase_1'] !== null ? $est['fase_1'] : '-' ?></td>
                    <td><?= $est['fase_2'] !== null ? $est['fase_2'] : '-' ?></td>
                    <td><?= $est['fase_final'] !== null ? $est['fase_final'] : '-' ?></td>
                    <td><?= $est['nota_final'] !== null ? $est['nota_final'] : '-' ?></td>
                    <td><?= htmlspecialchars($est['observaciones'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php 
            }
        } else {
            echo "<p class='alert alert-info'>Selecciona un curso para ver sus estudiantes.</p>";
        }
    } 
    ?>

</div>

</body>
</html>