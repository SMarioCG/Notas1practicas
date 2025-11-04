<?php
include("conexion.php");

// Crear
if(isset($_POST['guardar'])){
    $id_materia = $_POST['id_materia'];
    $id_catedratico = $_POST['id_catedratico'];
    $semestre = $_POST['semestre'];

    $sql = "INSERT INTO cursos (id_materia, id_catedratico, semestre) 
            VALUES ('$id_materia','$id_catedratico','$semestre')";
    $conexion->query($sql);
    header("Location: cursos.php");
    exit;
}

// Actualizar
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $id_materia = $_POST['id_materia'];
    $id_catedratico = $_POST['id_catedratico'];
    $semestre = $_POST['semestre'];

    $sql = "UPDATE cursos 
            SET id_materia='$id_materia', id_catedratico='$id_catedratico', semestre='$semestre' 
            WHERE id=$id";
    $conexion->query($sql);
    header("Location: cursos.php");
    exit;
}

// Eliminar
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM cursos WHERE id=$id");
    header("Location: cursos.php");
    exit;
}

// Editar
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $editar = $conexion->query("SELECT * FROM cursos WHERE id=$id")->fetch_assoc();
}

// Consultar cursos
$resultado = $conexion->query("SELECT cu.*, m.nombre AS materia, CONCAT(ca.nombre,' ',ca.apellido) AS catedratico
                               FROM cursos cu
                               LEFT JOIN materias m ON cu.id_materia=m.id
                               LEFT JOIN catedraticos ca ON cu.id_catedratico=ca.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Cursos</title>
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
    <a href="panel_admin.php">Volver a Panel Principal</a>
    <a href="login.php">Cerrar Sesión</a>
  </nav>

  <header>Gestionar Cursos</header>

  <main>
    <div class="card">
      <h2>➕ <?= isset($editar) ? "Editar Curso" : "Asignar Curso" ?></h2>
      <form method="post">
        <input type="hidden" name="id" value="<?= isset($editar)?$editar['id']:'' ?>">

        <select name="id_catedratico" required>
          <option value="">Seleccionar catedrático</option>
          <?php
          $catedraticos = $conexion->query("SELECT * FROM catedraticos");
          while($fila = $catedraticos->fetch_assoc()):
          ?>
            <option value="<?= $fila['id'] ?>" <?= isset($editar) && $editar['id_catedratico']==$fila['id']?'selected':'' ?>>
              <?= $fila['nombre'].' '.$fila['apellido'] ?>
            </option>
          <?php endwhile; ?>
        </select>

        <select name="id_materia" required>
          <option value="">Seleccionar materia</option>
          <?php
          $materias = $conexion->query("SELECT * FROM materias");
          while($fila = $materias->fetch_assoc()):
          ?>
            <option value="<?= $fila['id'] ?>" <?= isset($editar) && $editar['id_materia']==$fila['id']?'selected':'' ?>>
              <?= $fila['nombre'] ?>
            </option>
          <?php endwhile; ?>
        </select>

        <input type="text" name="semestre" placeholder="Semestre (ej: I, II)" value="<?= isset($editar)?$editar['semestre']:'' ?>" required>

        <button type="submit" name="<?= isset($editar)?'actualizar':'guardar' ?>">
          <?= isset($editar)?'Actualizar':'Asignar' ?>
        </button>
      </form>
    </div>

    <div class="card">
      <h2>📋 Lista de Cursos</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Materia</th>
          <th>Catedrático</th>
          <th>Semestre</th>
          <th>Acciones</th>
        </tr>
        <?php while($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= $fila['id'] ?></td>
          <td><?= $fila['materia'] ?></td>
          <td><?= $fila['catedratico'] ?></td>
          <td><?= $fila['semestre'] ?></td>
          <td>
            <a href="?editar=<?= $fila['id'] ?>" class="edit">Editar</a>
            <a href="?eliminar=<?= $fila['id'] ?>" class="delete" onclick="return confirm('¿Eliminar curso?')">Eliminar</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </main>
</body>
</html>

