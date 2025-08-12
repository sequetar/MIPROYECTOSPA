<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

// Consultar personal
$result = mysqli_query($conn, "SELECT * FROM personal ORDER BY nombre ASC");

include("header_admin.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
   
</head>
<body class="container py-4">

<h1 class="mb-4">Gestión de Personal</h1>

<a href="nuevo_personal.php" class="btn btn-success mb-3">Agregar Nuevo Personal</a>

<?php if (mysqli_num_rows($result) === 0): ?>
    <p>No hay personal registrado.</p>
<?php else: ?>
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>Nombre</th>
                <th>Disponible</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= $row['disponible'] ? 'Sí' : 'No' ?></td>
                <td>
                    <a href="editar_personal.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="eliminar_personal.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Está seguro de eliminar este personal?');">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
<?php endif; ?>
<?php include("footer_visible.php"); ?>
<?php
include("footer_admin.php");
?>
</body>
</html>
