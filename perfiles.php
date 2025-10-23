<?php
include("conexion.php");

// Crear
if (isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $sql = "INSERT INTO perfiles (nombre) VALUES ('$nombre')";
    $conexion->query($sql);
    header("Location: perfiles.php");
    exit;
}

// Actualizar
if (isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $sql = "UPDATE perfiles SET nombre='$nombre' WHERE id=$id";
    $conexion->query($sql);
    header("Location: perfiles.php");
    exit;
}

// Eliminar
if(isset ($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM perfiles WHERE id=$id";
    $conexion->query($sql);
    header("Location: perfiles.php");
    exit;
}

// Editar
if(isset ($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM perfiles WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT * FROM perfiles");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfiles</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<h2>Gestión de Perfiles</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">
    Nombre: <input type="text" name="nombre" value="<?=isset($editar)?$editar['nombre']:'' ?>" required><br><br>

    <?php if (isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="perfiles.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>
    <?php while($fila = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $fila['id'] ?></td>
        <td><?= $fila['nombre'] ?></td>
        <td>
            <a href="?editar=<?= $fila['id'] ?>">Editar</a>
            <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar perfil?')">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
