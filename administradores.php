<?php
include("conexion.php");

// ===================== CREAR =====================
if (isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = hash('sha256', $_POST['password']);

    $sql = "INSERT INTO administradores (nombre, apellido, correo, password) 
            VALUES ('$nombre','$apellido','$correo','$password')";
    $conexion->query($sql);
    header("Location: administradores.php");
    exit;
}

// ===================== ACTUALIZAR =====================
if (isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $sql = "UPDATE administradores SET nombre='$nombre', apellido='$apellido', correo='$correo' WHERE id=$id";
    $conexion->query($sql);
    header("Location: administradores.php");
    exit;
}

// ===================== ELIMINAR =====================
if(isset ($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM administradores WHERE id=$id";
    $conexion->query($sql);
    header("Location: administradores.php");
    exit;
}

// ===================== EDITAR =====================
if(isset ($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM administradores WHERE id=$id")->fetch_assoc();
}

// ===================== CONSULTAR =====================
$resultado = $conexion->query("SELECT * FROM administradores");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Administradores</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:#121212;color:#fff;display:flex;}

/* Sidebar */
nav{
    width:240px;
    background:#1c1c1c;
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
    border-left:4px solid #00bfff;
}

/* Header */
header{
    position:fixed;
    left:240px;
    top:0;
    width:calc(100% - 240px);
    padding:20px;
    background:#1a73e8;
    font-size:1.8em;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
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
    background: #222;
    padding:25px;
    border-radius:12px;
    margin-bottom:20px;
}
.card h2{
    margin-bottom:15px;
    color:#00eaff;
}

/* Form */
form{display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
form input, form button{padding:8px;border-radius:8px;border:none;outline:none;}
form input{flex:1;background:#333;color:#fff;}
form button{background:#1a73e8;color:#fff;cursor:pointer;transition:0.3s;}
form button:hover{background:#00bfff;}

/* Tabla */
table{width:100%;border-collapse:collapse;margin-top:20px;background:#222;border-radius:12px;overflow:hidden;}
th,td{padding:12px;text-align:left;}
th{background:#00eaff;color:#000;}
tr:nth-child(even){background:#2a2a2a;}
td a{
    text-decoration:none;padding:6px 12px;border-radius:6px;margin-right:5px;font-size:0.9em;color:#fff;
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

<header>Dashboard Administradores</header>

<main>
    <div class="card">
        <h2>➕ <?= isset($editar)? "Editar Administrador" : "Agregar Administrador" ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">
            <input type="text" name="nombre" placeholder="Nombre" value="<?= isset($editar)?$editar['nombre']:'' ?>" required>
            <input type="text" name="apellido" placeholder="Apellido" value="<?= isset($editar)?$editar['apellido']:'' ?>" required>
            <input type="email" name="correo" placeholder="Correo" value="<?= isset($editar)?$editar['correo']:'' ?>" required>
            <?php if(!isset($editar)): ?>
                <input type="password" name="password" placeholder="Contraseña" required>
            <?php endif; ?>
            <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>"><?= isset($editar)?'Actualizar':'Guardar' ?></button>
        </form>
    </div>

    <div class="card">
        <h2>📋 Lista de Administradores</h2>
        <table>
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
                    <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
                    <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar administrador?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>
