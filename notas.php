
<?php
include("conexion.php");

// Crear
if(isset($_POST['guardar'])){
    $id_inscripcion = $_POST['id_inscripcion'];
    $zona = $_POST['zona'];
    $fase_1 = $_POST['fase_1'];
    $fase_2 = $_POST['fase_2'];
    $fase_final = $_POST['fase_final'];
    $observaciones = $_POST['observaciones'];

    $sql = "INSERT INTO notas (id_inscripcion, zona, fase_1, fase_2, fase_final, observaciones) 
            VALUES ('$id_inscripcion','$zona','$fase_1','$fase_2','$fase_final','$observaciones')";
    $conexion->query($sql);
    header("Location: notas.php");
    exit;
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $zona = $_POST['zona'];
    $fase_1 = $_POST['fase_1'];
    $fase_2 = $_POST['fase_2'];
    $fase_final = $_POST['fase_final'];
    $observaciones = $_POST['observaciones'];

    $sql = "UPDATE notas 
            SET zona='$zona', fase_1='$fase_1', fase_2='$fase_2', fase_final='$fase_final', observaciones='$observaciones' 
            WHERE id=$id";
    $conexion->query($sql);
    header("Location: notas.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM notas WHERE id=$id");
    header("Location: notas.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM notas WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT n.*, e.nombre AS estudiante, m.nombre AS materia
                               FROM notas n
                               LEFT JOIN inscripciones i ON n.id_inscripcion=i.id
                               LEFT JOIN estudiantes e ON i.id_estudiante=e.id
                               LEFT JOIN cursos c ON i.id_curso=c.id
                               LEFT JOIN materias m ON c.id_materia=m.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <title>Notas</title>
</head>
<body>
<h2>Gestión de Notas</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">

    Inscripción:
    <select name="id_inscripcion">
        <option value="">--Seleccionar--</option>
        <?php
        $inscripciones = $conexion->query("SELECT i.id, e.nombre AS estudiante, m.nombre AS materia
                                           FROM inscripciones i
                                           LEFT JOIN estudiantes e ON i.id_estudiante=e.id
                                           LEFT JOIN cursos c ON i.id_curso=c.id
                                           LEFT JOIN materias m ON c.id_materia=m.id");
        while($fila = $inscripciones->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_inscripcion']==$fila['id']?'selected':''?>><?= $fila['estudiante'].' - '.$fila['materia'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Zona: <input type="number" step="0.01" name="zona" value="<?=isset($editar)?$editar['zona']:0?>"><br>
    Fase 1: <input type="number" step="0.01" name="fase_1" value="<?=isset($editar)?$editar['fase_1']:0?>"><br>
    Fase 2: <input type="number" step="0.01" name="fase_2" value="<?=isset($editar)?$editar['fase_2']:0?>"><br>
    Fase Final: <input type="number" step="0.01" name="fase_final" value="<?=isset($editar)?$editar['fase_final']:0?>"><br>
    Observaciones: <input type="text" name="observaciones" value="<?=isset($editar)?$editar['observaciones']:''?>"><br><br>

    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="notas.php">Cancelar</a>
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
    <th>Zona</th>
    <th>Fase 1</th>
    <th>Fase 2</th>
    <th>Fase Final</th>
    <th>Nota Final</th>
    <th>Observaciones</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['estudiante'] ?></td>
    <td><?= $fila['materia'] ?></td>
    <td><?= $fila['zona'] ?></td>
    <td><?= $fila['fase_1'] ?></td>
    <td><?= $fila['fase_2'] ?></td>
    <td><?= $fila['fase_final'] ?></td>
    <td><?= $fila['nota_final'] ?></td>
    <td><?= $fila['observaciones'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar nota?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
