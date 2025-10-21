<?php
include("conexion.php");

// Crear
if(isset($_POST['guardar'])){
    $tipo_registro = $_POST['tipo_registro'];
    $tipo_examen = $_POST['tipo_examen'] ?: NULL;
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_administrador = $_POST['id_administrador'] ?: NULL;
    $id_catedratico = $_POST['id_catedratico'] ?: NULL;

    $sql = "INSERT INTO calendario_examenes (tipo_registro, tipo_examen, fecha, hora, id_administrador, id_catedratico)
            VALUES ('$tipo_registro', ".($tipo_examen? "'$tipo_examen'": "NULL").", '$fecha', '$hora', ".($id_administrador?$id_administrador:"NULL").", ".($id_catedratico?$id_catedratico:"NULL").")";
    $conexion->query($sql);
    header("Location: calendario_examenes.php");
    exit;
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $tipo_registro = $_POST['tipo_registro'];
    $tipo_examen = $_POST['tipo_examen'] ?: NULL;
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_administrador = $_POST['id_administrador'] ?: NULL;
    $id_catedratico = $_POST['id_catedratico'] ?: NULL;

    $sql = "UPDATE calendario_examenes 
            SET tipo_registro='$tipo_registro', tipo_examen=".($tipo_examen? "'$tipo_examen'": "NULL").", fecha='$fecha', hora='$hora', id_administrador=".($id_administrador?$id_administrador:"NULL").", id_catedratico=".($id_catedratico?$id_catedratico:"NULL")."
            WHERE id=$id";
    $conexion->query($sql);
    header("Location: calendario_examenes.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM calendario_examenes WHERE id=$id");
    header("Location: calendario_examenes.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM calendario_examenes WHERE id=$id")->fetch_assoc();
}

// Consultar
$resultado = $conexion->query("SELECT ce.*, a.nombre AS admin, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico
                               FROM calendario_examenes ce
                               LEFT JOIN administradores a ON ce.id_administrador=a.id
                               LEFT JOIN catedraticos ca ON ce.id_catedratico=ca.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <title>Calendario de Exámenes</title>
</head>
<body>
<h2>Gestión de Calendario de Exámenes</h2>

<form method="post">
    <input type="hidden" name="id" value="<?=isset($editar)?$editar['id']:'' ?>">

    Tipo de Registro:
    <select name="tipo_registro" required>
        <option value="">--Seleccionar--</option>
        <option value="Examen" <?=isset($editar) && $editar['tipo_registro']=='Examen'?'selected':''?>>Examen</option>
        <option value="Clase Presencial" <?=isset($editar) && $editar['tipo_registro']=='Clase Presencial'?'selected':''?>>Clase Presencial</option>
        <option value="Clase Virtual" <?=isset($editar) && $editar['tipo_registro']=='Clase Virtual'?'selected':''?>>Clase Virtual</option>
        <option value="Evento" <?=isset($editar) && $editar['tipo_registro']=='Evento'?'selected':''?>>Evento</option>
    </select><br>

    Tipo de Examen:
    <select name="tipo_examen">
        <option value="">--Seleccionar--</option>
        <option value="Parcial 1" <?=isset($editar) && $editar['tipo_examen']=='Parcial 1'?'selected':''?>>Parcial 1</option>
        <option value="Parcial 2" <?=isset($editar) && $editar['tipo_examen']=='Parcial 2'?'selected':''?>>Parcial 2</option>
        <option value="Final" <?=isset($editar) && $editar['tipo_examen']=='Final'?'selected':''?>>Final</option>
    </select><br>

    Fecha: <input type="date" name="fecha" value="<?=isset($editar)?$editar['fecha']:date('Y-m-d')?>" required><br>
    Hora: <input type="time" name="hora" value="<?=isset($editar)?$editar['hora']:date('H:i')?>" required><br>

    Administrador:
    <select name="id_administrador">
        <option value="">--Seleccionar--</option>
        <?php
        $admins = $conexion->query("SELECT * FROM administradores");
        while($fila = $admins->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_administrador']==$fila['id']?'selected':''?>><?= $fila['nombre'].' '.$fila['apellido'] ?></option>
        <?php endwhile; ?>
    </select><br>

    Catedrático:
    <select name="id_catedratico">
        <option value="">--Seleccionar--</option>
        <?php
        $cateds = $conexion->query("SELECT * FROM catedraticos");
        while($fila = $cateds->fetch_assoc()):
        ?>
            <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_catedratico']==$fila['id']?'selected':''?>><?= $fila['nombre'].' '.$fila['apellido'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <?php if(isset($editar)): ?>
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="calendario_examenes.php">Cancelar</a>
    <?php else: ?>
        <input type="submit" name="guardar" value="Guardar">
    <?php endif; ?>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Tipo Registro</th>
    <th>Tipo Examen</th>
    <th>Fecha</th>
    <th>Hora</th>
    <th>Administrador</th>
    <th>Catedrático</th>
    <th>Acciones</th>
</tr>
<?php while($fila = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['tipo_registro'] ?></td>
    <td><?= $fila['tipo_examen'] ?></td>
    <td><?= $fila['fecha'] ?></td>
    <td><?= $fila['hora'] ?></td>
    <td><?= $fila['admin'] ?></td>
    <td><?= $fila['catedratico'] ?></td>
    <td>
        <a href="?editar=<?= $fila['id'] ?>">Editar</a>
        <a href="?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar registro?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>

