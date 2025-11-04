<?php
// config/database.php
session_start();

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "admin123";
$db   = "notasregional2";

// Conexión a la base de datos
$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

// Simular datos de usuario (reemplaza con tu sistema de autenticación real)
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = [
        'nombre' => 'Admin Principal',
        'rol' => 'Administrador',
        'email' => 'admin@colegio.edu',
        'avatar' => '👨‍💼'
    ];
}

// Manejar cierre de sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Manejar reinicio de 2FA
if (isset($_POST['reset_2fa'])) {
    $tipo_usuario = $_POST['tipo_usuario'];
    $usuario_id = $_POST['usuario_id'];
    
    if (resetTwoFactorAuth($conexion, $tipo_usuario, $usuario_id)) {
        $_SESSION['mensaje'] = "✅ QR de Google Authenticator reiniciado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "❌ Error al reiniciar el QR de Google Authenticator";
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Función para reiniciar 2FA
function resetTwoFactorAuth($conexion, $tipo_usuario, $usuario_id) {
    $tabla = ($tipo_usuario == 'administrador') ? 'administradores' : 'catedraticos';
    $sql = "UPDATE $tabla SET secret_2fa = NULL WHERE id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    }
    
    $stmt->close();
    return false;
}

// Obtener información del sistema para el dashboard
$total_estudiantes = 0;
$total_catedraticos = 0;
$total_materias = 0;
$total_inscripciones = 0;

// Consultas para estadísticas
try {
    $sql_estudiantes = "SELECT COUNT(*) as total FROM estudiantes";
    $sql_catedraticos = "SELECT COUNT(*) as total FROM catedraticos";
    $sql_materias = "SELECT COUNT(*) as total FROM materias";
    $sql_inscripciones = "SELECT COUNT(*) as total FROM inscripciones";
    
    if ($result = $conexion->query($sql_estudiantes)) {
        $row = $result->fetch_assoc();
        $total_estudiantes = $row['total'];
        $result->free();
    }
    
    if ($result = $conexion->query($sql_catedraticos)) {
        $row = $result->fetch_assoc();
        $total_catedraticos = $row['total'];
        $result->free();
    }
    
    if ($result = $conexion->query($sql_materias)) {
        $row = $result->fetch_assoc();
        $total_materias = $row['total'];
        $result->free();
    }
    
    if ($result = $conexion->query($sql_inscripciones)) {
        $row = $result->fetch_assoc();
        $total_inscripciones = $row['total'];
        $result->free();
    }
    
} catch (Exception $e) {
    error_log("Error en consultas: " . $e->getMessage());
}

// Obtener administradores y catedráticos para el modal
$administradores = [];
$catedraticos = [];

$sql_admins = "SELECT id, nombre, apellido, correo FROM administradores";
if ($result = $conexion->query($sql_admins)) {
    while ($row = $result->fetch_assoc()) {
        $administradores[] = $row;
    }
    $result->free();
}

$sql_catedraticos = "SELECT id, nombre, apellido, correo FROM catedraticos";
if ($result = $conexion->query($sql_catedraticos)) {
    while ($row = $result->fetch_assoc()) {
        $catedraticos[] = $row;
    }
    $result->free();
}

// Obtener fecha y hora actual
$fecha_actual = date('d/m/Y');
$hora_actual = date('H:i');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Principal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #004383;     /* Azul institucional */
    --secondary: #FFB800;   /* Mostaza */
    --dark: #000000;        /* Negro */
    --light: rgba(255, 255, 255, 0.9); /* Blanco translúcido */
    --lighter: rgba(255, 255, 255, 0.05);
}

/* ==================== ESTILOS GLOBALES ==================== */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--primary), #001f3f, var(--dark));
    color: var(--light);
    min-height: 100vh;
    line-height: 1.6;
    overflow-x: hidden;
}

/* ==================== HEADER ==================== */
.header-main {
    background: var(--light);
    padding: 20px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.4);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo img {
    max-width: 250px;
    height: auto;
    border-radius: 8px;
}

.header-text h1 {
    font-size: 2em;
    font-weight: 700;
    color: #000;       /* letras negras */
    text-align: center; /* centra el texto */
    margin: 0;          /* elimina margen por defecto */
}

.header-text p {
    color: var(--secondary);
    font-size: 0.95em;
    text-align: center;
    margin: 0;
}


.user-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-info {
    text-align: right;
}

.user-name {
    font-weight: 600;
    font-size: 1.1em;
    color: #000000ff;
}

.user-role {
    color: var(--secondary);
    font-size: 0.9em;
}

.logout-btn {
    background: var(--secondary);
    border: none;
    color: #000;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background: #FFD84C;
    transform: translateY(-2px);
}

/* ==================== BANNER ==================== */
.welcome-banner {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    margin-bottom: 30px;
}

.welcome-banner h2 {
    color: var(--secondary);
    font-size: 2em;
}

.welcome-banner p {
    color: #fff;
    opacity: 0.9;
}

/* ==================== TARJETAS DE ESTADÍSTICAS ==================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-8px);
    border-color: var(--secondary);
    box-shadow: 0 0 15px rgba(255,184,0,0.3);
}

.stat-number {
    font-size: 3em;
    color: var(--secondary);
    font-weight: bold;
}

.stat-label {
    color: #fff;
    opacity: 0.9;
}

/* ==================== MÓDULOS ==================== */
.navigation-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 1.8em;
    text-align: center;
    color: var(--secondary);
    margin-bottom: 25px;
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.nav-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    padding: 25px;
    border-radius: 15px;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.nav-card:hover {
    background: var(--secondary);
    color: #000;
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(255,184,0,0.4);
}

.nav-label {
    font-size: 1.2em;
    font-weight: bold;
}

.nav-desc {
    font-size: 0.9em;
    opacity: 0.8;
}

/* ==================== FOOTER ==================== */
.footer-main {
    background: #000;
    color: #fff;
    padding: 20px;
    text-align: center;
    border-top: 2px solid var(--secondary);
}

.footer-link {
    color: var(--secondary);
    text-decoration: none;
    margin: 0 10px;
    transition: 0.3s;
}

.footer-link:hover {
    opacity: 0.8;
}

/* ==================== BOTONES Y FORMULARIOS ==================== */
.submit-btn {
    background: var(--secondary);
    color: #000;
    border: none;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
}

.submit-btn:hover {
    background: #FFD84C;
}

.form-select, .form-input {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    padding: 12px;
}

.form-select:focus, .form-input:focus {
    outline: none;
    border-color: var(--secondary);
}

/* ==================== ALERTAS ==================== */
.alert-success {
    background: rgba(255, 184, 0, 0.15);
    color: var(--secondary);
    border: 1px solid var(--secondary);
}

.alert-error {
    background: rgba(255,0,0,0.15);
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .user-section {
        flex-direction: column;
    }

    .nav-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<!-- Efecto de partículas -->
<div class="particles" id="particles"></div>

<header class="header-main">
    <div class="header-content">
        <div class="logo-section">
            <div class="logo">
                <img src="https://moria.aurens.com/organizations/362029ae-4545-4e01-a1d9-5a79a6e6f493/logos/26b681-regional.png" alt="Logo Regional">
            </div>
            <div class="header-text">
                <h1>Sistema de Notas</h1>
                
            </div>
        </div>
        
        <div class="user-section">
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['usuario']['nombre']; ?></div>
                <div class="user-role"><?php echo $_SESSION['usuario']['rol']; ?></div>
            </div>
            <button class="logout-btn" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </button>
        </div>
    </div>
</header>

<main class="dashboard">
    <!-- Mensajes -->
    <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?>">
        <?php 
        echo $_SESSION['mensaje']; 
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        ?>
    </div>
    <?php endif; ?>

    <section class="welcome-banner">
        <h2>Bienvenido, <?php echo explode(' ', $_SESSION['usuario']['nombre'])[0]; ?>! </h2>
        <p>Panel de control del sistema académico - Regional Sede Mixco 2</p>
    </section>

    <section class="navigation-section">
        <h2 class="section-title">Módulos del Sistema de Notas</h2>
        <div class="nav-grid">
            <?php
            $modules = [
                'administradores.php' => ['', 'Administradores', 'Gestión de usuarios administrativos'],
                'calendario_examenes.php' => ['', 'Calendario', 'Programación de evaluaciones'],
                'carreras.php' => ['', 'Carreras', 'Programas académicos'],
                'catedraticos.php' => ['', 'Catedráticos', 'Personal docente'],
                'estudiantes.php' => ['', 'Estudiantes', 'Registro académico'],
               
                'materias.php' => ['', 'Materias', 'Plan de estudios'],
                'cursos.php' => ['', 'Cursos', 'Grupos y secciones'],
                'notas.php' => ['', 'Notas', 'Sistema de calificaciones'],
                'perfiles.php' => ['', 'Perfiles', 'Gestión de usuarios']
            ];
            
            $delay = 0.1;
            foreach ($modules as $link => $info) {
                echo "
                <a href='$link' class='nav-card' style='animation-delay: {$delay}s'>
                    <div class='nav-icon'>{$info[0]}</div>
                    <div class='nav-content'>
                        <div class='nav-label'>{$info[1]}</div>
                        <div class='nav-desc'>{$info[2]}</div>
                    </div>
                </a>";
                $delay += 0.05;
            }
            ?>
        </div>
    </section>
</main>

<script>
// Confirmar cierre de sesión
function confirmLogout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = '?logout=true';
    }
}
</script>
</body>
</html>
