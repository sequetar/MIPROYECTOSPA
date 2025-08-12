<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

// Consultar citas con detalles de servicio y personal
$citas_result = mysqli_query($conn, "SELECT c.*, s.nombre AS servicio_nombre, p.nombre AS personal_nombre 
                                     FROM citas c
                                     LEFT JOIN servicios s ON c.servicio_id = s.id
                                     LEFT JOIN personal p ON c.personal_id = p.id
                                     ORDER BY c.fecha ASC, c.hora ASC");

include("header_admin.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center text-primary">Gestión de Citas</h1>

    <div class="mb-3 text-end">
        <a href="agregar_cita.php" class="btn btn-success">➕ Agregar Nueva Cita</a>
    </div>

    <?php if (mysqli_num_rows($citas_result) === 0): ?>
        <p class="text-center">No hay citas programadas.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Personal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cita = mysqli_fetch_assoc($citas_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($cita['cliente_nombre']) ?></td>
                            <td><?= htmlspecialchars($cita['telefono_cliente']) ?></td>
                            <td><?= htmlspecialchars($cita['servicio_nombre'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($cita['fecha']) ?></td>
                            <td><?= htmlspecialchars(substr($cita['hora'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($cita['personal_nombre'] ?? 'Sin asignar') ?></td>
                            <td>
                                <a href="editar_cita.php?id=<?= $cita['id'] ?>" class="btn btn-sm btn-outline-primary me-1">✏️ Editar</a>
                                <a href="eliminar_cita.php?id=<?= $cita['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta cita?')">🗑️ Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include("footer_admin.php"); ?>
</body>
</html>
