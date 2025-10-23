<?php
include("conexion.php");

// ===================== CREAR =====================
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

// ===================== ACTUALIZAR =====================
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

// ===================== ELIMINAR =====================
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM calendario_examenes WHERE id=$id");
    header("Location: calendario_examenes.php");
    exit;
}

// ===================== EDITAR =====================
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM calendario_examenes WHERE id=$id")->fetch_assoc();
}

// ===================== CONSULTAR =====================
$resultado = $conexion->query("SELECT ce.*, a.nombre AS admin, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico
                               FROM calendario_examenes ce
                               LEFT JOIN administradores a ON ce.id_administrador=a.id
                               LEFT JOIN catedraticos ca ON ce.id_catedratico=ca.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Calendario de Exámenes</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:#fff;
    display:flex;
}

/* Sidebar */
nav{
    width:240px;
    background: rgba(20,20,30,0.95);
    height:100vh;
    padding-top:60px;
    position:fixed;
    left:0;
    box-shadow:2px 0 15px rgba(0,0,0,0.5);
}
nav a{
    display:block;
    padding:15px 25px;
    color:#fff;
    text-decoration:none;
    font-weight:500;
    transition: all 0.3s;
    border-left:4px solid transparent;
}
nav a:hover{
    background:linear-gradient(90deg,#1a2a6c,#02cdfa6f);
    transform:translateX(5px);
    border-left:4px solid #fff;
}

/* Header */
header{
    position:fixed;
    left:240px;
    top:0;
    width:calc(100% - 240px);
    padding:20px;
    background: linear-gradient(90deg,#1a2a6c,#0086ecff);
    font-size:1.8em;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
    z-index:10;
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
    background: rgba(255,255,255,0.05);
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.4);
    margin-bottom:30px;
}
.card h2{
    margin-bottom:15px;
    font-size:1.4em;
    color:#00eaff;
}

/* Form */
form{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
}
form input, form select, form button{
    padding:8px;
    border-radius:8px;
    border:none;
    outline:none;
}
form input, form select{
    flex:1;
    background: rgba(255,255,255,0.1);
    color:#fff;
}
form button{
    background:#0086ec;
    color:#fff;
    cursor:pointer;
    transition: background 0.3s;
}
form button:hover{background:#00bfff;}

/* Tabla */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
    background: rgba(255,255,255,0.05);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.4);
}
th,td{padding:12px;text-align:left;}
th{background-color: rgba(0,234,255,0.8);color:#000;}
tr:nth-child(even){background: rgba(255,255,255,0.05);}
td a{
    text-decoration:none;
    padding:6px 12px;
    border-radius:6px;
    margin-right:5px;
    font-size:0.9em;
    color:#fff;
}
td a.edit{background:#27ae60;}
td a.edit:hover{background:#1e8449;}
td a.delete{background:#e74c3c;}
td a.delete:hover{background:#c0392b;}
</style>
</head>
<body>
<nav>
    <a href="panel_admin.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar sesión</a>
</nav>

<header>Dashboard Calendario de Exámenes</header>

<main>
    <div class="card">
        <h2>➕ <?= isset($editar)? "Editar Registro" : "Agregar Registro" ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">
            
            <select name="tipo_registro" required>
                <option value="">--Tipo de Registro--</option>
                <?php 
                $tipos = ['Examen','Clase Presencial','Clase Virtual','Evento'];
                foreach($tipos as $tipo):
                    $sel = isset($editar) && $editar['tipo_registro']==$tipo?'selected':'';
                    echo "<option value='$tipo' $sel>$tipo</option>";
                endforeach;
                ?>
            </select>

            <select name="tipo_examen">
                <option value="">--Tipo de Examen--</option>
                <?php 
                $exams = ['Parcial 1','Parcial 2','Final'];
                foreach($exams as $exam):
                    $sel = isset($editar) && $editar['tipo_examen']==$exam?'selected':'';
                    echo "<option value='$exam' $sel>$exam</option>";
                endforeach;
                ?>
            </select>

            <input type="date" name="fecha" value="<?= isset($editar)?$editar['fecha']:date('Y-m-d') ?>" required>
            <input type="time" name="hora" value="<?= isset($editar)?$editar['hora']:date('H:i') ?>" required>

            <select name="id_administrador">
                <option value="">--Administrador--</option>
                <?php
                $admins = $conexion->query("SELECT * FROM administradores");
                while($fila = $admins->fetch_assoc()):
                    $sel = isset($editar) && $editar['id_administrador']==$fila['id']?'selected':'';
                    echo "<option value='{$fila['id']}' $sel>{$fila['nombre']} {$fila['apellido']}</option>";
                endwhile;
                ?>
            </select>

            <select name="id_catedratico">
                <option value="">--Catedrático--</option>
                <?php
                $cateds = $conexion->query("SELECT * FROM catedraticos");
                while($fila = $cateds->fetch_assoc()):
                    $sel = isset($editar) && $editar['id_catedratico']==$fila['id']?'selected':'';
                    echo "<option value='{$fila['id']}' $sel>{$fila['nombre']} {$fila['apellido']}</option>";
                endwhile;
                ?>
            </select>

            <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>"><?= isset($editar)?'Actualizar':'Guardar' ?></button>
        </form>
    </div>

    <div class="card">
        <h2>📋 Lista de Registros</h2>
        <table>
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
                    <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
                    <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar registro?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>


