
<?php
include("conexion.php");

// Crear
if(isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $codigo = $_POST['codigo'];
    $creditos = $_POST['creditos'];
    $id_carrera = $_POST['id_carrera'];

    $sql = "INSERT INTO materias (nombre, codigo, creditos, id_carrera) 
            VALUES ('$nombre','$codigo','$creditos','$id_carrera')";
    $conexion->query($sql);
    header("Location: materias.php");
    exit;
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $codigo = $_POST['codigo'];
    $creditos = $_POST['creditos'];
    $id_carrera = $_POST['id_carrera'];

    $sql = "UPDATE materias 
            SET nombre='$nombre', codigo='$codigo', creditos='$creditos', id_carrera='$id_carrera' 
            WHERE id=$id";
    $conexion->query($sql);
    header("Location: materias.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM materias WHERE id=$id");
    header("Location: materias.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM materias WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT m.*, c.nombre AS carrera FROM materias m
                               LEFT JOIN carreras c ON m.id_carrera=c.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <title>Materias</title>
</head>
<body>
<h2>Gestión de Materias</h2>
<a href="index.php">Portal Estudiante</a>
<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">
    Nombre: <input type="text" name="nombre" value="<?=isset($editar)?$editar['nombre']:'' ?>" required><br>
    Código: <input type="text" name="codigo" value="<?=isset($editar)?$editar['codigo']:'' ?>" required><br>
    Créditos: <input type="number" name="creditos" value="<?=isset($editar)?$editar['creditos']:'' ?>" required><br>
    Carrera:
    <select name="id_carrera">
        <option value="">--Seleccionar--</option>
        <?php
        $carreras = $conexion->query("SELECT * FROM carreras");
        while($fila = $carreras->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_carrera']==$fila['id']?'selected':''?>><?= $fila['nombre'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="materias.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Código</th>
    <th>Créditos</th>
    <th>Carrera</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['codigo'] ?></td>
    <td><?= $fila['creditos'] ?></td>
    <td><?= $fila['carrera'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar materia?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
