<?php
include("conexion.php");

// ===================== CREAR =====================
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

// ===================== ACTUALIZAR =====================
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

// ===================== ELIMINAR =====================
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM catedraticos WHERE id=$id";
    $conexion->query($sql);
    header("Location: catedraticos.php");
    exit;
}

// ===================== EDITAR =====================
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM catedraticos WHERE id=$id")->fetch_assoc();
}

// ===================== CONSULTAR =====================
$resultado = $conexion->query("SELECT * FROM catedraticos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Catedráticos</title>
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
form input, form button{
    padding:12px;
    border-radius:8px;
    border:none;
    outline:none;
    font-size: 1em;
}
form input{
    flex:1;
    background:#f8f9fa;
    color:#333;
    border: 2px solid #1e3c72;
}
form input:focus{
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
    form input {
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

<header>Dashboard de Catedráticos</header>

<main>
    <div class="card">
        <h2>➕ <?= isset($editar)? "Editar Catedrático" : "Agregar Catedrático" ?></h2>
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
        <h2>📋 Lista de Catedráticos</h2>
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
                    <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar catedrático?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>