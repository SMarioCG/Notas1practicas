
<?php
session_start();
include("conexion.php");

// Inicializar mensaje
$error = "";

if(isset($_POST['login'])){
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // Validar campos
    if($correo && $password && $rol){
        $hash_pass = hash('sha256', $password); // SHA2 256

        if($rol === 'Administrador'){
            $query = $conexion->query("SELECT * FROM administradores WHERE correo='$correo' AND password='$hash_pass'");
            $user = $query->fetch_assoc();
            if($user){
                $_SESSION['id'] = $user['id'];
                $_SESSION['rol'] = 'Administrador';
                $_SESSION['nombre'] = $user['nombre'];
                header("Location: administradores.php");
                exit;
            }
        }
        elseif($rol === 'Catedrático'){
            $query = $conexion->query("SELECT * FROM catedraticos WHERE correo='$correo' AND password='$hash_pass'");
            $user = $query->fetch_assoc();
            if($user){
                $_SESSION['id'] = $user['id'];
                $_SESSION['rol'] = 'Catedrático';
                $_SESSION['nombre'] = $user['nombre'];
                header("Location: catedraticos.php");
                exit;
            }
        }
        elseif($rol === 'Estudiante'){
            $query = $conexion->query("SELECT * FROM estudiantes WHERE correo='$correo' AND password='$hash_pass'");
            $user = $query->fetch_assoc();
            if($user){
                $_SESSION['id'] = $user['id'];
                $_SESSION['rol'] = 'Estudiante';
                $_SESSION['nombre'] = $user['nombre'];
                header("Location: estudiantes.php");
                exit;
            }
        }

        $error = "Correo, contraseña o rol incorrecto.";
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Académico</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .login-container h2 {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            color: #0d47a1;
            margin-bottom: 20px;
        }
        .login-container input, .login-container select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #0d47a1;
            border-radius: 5px;
            font-size: 14px;
        }
        .login-container input[type="submit"] {
            background-color: #0d47a1;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        .login-container input[type="submit"]:hover {
            background-color: #1565c0;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        Correo: <input type="email" name="correo" required>
        Contraseña: <input type="password" name="password" required>
        Rol:
        <select name="rol" required>
            <option value="">--Seleccionar--</option>
            <option value="Administrador">Administrador</option>
            <option value="Catedrático">Catedrático</option>
            <option value="Estudiante">Estudiante</option>
        </select>
        <input type="submit" name="login" value="Iniciar Sesión">
    </form>
</div>

</body>
</html>
