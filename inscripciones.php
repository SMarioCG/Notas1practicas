<?php
session_start();
include("conexion.php");

// Verificar sesión activa
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Estudiante') {
    header("Location: login.php");
    exit;
}

$estudiante_id = $_SESSION['id']; // ID del estudiante logueado
$nombre_estudiante = $_SESSION['nombre'];

// === GUARDAR ===
if (isset($_POST['guardar'])) {
    $id_curso = $_POST['id_curso'];
    $fecha = $_POST['fecha_inscripcion'];

    // Evitar duplicados
    $verificar = $conexion->query("SELECT * FROM inscripciones WHERE id_estudiante='$estudiante_id' AND id_curso='$id_curso'");
    if ($verificar->num_rows > 0) {
        echo "<script>alert('⚠️ Ya estás inscrito en este curso');</script>";
    } else {
        $sql = "INSERT INTO inscripciones (id_estudiante, id_curso, fecha_inscripcion) 
                VALUES ('$estudiante_id', '$id_curso', '$fecha')";
        if ($conexion->query($sql)) {
            echo "<script>alert('✅ Inscripción guardada correctamente'); window.location='inscripciones.php';</script>";
        } else {
            echo "<script>alert('❌ Error al guardar la inscripción');</script>";
        }
    }
}

// === ELIMINAR ===
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM inscripciones WHERE id='$id' AND id_estudiante='$estudiante_id'");
    echo "<script>alert('🗑️ Inscripción eliminada'); window.location='inscripciones.php';</script>";
}

// === CONSULTA PRINCIPAL ===
$stmt = $conexion->prepare("
    SELECT i.id, i.fecha_inscripcion, 
           CONCAT(e.nombre,' ',e.apellido) AS estudiante,
           m.nombre AS materia, 
           c.semestre, 
           CONCAT(ca.nombre,' ',ca.apellido) AS catedratico
    FROM inscripciones i
    LEFT JOIN estudiantes e ON i.id_estudiante = e.id
    LEFT JOIN cursos c ON i.id_curso = c.id
    LEFT JOIN materias m ON c.id_materia = m.id
    LEFT JOIN catedraticos ca ON c.id_catedratico = ca.id
    WHERE i.id_estudiante = ?
    ORDER BY c.semestre, m.nombre
");
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Inscripciones - <?= htmlspecialchars($nombre_estudiante) ?></title>
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

/* Sidebar */
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

/* Contenido */
.content {
    flex: 1;
    padding: 40px;
}
.content h2 {
    color: #004383;
    font-weight: 700;
    margin-bottom: 25px;
}
.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 30px;
}

/* Formulario */
form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
form select, form input, form button {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
form select, form input {
    flex: 1;
    background: #f0f2f5;
    color: #004383;
    font-weight: 500;
}
form button {
    background: #004383;
    color: #ffb300;
    font-weight: 600;
    cursor: pointer;
}
form button:hover {
    background: #0059a0;
    color: #fff;
}

/* Tabla */
.table thead { background: #004383; color: #ffb300; }
.table tbody tr:hover { background-color: rgba(0,67,131,0.05); }
.table td, .table th { text-align: center; padding: 12px; }

/* Botones acción */
td a {
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    margin-right: 5px;
    font-size: 0.9em;
}
td a.delete {background:#e74c3c; color:white;}
td a.delete:hover {background:#c0392b;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Mis Inscripciones</h4>
    <hr>
    <a href="panel_estudiante.php"> Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<!-- Contenido -->
<div class="content">
    <h2>📋 Bienvenido, <?= htmlspecialchars($nombre_estudiante) ?></h2>

    <!-- Formulario -->
    <div class="card">
        <h4>➕ Nueva Inscripción</h4>
        <form method="post">
            <select name="id_curso" required>
                <option value="">--Seleccionar Curso--</option>
                <?php
                $cursos = $conexion->query("
                    SELECT cu.id, m.nombre AS materia, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico 
                    FROM cursos cu
                    LEFT JOIN materias m ON cu.id_materia=m.id
                    LEFT JOIN catedraticos ca ON cu.id_catedratico=ca.id
                ");
                while($fila = $cursos->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>">
                        <?= $fila['materia'].' - '.$fila['catedratico'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="date" name="fecha_inscripcion" value="<?= date('Y-m-d') ?>">
            <button type="submit" name="guardar">Guardar</button>
        </form>
    </div>

    <!-- Tabla de inscripciones -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Materia</th>
                <th>Catedrático</th>
                <th>Semestre</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $fila['id'] ?></td>
                <td><?= htmlspecialchars($fila['estudiante']) ?></td>
                <td><?= htmlspecialchars($fila['materia']) ?></td>
                <td><?= htmlspecialchars($fila['catedratico']) ?></td>
                <td><?= htmlspecialchars($fila['semestre']) ?></td>
                <td><?= htmlspecialchars($fila['fecha_inscripcion']) ?></td>
                <td>
                    <a href="inscripciones.php?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Seguro que deseas eliminar esta inscripción?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

