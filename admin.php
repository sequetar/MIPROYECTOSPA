<?php
session_start();
include("includes/db.php");

// --- CONFIGURACIÓN DE CREDENCIALES ---
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); 

// --- LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// --- LOGIN ---
if (isset($_POST['login'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin_logged'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

// --- SI NO ESTÁ LOGUEADO, MOSTRAR LOGIN ---
if (empty($_SESSION['admin_logged'])):
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Admin Spa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h1 class="mb-4">Panel Administrativo - Login</h1>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" class="border p-4 rounded shadow-sm" style="max-width:400px;">
    <div class="mb-3">
      <label for="username" class="form-label">Usuario</label>
      <input type="text" name="username" id="username" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button name="login" class="btn btn-primary w-100">Entrar</button>
  </form>
</body>
</html>
<?php
exit;
endif;

// --- CONSULTAS RESUMEN ---
$citas_result = mysqli_query($conn, "
    SELECT c.*, s.nombre AS servicio_nombre, p.nombre AS personal_nombre 
    FROM citas c
    LEFT JOIN servicios s ON c.servicio_id = s.id
    LEFT JOIN personal p ON c.personal_id = p.id
    ORDER BY c.fecha DESC, c.hora DESC LIMIT 5
");

$personal_result = mysqli_query($conn, "SELECT * FROM personal ORDER BY nombre ASC LIMIT 5");
$servicios_result = mysqli_query($conn, "SELECT * FROM servicios ORDER BY nombre ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Administrativo Spa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<nav class="mb-4">
  <a href="admin.php" class="btn btn-primary me-2">Inicio</a>
  <a href="personal.php" class="btn btn-outline-primary me-2">Personal</a>
  <a href="servicios.php" class="btn btn-outline-primary me-2">Servicios</a>
  <a href="citas.php" class="btn btn-outline-primary me-2">Citas</a>
  <a href="admin.php?logout=1" class="btn btn-danger float-end">Cerrar sesión</a>
</nav>

<h1>Panel Administrativo - Spa</h1>

<!-- Últimas Citas -->
<h2>Últimas Citas Programadas</h2>
<?php if (mysqli_num_rows($citas_result) === 0): ?>
  <p>No hay citas programadas.</p>
<?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Teléfono</th>
        <th>Servicio</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Personal</th>
      </tr>
    </thead>
    <tbody>
    <?php while($cita = mysqli_fetch_assoc($citas_result)): ?>
      <tr>
        <td><?= htmlspecialchars($cita['cliente_nombre']) ?></td>
        <td><?= htmlspecialchars($cita['telefono_cliente']) ?></td>
        <td><?= htmlspecialchars($cita['servicio_nombre'] ?? '—') ?></td>
        <td><?= htmlspecialchars($cita['fecha']) ?></td>
        <td><?= htmlspecialchars(substr($cita['hora'], 0, 5)) ?></td>
        <td><?= htmlspecialchars($cita['personal_nombre'] ?? 'Sin asignar') ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>

<!-- Personal -->
<h2>Personal (Resumen)</h2>
<?php if (mysqli_num_rows($personal_result) === 0): ?>
  <p>No hay personal registrado.</p>
<?php else: ?>
  <ul class="list-group mb-4">
    <?php while($p = mysqli_fetch_assoc($personal_result)): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span><?= htmlspecialchars($p['nombre']) ?></span>
        <span class="badge bg-<?= $p['disponible'] ? 'success' : 'secondary' ?>">
          <?= $p['disponible'] ? 'Disponible' : 'Ocupado' ?>
        </span>
      </li>
    <?php endwhile; ?>
  </ul>
  <a href="personal.php" class="btn btn-outline-primary mb-4">Ver todo el personal</a>
<?php endif; ?>

<!-- Servicios -->
<h2>Servicios (Resumen)</h2>
<?php if (mysqli_num_rows($servicios_result) === 0): ?>
  <p>No hay servicios registrados.</p>
<?php else: ?>
  <ul class="list-group mb-4">
    <?php while($s = mysqli_fetch_assoc($servicios_result)): ?>
      <li class="list-group-item"><?= htmlspecialchars($s['nombre']) ?></li>
    <?php endwhile; ?>
  </ul>
  <a href="servicios.php" class="btn btn-outline-primary mb-4">Ver todos los servicios</a>
<?php endif; ?>

<?php include("footer_visible.php"); ?>
<?php include("footer_admin.php"); ?>
</body>
</html>
