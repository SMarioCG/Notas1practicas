<?php
include("conexion.php");

// ===================== CREAR =====================
if (isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = hash('sha256', $_POST['password']);
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
<title>Dashboard Estudiantes</title>
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

<header>Gestionar Estudiantes</header>

<main>
    <div class="card">
        <h2>➕ <?= isset($editar)? "Editar Estudiante" : "Agregar Estudiante" ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">

            <input type="text" name="nombre" placeholder="Nombre" value="<?= isset($editar)?$editar['nombre']:'' ?>" required>
            <input type="text" name="apellido" placeholder="Apellido" value="<?= isset($editar)?$editar['apellido']:'' ?>">

            <input type="email" name="correo" placeholder="Correo" value="<?= isset($editar)?$editar['correo']:'' ?>" required>

            <?php if(!isset($editar)): ?>
                <input type="password" name="password" placeholder="Contraseña" required>
            <?php endif; ?>

            <select name="id_carrera" required>
                <option value="">--Seleccionar Carrera--</option>
                <?php
                $carreras = $conexion->query("SELECT * FROM carreras");
                while($fila = $carreras->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>" <?=isset($editar) && $editar['id_carrera']==$fila['id']?'selected':''?>><?= $fila['nombre'] ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>"><?= isset($editar)?'Actualizar':'Guardar' ?></button>
        </form>
    </div>

    <div class="card">
        <h2>📋 Lista de Estudiantes</h2>
        <table>
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
                    <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
                    <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar estudiante?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>
