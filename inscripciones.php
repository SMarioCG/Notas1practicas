
<?php
include("conexion.php");

// Crear
// Crear
if(isset($_POST['guardar'])){
    $id_estudiante = $_POST['id_estudiante'] ?? null;
    $id_curso = $_POST['id_curso'] ?? null;
    $fecha = $_POST['fecha_inscripcion'] ?? date('Y-m-d');

    if($id_estudiante && $id_curso){
        $sql = "INSERT INTO inscripciones (id_estudiante, id_curso, fecha_inscripcion) 
                VALUES ('$id_estudiante','$id_curso','$fecha')";
        $conexion->query($sql);
        header("Location: inscripciones.php");
        exit;
    } else {
        echo "Seleccione estudiante y curso.";
    }
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'] ?? null;
    $id_estudiante = $_POST['id_estudiante'] ?? null;
    $id_curso = $_POST['id_curso'] ?? null;
    $fecha = $_POST['fecha_inscripcion'] ?? date('Y-m-d');

    if($id && $id_estudiante && $id_curso){
        $sql = "UPDATE inscripciones 
                SET id_estudiante='$id_estudiante', id_curso='$id_curso', fecha_inscripcion='$fecha' 
                WHERE id=$id";
        $conexion->query($sql);
        header("Location: inscripciones.php");
        exit;
    } else {
        echo "Faltan datos para actualizar.";
    }
}


// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM inscripciones WHERE id=$id");
    header("Location: inscripciones.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM inscripciones WHERE id=$id")->fetch_assoc();
}

// Consultar
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
    <link rel="stylesheet" href="estilos.css">
    <title>Inscripciones</title>
</head>
<body>
<h2>Gestión de Inscripciones</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">

    Estudiante:
    <select name="id_estudiante">
        <option value="">--Seleccionar--</option>
        <?php
        $estudiantes = $conexion->query("SELECT * FROM estudiantes");
        while($fila = $estudiantes->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_estudiante']==$fila['id']?'selected':''?>><?= $fila['nombre'].' '.$fila['apellido'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Curso:
    <select name="id_curso">
        <option value="">--Seleccionar--</option>
        <?php
        $cursos = $conexion->query("SELECT cu.id, m.nombre AS materia, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico FROM cursos cu
                                    LEFT JOIN materias m ON cu.id_materia=m.id
                                    LEFT JOIN catedraticos ca ON cu.id_catedratico=ca.id");
        while($fila = $cursos->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_curso']==$fila['id']?'selected':''?>><?= $fila['materia'].' - '.$fila['catedratico'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Fecha de Inscripción: <input type="date" name="fecha_inscripcion" value="<?=isset($editar)?$editar['fecha_inscripcion']:date('Y-m-d') ?>"><br><br>

    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="inscripciones.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Estudiante</th>
    <th>Materia</th>
    <th>Catedrático</th>
    <th>Semestre</th>
    <th>Fecha</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['estudiante'] ?></td>
    
    <td><?= $fila['materia'] ?></td>
    <td><?= $fila['catedratico'] ?></td>
    <td><?= $fila['semestre'] ?></td>
    <td><?= $fila['fecha_inscripcion'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar inscripción?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
