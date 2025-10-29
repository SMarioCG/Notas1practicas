<?php
// config/database.php
session_start();

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "pequeñocesar2025";
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

// Consultas para estadísticas - VERSIÓN CORREGIDA
try {
    // Verificar si las tablas existen y obtener conteos
    $sql_estudiantes = "SELECT COUNT(*) as total FROM estudiantes";
    $sql_catedraticos = "SELECT COUNT(*) as total FROM catedraticos";
    $sql_materias = "SELECT COUNT(*) as total FROM materias";
    $sql_inscripciones = "SELECT COUNT(*) as total FROM inscripciones";
    
    // Consulta segura para estudiantes
    if ($result = $conexion->query($sql_estudiantes)) {
        $row = $result->fetch_assoc();
        $total_estudiantes = $row['total'];
        $result->free();
    }
    
    // Consulta segura para catedráticos
    if ($result = $conexion->query($sql_catedraticos)) {
        $row = $result->fetch_assoc();
        $total_catedraticos = $row['total'];
        $result->free();
    }
    
    // Consulta segura para materias
    if ($result = $conexion->query($sql_materias)) {
        $row = $result->fetch_assoc();
        $total_materias = $row['total'];
        $result->free();
    }
    
    // Consulta segura para inscripciones (sin filtro de estado)
    if ($result = $conexion->query($sql_inscripciones)) {
        $row = $result->fetch_assoc();
        $total_inscripciones = $row['total'];
        $result->free();
    }
    
} catch (Exception $e) {
    // Manejar errores de consulta sin detener la aplicación
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
<title>Panel Principal - Gestión del Colegio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #1a2a6c;
    --secondary: #00d6fc;
    --accent: #00eeff;
    --dark: #0f2027;
    --darker: #0a1a20;
    --light: rgba(255,255,255,0.1);
    --lighter: rgba(255,255,255,0.05);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--dark), #203a43, #2c5364);
    color: #fff;
    min-height: 100vh;
    line-height: 1.6;
    overflow-x: hidden;
}

/* Header mejorado */
.header-main {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    padding: 20px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo {
    font-size: 2.5em;
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.header-text h1 {
    font-size: 2em;
    font-weight: 700;
    margin-bottom: 5px;
}

.header-text p {
    opacity: 0.9;
    font-size: 0.9em;
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
}

.user-role {
    opacity: 0.8;
    font-size: 0.9em;
}

.logout-btn {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.logout-btn:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Dashboard mejorado */
.dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 20px;
}

.welcome-banner {
    background: linear-gradient(135deg, var(--lighter), var(--light));
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    text-align: center;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
}

.welcome-banner h2 {
    font-size: 2.2em;
    margin-bottom: 10px;
    background: linear-gradient(90deg, var(--secondary), #ffffff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.time-info {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 15px;
    opacity: 0.9;
}

/* Stats mejoradas */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--lighter);
    border: 1px solid var(--light);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    backdrop-filter: blur(15px);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    border-color: var(--accent);
}

.stat-icon {
    font-size: 3em;
    margin-bottom: 15px;
    opacity: 0.9;
}

.stat-number {
    font-size: 3.5em;
    font-weight: 800;
    background: linear-gradient(135deg, var(--secondary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
    line-height: 1;
}

.stat-label {
    font-size: 1.2em;
    opacity: 0.9;
    font-weight: 500;
}

/* Navigation mejorada */
.navigation-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 1.8em;
    margin-bottom: 25px;
    text-align: center;
    background: linear-gradient(90deg, var(--secondary), #ffffff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.nav-card {
    display: flex;
    align-items: center;
    padding: 25px;
    background: var(--lighter);
    border: 1px solid var(--light);
    border-radius: 15px;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    gap: 20px;
    position: relative;
    overflow: hidden;
}

.nav-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, var(--primary), var(--accent));
    transition: width 0.3s ease;
}

.nav-card:hover {
    transform: translateX(10px) translateY(-5px);
    background: linear-gradient(135deg, var(--primary), transparent);
    border-color: var(--accent);
    box-shadow: 0 15px 30px rgba(0,0,0,0.3);
}

.nav-card:hover::before {
    width: 8px;
}

.nav-icon {
    font-size: 2.2em;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.nav-card:hover .nav-icon {
    transform: scale(1.1);
    background: rgba(255,255,255,0.2);
}

.nav-content {
    flex: 1;
}

.nav-label {
    font-size: 1.3em;
    font-weight: 600;
    margin-bottom: 5px;
}

.nav-desc {
    opacity: 0.8;
    font-size: 0.9em;
}

/* Quick Actions */
.quick-actions {
    background: var(--lighter);
    border-radius: 20px;
    padding: 30px;
    backdrop-filter: blur(15px);
    border: 1px solid var(--light);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    color: white;
    padding: 15px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* Footer mejorado */
.footer-main {
    background: var(--darker);
    padding: 30px 20px;
    margin-top: 50px;
    border-top: 1px solid var(--light);
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    text-align: center;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.footer-link {
    color: var(--accent);
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.footer-link:hover {
    opacity: 0.8;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: linear-gradient(135deg, var(--dark), #203a43);
    margin: 5% auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    border: 1px solid var(--light);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    padding: 20px;
    border-radius: 20px 20px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.4em;
}

.close-modal {
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.close-modal:hover {
    background: rgba(255,255,255,0.2);
}

.modal-body {
    padding: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-select, .form-input {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid var(--light);
    background: rgba(255,255,255,0.05);
    color: white;
    font-size: 1em;
    backdrop-filter: blur(10px);
}

.form-select:focus, .form-input:focus {
    outline: none;
    border-color: var(--accent);
    background: rgba(255,255,255,0.1);
}

.submit-btn {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    color: white;
    padding: 15px 25px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 500;
    width: 100%;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* Alert Messages */
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid;
}

.alert-success {
    background: rgba(76, 175, 80, 0.2);
    border-color: #4CAF50;
    color: #a5d6a7;
}

.alert-error {
    background: rgba(244, 67, 54, 0.2);
    border-color: #f44336;
    color: #ef9a9a;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .user-section {
        flex-direction: column;
        gap: 10px;
    }
    
    .user-info {
        text-align: center;
    }
    
    .nav-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-card {
        padding: 20px;
    }
    
    .welcome-banner h2 {
        font-size: 1.8em;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}

@media (max-width: 480px) {
    .dashboard {
        padding: 20px 15px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-number {
        font-size: 2.8em;
    }
    
    .nav-icon {
        font-size: 1.8em;
        width: 50px;
        height: 50px;
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeInUp 0.6s ease-out;
}

/* Efectos de partículas (opcional) */
.particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.particle {
    position: absolute;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    animation: float 6s infinite ease-in-out;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}
</style>
</head>
<body>

<!-- Efecto de partículas -->
<div class="particles" id="particles"></div>

<header class="header-main">
    <div class="header-content">
        <div class="logo-section">
            <div class="logo">🏫</div>
            <div class="header-text">
                <h1>Sistema de Notas</h1>
                <p>Universidad Regional</p>
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
    <!-- Mostrar mensajes -->
    <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> fade-in">
        <?php 
        echo $_SESSION['mensaje']; 
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        ?>
    </div>
    <?php endif; ?>

    <section class="welcome-banner fade-in">
        <h2>Bienvenido, <?php echo explode(' ', $_SESSION['usuario']['nombre'])[0]; ?>! </h2>
        <p>Panel de control del sistema académico - Regional Sede Mixco 2</p>
        
    </section>

    <section class="stats-grid">
        <div class="stat-card fade-in" style="animation-delay: 0.1s">
            <div class="stat-icon"></div>
            <div class="stat-number" id="count-estudiantes">0</div>
            <div class="stat-label">Estudiantes Registrados</div>
        </div>
        
        <div class="stat-card fade-in" style="animation-delay: 0.2s">
            <div class="stat-icon"></div>
            <div class="stat-number" id="count-catedraticos">0</div>
            <div class="stat-label">Catedráticos</div>
        </div>
        
        <div class="stat-card fade-in" style="animation-delay: 0.3s">
            <div class="stat-icon"></div>
            <div class="stat-number" id="count-materias">0</div>
            <div class="stat-label">Materias</div>
        </div>
        
        <div class="stat-card fade-in" style="animation-delay: 0.4s">
            <div class="stat-icon"></div>
            <div class="stat-number" id="count-inscripciones">0</div>
            <div class="stat-label">Inscripciones</div>
        </div>
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
                'inscripciones.php' => ['', 'Inscripciones', 'Proceso de matrícula'],
                'materias.php' => ['', 'Materias', 'Plan de estudios'],
                'cursos.php' => ['', 'Cursos', 'Grupos y secciones'],
                'notas.php' => ['', 'Notas', 'Sistema de calificaciones'],
                'perfiles.php' => ['', 'Perfiles', 'Gestión de usuarios']
            ];
            
            $delay = 0.1;
            foreach ($modules as $link => $info) {
                echo "
                <a href='$link' class='nav-card fade-in' style='animation-delay: {$delay}s'>
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

    <section class="quick-actions fade-in" style="animation-delay: 0.8s">
        <h2 class="section-title">Reiniciar QR </h2>
        <div class="actions-grid">
         
            <button class="action-btn" onclick="openReset2FAModal()">
                <i class="fas fa-qrcode"></i> Reiniciar QR 2FA
            </button>
        </div>
    </section>
</main>

<!-- Modal para reiniciar QR 2FA -->
<div id="reset2FAModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-qrcode"></i> Reiniciar QR Google Authenticator</h3>
            <button class="close-modal" onclick="closeReset2FAModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuario:</label>
                    <select id="tipo_usuario" name="tipo_usuario" class="form-select" onchange="updateUserList()" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="administrador">Administrador</option>
                        <option value="catedratico">Catedrático</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="usuario_id">Usuario:</label>
                    <select id="usuario_id" name="usuario_id" class="form-select" required>
                        <option value="">Seleccionar usuario</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="reset_2fa" class="submit-btn">
                        <i class="fas fa-sync-alt"></i> Reiniciar QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
// Datos de usuarios para el modal
const usuarios = {
    administrador: <?php echo json_encode($administradores); ?>,
    catedratico: <?php echo json_encode($catedraticos); ?>
};

// Animación de contadores
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 40);
}

// Actualizar lista de usuarios en el modal
function updateUserList() {
    const tipo = document.getElementById('tipo_usuario').value;
    const usuarioSelect = document.getElementById('usuario_id');
    
    usuarioSelect.innerHTML = '<option value="">Seleccionar usuario</option>';
    
    if (tipo && usuarios[tipo]) {
        usuarios[tipo].forEach(usuario => {
            const option = document.createElement('option');
            option.value = usuario.id;
            option.textContent = `${usuario.nombre} ${usuario.apellido} (${usuario.correo})`;
            usuarioSelect.appendChild(option);
        });
    }
}

// Modal functions
function openReset2FAModal() {
    document.getElementById('reset2FAModal').style.display = 'block';
}

function closeReset2FAModal() {
    document.getElementById('reset2FAModal').style.display = 'none';
    // Reset form
    document.getElementById('tipo_usuario').value = '';
    document.getElementById('usuario_id').innerHTML = '<option value="">Seleccionar usuario</option>';
}

// Cerrar modal al hacer click fuera
window.onclick = function(event) {
    const modal = document.getElementById('reset2FAModal');
    if (event.target === modal) {
        closeReset2FAModal();
    }
}

// Inicializar contadores después de cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Contadores de estadísticas
    animateCounter(document.getElementById('count-estudiantes'), <?php echo $total_estudiantes; ?>);
    animateCounter(document.getElementById('count-catedraticos'), <?php echo $total_catedraticos; ?>);
    animateCounter(document.getElementById('count-materias'), <?php echo $total_materias; ?>);
    animateCounter(document.getElementById('count-inscripciones'), <?php echo $total_inscripciones; ?>);
    
    // Crear partículas de fondo
    createParticles();
});

// Crear efecto de partículas
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 15;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Posición y tamaño aleatorio
        const size = Math.random() * 60 + 10;
        const left = Math.random() * 100;
        const top = Math.random() * 100;
        const delay = Math.random() * 5;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${left}vw`;
        particle.style.top = `${top}vh`;
        particle.style.animationDelay = `${delay}s`;
        particle.style.opacity = Math.random() * 0.3 + 0.1;
        
        particlesContainer.appendChild(particle);
    }
}

// Confirmar cierre de sesión
function confirmLogout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = '?logout=true';
    }
}

// Actualizar hora en tiempo real
function updateTime() {
    const now = new Date();
    const timeElement = document.querySelector('.time-info span:nth-child(2)');
    if (timeElement) {
        timeElement.innerHTML = `<i class="fas fa-clock"></i> ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
    }
}

setInterval(updateTime, 60000);
</script>

</body>
</html>