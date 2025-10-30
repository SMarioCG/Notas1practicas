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
    <title>Dashboard de Gestión de Notas</title>
    <style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:#fff;color:#fff;display:flex;}

/* Sidebar */
nav{
    width:240px;
    background:#004383;
    height:100vh;
    padding-top:60px;
    position:fixed;
}
nav a{
    display:block;
    padding:15px 25px;
    color:#fff;
    text-decoration:none;
    border-left:4px solid transparent;
    transition:0.3s;
}
nav a:hover{
    background:#272727;
    border-left:4px solid #e06d09da;
}

/* Header */
header{
    position:fixed;
    left:240px;
    top:0;
    width:calc(100% - 240px);
    padding:20px;
    background:#004383;
    font-size:1.8em;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0, 0, 0, 1);
}

/* Main */
main{
    margin-left:240px;
    margin-top:80px;
    padding:30px;
    flex:1;
}

/* Card */
.card{
    background:#004383;
    padding:25px;
    border-radius:12px;
    margin-bottom:20px;
}
.card h2{
    margin-bottom:15px;
    color:#fff;
}

/* Form */
form{display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
form input, form select, form button{padding:8px;border-radius:8px;border:none;outline:none;}
form input, form select{flex:1;background:#333;color:#fff;}
form button{background:#1a73e8;color:#fff;cursor:pointer;transition:0.3s;}
form button:hover{background:#00bfff;}

/* Tabla */
table{width:100%;border-collapse:collapse;margin-top:20px;background:#222;border-radius:12px;overflow:hidden;}
th,td{padding:12px;text-align:left;}
th{background:#fff;color:#000;}
tr:nth-child(even){background:#333;}
td a{text-decoration:none;padding:6px 12px;border-radius:6px;margin-right:5px;font-size:0.9em;color:#fff;}
td a.edit{background:#27ae60;}
td a.edit:hover{background:#1e8449;}
td a.delete{background:#e74c3c;}
td a.delete:hover{background:#c0392b;}
</style>

</head>
<body>
    <nav>
        <a href="panel_admin.php">Volver al panel Principal</a>
        <a href="login.php">Cerrar Sesión</a>
    </nav>

    <header>Dashboard de Gestión de Notas</header>

    <main>
        <div class="card">
            <h2>➕ <?= isset($editar) ? "Editar Nota" : "Agregar Nota" ?></h2>
            <form method="post">
                <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">

                <select name="id_inscripcion" required>
                    <option value="">--Seleccionar Inscripción--</option>
                    <?php
                    $inscripciones = $conexion->query("SELECT i.id, e.nombre AS estudiante, m.nombre AS materia
                                                       FROM inscripciones i
                                                       LEFT JOIN estudiantes e ON i.id_estudiante=e.id
                                                       LEFT JOIN cursos c ON i.id_curso=c.id
                                                       LEFT JOIN materias m ON c.id_materia=m.id");
                    while($fila = $inscripciones->fetch_assoc()):
                    ?>
                        <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_inscripcion']==$fila['id']?'selected':''?>>
                            <?= $fila['estudiante'].' - '.$fila['materia'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="number" step="0.01" name="zona" value="<?= isset($editar)?$editar['zona']:0 ?>" placeholder="Zona">
                <input type="number" step="0.01" name="fase_1" value="<?= isset($editar)?$editar['fase_1']:0 ?>" placeholder="Fase 1">
                <input type="number" step="0.01" name="fase_2" value="<?= isset($editar)?$editar['fase_2']:0 ?>" placeholder="Fase 2">
                <input type="number" step="0.01" name="fase_final" value="<?= isset($editar)?$editar['fase_final']:0 ?>" placeholder="Fase Final">
                <input type="text" name="observaciones" value="<?= isset($editar)?$editar['observaciones']:'' ?>" placeholder="Observaciones">

                <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>">
                    <?= isset($editar)?'Actualizar':'Guardar' ?>
                </button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Lista de Notas</h2>
            <table>
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
                        <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
                        <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar nota?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </main>
</body>
</html>
