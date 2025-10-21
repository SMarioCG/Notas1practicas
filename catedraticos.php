
<?php
include("conexion.php");

// Crear
if (isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = hash('sha256', $_POST['password']);

    $sql = "INSERT INTO catedraticos (nombre, apellido, correo, password) 
            VALUES ('$nombre','$apellido','$correo','$password')";
    $conexion->query($sql);
    header("Location: catedraticos.php");
    exit;
}

// Actualizar
if (isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $sql = "UPDATE catedraticos SET nombre='$nombre', apellido='$apellido', correo='$correo' WHERE id=$id";
    $conexion->query($sql);
    header("Location: catedraticos.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM catedraticos WHERE id=$id";
    $conexion->query($sql);
    header("Location: catedraticos.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM catedraticos WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT * FROM catedraticos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilos.css">
    <meta charset="UTF-8">
    <a href="notas.php">Notas</a>
     <a href="calendario_examenes.php">Calendario de Exámenes</a>
    <title>Catedráticos</title>
</head>
<body>
<h2>Gestión de Catedráticos</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">
    Nombre: <input type="text" name="nombre" value="<?=isset($editar)?$editar['nombre']:'' ?>" required><br>
    Apellido: <input type="text" name="apellido" value="<?=isset($editar)?$editar['apellido']:'' ?>" required><br>
    Correo: <input type="email" name="correo" value="<?=isset($editar)?$editar['correo']:'' ?>" required><br>
    <?php if(!isset($editar)): ?>
        Contraseña: <input type="password" name="password" required><br>
    <?php endif; ?>
    <br>
    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="catedraticos.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Apellido</th>
    <th>Correo</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['apellido'] ?></td>
    <td><?= $fila['correo'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar catedrático?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
