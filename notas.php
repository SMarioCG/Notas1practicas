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
        * {margin:0; padding:0; box-sizing:border-box;}
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #3a6fd9);
            color: #fff;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        nav {
            width: 240px;
            background: #1e3c72;
            height: 100vh;
            padding-top: 60px;
            position: fixed;
            left: 0;
            border-right: 3px solid #ffffff;
        }
        nav a {
            display:block;
            padding:15px 25px;
            color:#fff;
            text-decoration:none;
            font-weight:500;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            margin: 8px 10px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
        }
        nav a:hover {
            background: #ffffff;
            color: #1e3c72;
            transform: translateX(5px);
            border-left: 4px solid #1e3c72;
        }

        /* Header */
        header {
            position: fixed;
            left: 240px;
            top: 0;
            width: calc(100% - 240px);
            padding: 20px;
            background: #1e3c72;
            font-size: 1.8em;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 10;
            color: #ffffff;
            border-bottom: 3px solid #ffffff;
        }

        /* Main */
        main {
            margin-left: 240px;
            margin-top: 80px;
            padding: 30px;
            flex:1;
        }

        /* Tarjetas */
        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border: 2px solid #1e3c72;
        }
        .card h2 {
            margin-bottom: 15px;
            font-size: 1.4em;
            color: #1e3c72;
        }

        /* Formulario */
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        form input, form select, form button {
            padding: 12px;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 1em;
        }
        form input, form select {
            flex: 1;
            background: #f8f9fa;
            color: #333;
            border: 2px solid #1e3c72;
        }
        form input:focus, form select:focus {
            border-color: #3a6fd9;
            background: #ffffff;
        }
        form button {
            background: #1e3c72;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            border: 2px solid #1e3c72;
        }
        form button:hover {
            background: #3a6fd9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }

        /* Tabla */
        table {
            width:100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #ffffff;
            border-radius:12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 2px solid #1e3c72;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #1e3c72;
            color: #ffffff;
            font-weight: 600;
        }
        td {
            color: #333;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        td a {
            text-decoration:none;
            padding:8px 15px;
            border-radius:6px;
            margin-right:5px;
            font-size:0.9em;
            color:#fff;
            transition: all 0.3s ease;
            display: inline-block;
        }
        td a.edit {
            background: #1e3c72;
            border: 1px solid #1e3c72;
        }
        td a.edit:hover {
            background: #3a6fd9;
            transform: translateY(-2px);
        }
        td a.delete {
            background: #dc3545;
            border: 1px solid #dc3545;
        }
        td a.delete:hover {
            background: #c82333;
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
            table {
                font-size: 0.9em;
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
            th, td {
                padding: 10px;
            }
        }
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