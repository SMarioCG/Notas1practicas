<?php
session_start();
require 'vendor/autoload.php';
//use PHPGangsta_GoogleAuthenticator;

$message = '';

if (!isset($_SESSION['2fa_user_id'], $_SESSION['2fa_secret'], $_SESSION['rol_temp'], $_SESSION['nombre_temp'])) {
    header("Location: login.php");
    exit;
}

$rol = $_SESSION['rol_temp'];
$nombre = $_SESSION['nombre_temp'];
$secret = $_SESSION['2fa_secret'];
$show_qr = $_SESSION['show_qr'] ?? false; // Evita el warning


$ga = new PHPGangsta_GoogleAuthenticator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    if ($ga->verifyCode($secret, $codigo, 2)) {
        // Autenticación correcta
        $_SESSION['rol'] = $rol;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['2fa_secret'] = $secret;

        // Limpiar variables temporales
        unset($_SESSION['rol_temp'], $_SESSION['nombre_temp'], $_SESSION['2fa_user_id'], $_SESSION['show_qr']);

        // Redirigir al dashboard
        if ($rol === 'administrador') {
            header("Location: pruebaprac.php");
        } elseif ($rol === 'catedratico') {
            header("Location: panel_catedratico.php");
        } else {
            header("Location: panel_catedratico.php");
        }
        exit;
    } else {
        $message = "Código incorrecto, inténtalo nuevamente.";
    }
}

$qrCodeUrl = $show_qr ? $ga->getQRCodeGoogleUrl("Sistema Académico - $rol", $secret) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>2FA</title>
<style>
/*  login */
body { font-family:'Roboto',sans-serif; background:linear-gradient(135deg,#1c1c2b,#2a2a3d,#3a3a50); display:flex; justify-content:center; align-items:center; height:100vh; color:#fff; }
.card { background:rgba(255,255,255,0.05); padding:40px; border-radius:25px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.6);}
.card input { width:100%; padding:15px; margin-bottom:20px; border-radius:12px; border:none; background:rgba(255,255,255,0.1); color:#fff;}
.card button { padding:15px 20px; border:none; border-radius:12px; background:linear-gradient(90deg,#1a2a6c,#b21f1f); color:#fff; cursor:pointer; width:100%; font-weight:bold;}
.card button:hover { transform: translateY(-3px); box-shadow:0 10px 20px rgba(0,0,0,0.5);}
.error-message { color:#ff4d4d; margin-bottom:15px;}
.qr img { margin:20px 0; }
</style>
</head>
<body>
<div class="card">
    <h2>Hola, <?php echo htmlspecialchars($nombre); ?></h2>
    <p>Ingresa el código de 6 dígitos de Google Authenticator</p>
    <?php if($message): ?><p class="error-message"><?php echo $message;?></p><?php endif; ?>
    <form method="POST">
        <input type="text" name="codigo" maxlength="6" placeholder="Código 6 dígitos" required>
        <button type="submit">Verificar</button>
    </form>
    <?php if($show_qr): ?>
    <div class="qr">
        <p>Escanea este QR con Google Authenticator:</p>
        <img src="<?php echo $qrCodeUrl;?>" alt="QR 2FA">
    </div>
    <?php endif; ?>
</div>
</body>
</html>


