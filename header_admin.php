<?php
//session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel Administrativo Spa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />

</head>
<body class="container py-4">

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 rounded">
  <a class="navbar-brand" href="admin.php">Spa Admin</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link" href="admin.php">Inicio</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="personal.php">Personal</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="servicios.php">Servicios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="citas.php">Citas</a>
      </li>
    </ul>
    <a href="admin.php?logout=1" class="btn btn-danger">Cerrar sesión</a>
  </div>
</nav>    
</body>
