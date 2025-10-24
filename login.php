<?php
session_start();
include("conexion.php"); // Asegúrate de que tu conexión MySQL esté correcta

$error = "";

if(isset($_POST['login'])){
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    if($correo && $password && $rol){
        $hash_pass = hash('sha256', $password);

        // Mapeo de rol → tabla y página de redirección
        $roles = [
            'Administrador' => ['tabla' => 'administradores', 'redirect' => 'panel_admin.php'],
            'Catedrático' => ['tabla' => 'catedraticos', 'redirect' => 'panel_catedratico.php'],
            'Estudiante' => ['tabla' => 'estudiantes', 'redirect' => 'panel_estudiante.php']
        ];

        if(isset($roles[$rol])){
            $tabla = $roles[$rol]['tabla'];
            $redirect = $roles[$rol]['redirect'];

            // Prepared statement para mayor seguridad
            $stmt = $conexion->prepare("SELECT * FROM $tabla WHERE correo=? AND password=?");
            $stmt->bind_param("ss", $correo, $hash_pass);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result && $result->num_rows > 0){
                $user = $result->fetch_assoc();
                $_SESSION['id'] = $user['id'];
                $_SESSION['rol'] = $rol;
                $_SESSION['nombre'] = $user['nombre'];
                header("Location: $redirect");
                exit;
            } else {
                $error = "❌ Correo, contraseña o rol incorrecto.";
            }
        } else {
            $error = "⚠️ Rol inválido.";
        }
    } else {
        $error = "⚠️ Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login | Sistema Académico</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}
.login-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    padding: 40px;
    width: 380px;
    text-align: center;
    animation: fadeIn 0.8s ease-in-out;
}
.login-card h2 {
    color: #0d47a1;
    margin-bottom: 25px;
    font-weight: 600;
}
.login-card input, .login-card select {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    transition: 0.3s;
    font-size: 14px;
}
.login-card input:focus, .login-card select:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 5px rgba(13,71,161,0.4);
    outline: none;
}
.login-card input[type="submit"] {
    background: #0d47a1;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.login-card input[type="submit"]:hover {
    background: #1565c0;
    transform: scale(1.03);
}
.error {
    background: #ffebee;
    color: #c62828;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 15px;
}
.footer {
    margin-top: 15px;
    font-size: 12px;
    color: #555;
}
a {
    color: #1565c0;
    text-decoration: none;
    font-size: 13px;
}
a:hover {
    text-decoration: underline;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="login-card">
    <h2>🎓 Sistema Académico</h2>
    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="correo" placeholder="📧 Correo electrónico" required>
        <input type="password" name="password" placeholder="🔒 Contraseña" required>
        <select name="rol" required>
            <option value="">-- Selecciona tu rol --</option>
            <option value="Administrador">Administrador</option>
            <option value="Catedrático">Catedrático</option>
            <option value="Estudiante">Estudiante</option>
        </select>
        <input type="submit" name="login" value="Iniciar Sesión">
    </form>
    <a href="recuperar_password.php">¿Olvidaste tu contraseña?</a>
    <div class="footer">© <?= date('Y') ?> Sistema Académico Regional</div>
</div>

</body>
</html>
