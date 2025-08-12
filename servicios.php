<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}
if (!empty($_SESSION['error'])) {
    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

if (!empty($_SESSION['success'])) {
    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
include("includes/db.php");

// Consultar servicios
$result = mysqli_query($conn, "SELECT * FROM servicios ORDER BY nombre ASC");

include("header_admin.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center text-primary">Gestión de Servicios</h1>

    <div class="mb-3 text-end">
        <a href="nuevo_servicio.php" class="btn btn-success">➕ Nuevo Servicio</a>
    </div>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="text-center">No hay servicios registrados.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Nombre</th>
                        <th>Duración (min)</th>
                        <th>Precio</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['duracion_minutos']) ?></td>
                            <td>$<?= number_format($row['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($row['descripcion']) ?></td>
                            <td>
                                <a href="editar_servicio.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1">✏️ Editar</a>
                                <a href="eliminar_servicio.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este servicio?')">🗑️ Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php include("footer_visible.php"); ?>
<?php include("footer_admin.php"); ?>
</body>
</html>
