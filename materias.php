<?php
include("conexion.php");

// ===================== CREAR =====================
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

// ===================== ACTUALIZAR =====================
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

// ===================== ELIMINAR =====================
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM materias WHERE id=$id");
    header("Location: materias.php");
    exit;
}

// ===================== EDITAR =====================
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM materias WHERE id=$id")->fetch_assoc();
}

// ===================== FILTRO =====================
$id_carrera = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;

// ===================== CONSULTAR CARRERAS =====================
$carreras = $conexion->query("SELECT * FROM carreras ORDER BY nombre ASC");

// ===================== CONSULTAR MATERIAS =====================
if ($id_carrera > 0) {
    $materias = $conexion->query("SELECT m.*, c.nombre AS carrera 
                                  FROM materias m
                                  LEFT JOIN carreras c ON m.id_carrera=c.id
                                  WHERE m.id_carrera=$id_carrera");
} else {
    $materias = $conexion->query("SELECT m.*, c.nombre AS carrera 
                                  FROM materias m
                                  LEFT JOIN carreras c ON m.id_carrera=c.id");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard de Materias</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:#fff;color:#fff;display:flex;}
nav{width:240px;background:#004383;height:100vh;padding-top:60px;position:fixed;}
nav a{display:block;padding:15px 25px;color:#fff;text-decoration:none;border-left:4px solid transparent;transition:0.3s;}
nav a:hover{background:#272727;border-left:4px solid #e06d09da;}
header{position:fixed;left:240px;top:0;width:calc(100% - 240px);padding:20px;background:#004383;font-size:1.8em;font-weight:bold;box-shadow:0 4px 10px rgba(0,0,0,1);}
main{margin-left:240px;margin-top:80px;padding:30px;flex:1;}
.card{background:#004383;padding:25px;border-radius:12px;margin-bottom:20px;}
.card h2{margin-bottom:15px;color:#fff;}
form{display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
form input,form select,form button{padding:8px;border-radius:8px;border:none;outline:none;}
form input,form select{flex:1;background:#333;color:#fff;}
form button{background:#1a73e8;color:#fff;cursor:pointer;transition:0.3s;}
form button:hover{background:#00bfff;}
table{width:100%;border-collapse:collapse;margin-top:20px;background:#222;border-radius:12px;overflow:hidden;}
th,td{padding:12px;text-align:left;}
th{background:#fff;color:#000;}
tr:nth-child(even){background:#333;}
td a{text-decoration:none;padding:6px 12px;border-radius:6px;margin-right:5px;font-size:0.9em;color:#fff;}
td a.edit{background:#27ae60;}
td a.edit:hover{background:#1e8449;}
td a.delete{background:#e74c3c;}
td a.delete:hover{background:#c0392b;}
select#filtroCarrera{padding:8px;background:#333;color:#fff;border-radius:8px;}
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
                <?php while($fila = $carreras->fetch_assoc()): ?>
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
        <h2>📚 Ver Materias por Carrera</h2>
        <form method="get">
            <select name="id_carrera" id="filtroCarrera" onchange="this.form.submit()">
                <option value="">--Todas las carreras--</option>
                <?php
                $carreras2 = $conexion->query("SELECT * FROM carreras");
                while($fila = $carreras2->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>" <?= $id_carrera == $fila['id'] ? 'selected' : '' ?>>
                        <?= $fila['nombre'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <div class="card">
        <h2>📋 Lista de Materias</h2>
        <table id="tablaMaterias">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Créditos</th>
                    <th>Carrera</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if($materias->num_rows > 0): ?>
                    <?php while($fila = $materias->fetch_assoc()): ?>
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
                <?php else: ?>
                    <tr><td colspan="6">No hay materias registradas para esta carrera.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
