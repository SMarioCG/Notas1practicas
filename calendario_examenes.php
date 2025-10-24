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
    background: linear-gradient(135deg, #1e3c72, #2a5298, #3a6fd9);
    color:#fff;
    display:flex;
    min-height:100vh;
}

/* Sidebar */
nav{
    width:240px;
    background:#1e3c72;
    height:100vh;
    padding-top:60px;
    position:fixed;
    left:0;
    border-right: 3px solid #ffffff;
}
nav a{
    display:block;
    padding:15px 25px;
    color:#fff;
    text-decoration:none;
    font-weight:500;
    transition: all 0.3s;
    border-left:4px solid transparent;
    margin: 8px 10px;
    border-radius: 8px;
    background: rgba(255,255,255,0.1);
}
nav a:hover{
    background:#ffffff;
    color:#1e3c72;
    transform:translateX(5px);
    border-left:4px solid #1e3c72;
}

/* Header */
header{
    position:fixed;
    left:240px;
    top:0;
    width:calc(100% - 240px);
    padding:20px;
    background:#1e3c72;
    font-size:1.8em;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
    z-index:10;
    color: #ffffff;
    border-bottom: 3px solid #ffffff;
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
    background: #ffffff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    margin-bottom:30px;
    border: 2px solid #1e3c72;
}
.card h2{
    margin-bottom:15px;
    font-size:1.4em;
    color:#1e3c72;
}

/* Form */
form{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
}
form input, form select, form button{
    padding:12px;
    border-radius:8px;
    border:none;
    outline:none;
    font-size: 1em;
}
form input, form select{
    flex:1;
    background:#f8f9fa;
    color:#333;
    border: 2px solid #1e3c72;
}
form input:focus, form select:focus{
    border-color: #3a6fd9;
    background: #ffffff;
}
form button{
    background:#1e3c72;
    color:#fff;
    cursor:pointer;
    transition: all 0.3s;
    font-weight: 600;
    border: 2px solid #1e3c72;
}
form button:hover{
    background:#3a6fd9;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(30,60,114,0.3);
}

/* Tabla */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
    background: #ffffff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    border: 2px solid #1e3c72;
}
th,td{
    padding:15px;
    text-align:left;
    border-bottom: 1px solid #dee2e6;
}
th{
    background:#1e3c72;
    color:#ffffff;
    font-weight: 600;
}
td{
    color: #333;
}
tr:nth-child(even){
    background:#f8f9fa;
}
td a{
    text-decoration:none;
    padding:8px 15px;
    border-radius:6px;
    margin-right:5px;
    font-size:0.9em;
    color:#fff;
    transition: all 0.3s ease;
    display: inline-block;
}
td a.edit{
    background:#1e3c72;
    border: 1px solid #1e3c72;
}
td a.edit:hover{
    background:#3a6fd9;
    transform: translateY(-2px);
}
td a.delete{
    background:#dc3545;
    border: 1px solid #dc3545;
}
td a.delete:hover{
    background:#c82333;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    nav {
        width: 200px;
    }
    header {
        left: 200px;
        width: calc(100% - 200px);
        font-size: 1.5em;
        padding: 15px;
    }
    main {
        margin-left: 200px;
        padding: 20px;
    }
    form {
        flex-direction: column;
    }
    form input, form select {
        width: 100%;
    }
}

@media (max-width: 480px) {
    nav {
        width: 100%;
        height: auto;
        position: relative;
        padding-top: 20px;
    }
    header {
        position: relative;
        left: 0;
        width: 100%;
        margin-top: 0;
    }
    main {
        margin-left: 0;
        margin-top: 20px;
    }
    table {
        font-size: 0.9em;
    }
    th, td {
        padding: 10px;
    }
}
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
