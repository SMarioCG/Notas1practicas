
<?php
include("conexion.php");

// Crear
if(isset($_POST['guardar'])){
    $id_materia = $_POST['id_materia'];
    $id_catedratico = $_POST['id_catedratico'];
    $semestre = $_POST['semestre'];

    $sql = "INSERT INTO cursos (id_materia, id_catedratico, semestre) 
            VALUES ('$id_materia','$id_catedratico','$semestre')";
    $conexion->query($sql);
    header("Location: cursos.php");
    exit;
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $id_materia = $_POST['id_materia'];
    $id_catedratico = $_POST['id_catedratico'];
    $semestre = $_POST['semestre'];

    $sql = "UPDATE cursos 
            SET id_materia='$id_materia', id_catedratico='$id_catedratico', semestre='$semestre' 
            WHERE id=$id";
    $conexion->query($sql);
    header("Location: cursos.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM cursos WHERE id=$id");
    header("Location: cursos.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM cursos WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT cu.*, m.nombre AS materia, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico
                               FROM cursos cu
                               LEFT JOIN materias m ON cu.id_materia=m.id
                               LEFT JOIN catedraticos ca ON cu.id_catedratico=ca.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <title>Cursos</title>
</head>
<body>
<h2>Gestión de Cursos</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">

    Materia:
    <select name="id_materia">
        <option value="">--Seleccionar--</option>
        <?php
        $materias = $conexion->query("SELECT * FROM materias");
        while($fila = $materias->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_materia']==$fila['id']?'selected':''?>><?= $fila['nombre'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Catedrático:
    <select name="id_catedratico">
        <option value="">--Seleccionar--</option>
        <?php
        $catedraticos = $conexion->query("SELECT * FROM catedraticos");
        while($fila = $catedraticos->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_catedratico']==$fila['id']?'selected':''?>><?= $fila['nombre'].' '.$fila['apellido'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Semestre: <input type="text" name="semestre" value="<?=isset($editar)?$editar['semestre']:'' ?>" required><br><br>

    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="cursos.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Materia</th>
    <th>Catedrático</th>
    <th>Semestre</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['materia'] ?></td>
    <td><?= $fila['catedratico'] ?></td>
    <td><?= $fila['semestre'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar curso?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
