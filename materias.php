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
<title>Dashboard de Materias</title>
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:#fff;
    display:flex;
}
/* Sidebar */
nav {
    width:240px;
    background: rgba(20,20,30,0.95);
    height:100vh;
    padding-top:60px;
    position:fixed;
    left:0;
    box-shadow:2px 0 15px rgba(0,0,0,0.5);
}
nav a {
    display:block;
    padding:15px 25px;
    color:#fff;
    text-decoration:none;
    font-weight:500;
    transition: all 0.3s;
    border-left:4px solid transparent;
}
nav a:hover {
    background:linear-gradient(90deg,#1a2a6c,#02cdfa6f);
    transform:translateX(5px);
    border-left:4px solid #fff;
}

/* Header */
header {
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
main {
    margin-left:240px;
    margin-top:80px;
    padding:30px;
    flex:1;
}

/* Card */
.card {
    background: rgba(255,255,255,0.05);
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.4);
    margin-bottom:30px;
}
.card h2 {
    margin-bottom:15px;
    font-size:1.4em;
    color:#00eaff;
}

/* Formulario */
form {
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
}
form input, form select, form button {
    padding:8px;
    border-radius:8px;
    border:none;
    outline:none;
}
form input, form select {
    flex:1;
    background: rgba(255,255,255,0.1);
    color:#fff;
}
form button {
    background:#0086ec;
    color:#fff;
    cursor:pointer;
    transition: background 0.3s;
}
form button:hover {background:#00bfff;}

/* Tabla */
table {
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
    background: rgba(255,255,255,0.05);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.4);
}
th,td {padding:12px;text-align:left;}
th {background-color: rgba(0,234,255,0.8);color:#000;}
tr:nth-child(even){background: rgba(255,255,255,0.05);}
td a {
    text-decoration:none;
    padding:6px 12px;
    border-radius:6px;
    margin-right:5px;
    font-size:0.9em;
    color:#fff;
}
td a.edit {background:#27ae60;}
td a.edit:hover {background:#1e8449;}
td a.delete {background:#e74c3c;}
td a.delete:hover {background:#c0392b;}
</style>
</head>
<body>
<nav>
    <a href="panel_admin.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
</nav>

<header>Dashboard de Gestión de Materias</header>

<main>
    <div class="card">
        <h2>➕ <?= isset($editar)? "Editar Materia" : "Agregar Materia" ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">
            <input type="text" name="nombre" placeholder="Nombre" value="<?= isset($editar)?$editar['nombre']:'' ?>" required>
            <input type="text" name="codigo" placeholder="Código" value="<?= isset($editar)?$editar['codigo']:'' ?>" required>
            <input type="number" name="creditos" placeholder="Créditos" value="<?= isset($editar)?$editar['creditos']:'' ?>" required>
            <select name="id_carrera" required>
                <option value="">--Seleccionar Carrera--</option>
                <?php
                $carreras = $conexion->query("SELECT * FROM carreras");
                while($fila = $carreras->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>" <?= isset($editar) && $editar['id_carrera']==$fila['id']?'selected':'' ?>>
                        <?= $fila['nombre'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>">
                <?= isset($editar)?'Actualizar':'Guardar' ?>
            </button>
        </form>
    </div>

    <div class="card">
        <h2>📋 Lista de Materias</h2>
        <table>
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
                    <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
                    <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar materia?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>
