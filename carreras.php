
<?php
include("conexion.php");

// Crear
if(isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $sql = "INSERT INTO carreras (nombre, descripcion) VALUES ('$nombre','$descripcion')";
    $conexion->query($sql);
    header("Location: carreras.php");
    exit;
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $sql = "UPDATE carreras SET nombre='$nombre', descripcion='$descripcion' WHERE id=$id";
    $conexion->query($sql);
    header("Location: carreras.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM carreras WHERE id=$id";
    $conexion->query($sql);
    header("Location: carreras.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM carreras WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT * FROM carreras");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <title>Carreras</title>
</head>
<body>
<h2>Gestión de Carreras</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">
    Nombre: <input type="text" name="nombre" value="<?=isset($editar)?$editar['nombre']:'' ?>" required><br>
    Descripción: <input type="text" name="descripcion" value="<?=isset($editar)?$editar['descripcion']:'' ?>"><br><br>

    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="carreras.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['descripcion'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar carrera?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
