<?php
include("conexion.php");

// Editar y Consultar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM inscripciones WHERE id=$id")->fetch_assoc();
}

$resultado = $conexion->query("SELECT i.*, e.nombre AS estudiante, c.semestre, m.nombre AS materia, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico
                               FROM inscripciones i
                               LEFT JOIN estudiantes e ON i.id_estudiante=e.id
                               LEFT JOIN cursos c ON i.id_curso=c.id
                               LEFT JOIN materias m ON c.id_materia=m.id
                               LEFT JOIN catedraticos ca ON c.id_catedratico=ca.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inscripciones - Estudiante</title>
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
    transition: transform 0.2s;
}
.card:hover { transform: translateY(-3px); }

/* Formulario */
form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
form input, form select, form button {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline: none;
}
form input, form select {
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
    transition: all 0.3s;
}
form button:hover {
    background: #0059a0;
    color: #fff;
}

/* Tabla */
.table-container { overflow-x:auto; }
.table thead { background: #004383; color: #ffb300; }
.table tbody tr:hover { background-color: rgba(0,67,131,0.05); }
.table td, .table th { text-align: center; padding: 12px; }
.table td:first-child { font-weight: 500; }

/* Botones de acción */
td a {
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    margin-right: 5px;
    font-size: 0.9em;
}
td a.edit {background:#ffb300; color:#004383; font-weight:600;}
td a.edit:hover {background:#e6a100;}
td a.delete {background:#e74c3c;}
td a.delete:hover {background:#c0392b;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Gestión de Inscripciones</h4>
    <hr>
    <a href="panel_estudiante.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</div>

<!-- Contenido principal -->
<div class="content">
    <h2>📋 Inscripciones</h2>

    <!-- Formulario -->
    <div class="card">
        <h4>➕ <?= isset($editar)? "Editar Inscripción" : "Agregar Inscripción" ?></h4>
        <form method="post">
            <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">

            <select name="id_estudiante" required>
                <option value="">--Seleccionar Estudiante--</option>
                <?php
                $estudiantes = $conexion->query("SELECT * FROM estudiantes");
                while($fila = $estudiantes->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>" <?= isset($editar) && $editar['id_estudiante']==$fila['id']?'selected':'' ?>>
                        <?= $fila['nombre'].' '.$fila['apellido'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="id_curso" required>
                <option value="">--Seleccionar Curso--</option>
                <?php
                $cursos = $conexion->query("SELECT cu.id, m.nombre AS materia, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico FROM cursos cu
                                            LEFT JOIN materias m ON cu.id_materia=m.id
                                            LEFT JOIN catedraticos ca ON cu.id_catedratico=ca.id");
                while($fila = $cursos->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>" <?= isset($editar) && $editar['id_curso']==$fila['id']?'selected':'' ?>>
                        <?= $fila['materia'].' - '.$fila['catedratico'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="date" name="fecha_inscripcion" value="<?= isset($editar)?$editar['fecha_inscripcion']:date('Y-m-d') ?>">

            <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>">
                <?= isset($editar)?'Actualizar':'Guardar' ?>
            </button>
        </form>
    </div>

    <!-- Tabla de inscripciones -->
    <div class="card table-container">
        <table class="table table-bordered table-hover">
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
                    <td><?= $fila['estudiante'] ?></td>
                    <td><?= $fila['materia'] ?></td>
                    <td><?= $fila['catedratico'] ?></td>
                    <td><?= $fila['semestre'] ?></td>
                    <td><?= $fila['fecha_inscripcion'] ?></td>
                    <td>
                        <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
                        <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar inscripción?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
