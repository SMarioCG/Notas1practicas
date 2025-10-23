
<?php
include("conexion.php");

// ===================== CREAR =====================
if (isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = hash('sha256', $_POST['password']); // SHA2
    $id_carrera = $_POST['id_carrera'];

    $sql = "INSERT INTO estudiantes (nombre, apellido, correo, password, id_carrera) 
            VALUES ('$nombre', '$apellido', '$correo', '$password', '$id_carrera')";
    $conexion->query($sql);
    header("Location: estudiantes.php");
    exit;
}

// ===================== ACTUALIZAR =====================
if (isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $id_carrera = $_POST['id_carrera'];

    $sql = "UPDATE estudiantes 
            SET nombre='$nombre', apellido='$apellido', correo='$correo', id_carrera='$id_carrera' 
            WHERE id=$id";
    $conexion->query($sql);
    header("Location: estudiantes.php");
    exit;
}

// ===================== ELIMINAR =====================
if(isset ($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM estudiantes WHERE id=$id";
    $conexion->query($sql);
    header("Location: estudiantes.php");
    exit;
}

// ===================== EDITAR =====================
if(isset ($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM estudiantes WHERE id=$id")->fetch_assoc();
    
}

// ===================== CONSULTAR =====================
$resultado = $conexion->query("SELECT e.*, c.nombre AS carrera FROM estudiantes e 
                               LEFT JOIN carreras c ON e.id_carrera=c.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Estudiantes</title>
</head>
<body>
<h2>Gestión de Estudiantes</h2>
<a href="index.php">Portal Estudiante</a>
<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">
    Nombre: <input type="text" name="nombre" value ="<?=isset($editar)?$editar['nombre']:'' ?>" required><br>
    Apellido: <input type="text" name="apellido" value ="<?=isset($editar)?$editar['apellido']:'' ?>"><br>
    Correo: <input type="email" name="correo" value ="<?=isset($editar)?$editar['correo']:'' ?>" required><br>
    <?php if(!isset($editar)): ?>
        Contraseña: <input type="password" name="password" required><br>
    <?php endif; ?>
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

    <?php if (isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="estudiantes.php">Cancelar</a>
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
        <th>Carrera</th>
        <th>Acciones</th>
    </tr>
    <?php while($fila = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $fila['id'] ?></td>
        <td><?= $fila['nombre'] ?></td>
        <td><?= $fila['apellido'] ?></td>
        <td><?= $fila['correo'] ?></td>
        <td><?= $fila['carrera'] ?></td>
        <td>
            <a href="?editar=<?= $fila['id'] ?>">Editar</a>
            <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar estudiante?')">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
