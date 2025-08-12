<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $duracion_minutos = intval($_POST['duracion_minutos'] ?? 0);
    $precio = floatval($_POST['precio'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fileName = '';

    // Validaciones básicas
    if (!$nombre || !$duracion_minutos || !$precio || !$descripcion) {
        $error = "Por favor, complete todos los campos correctamente.";
    }

    // Procesar imagen si no hay error aún
    if (!$error) {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['imagen']['type'];

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Solo se permiten imágenes JPG, PNG o GIF.";
            } else {
                $uploadDir = 'img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Nombre único para evitar sobreescrituras
                $fileName = uniqid() . '-' . basename($_FILES['imagen']['name']);
                $targetFile = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                    $error = "Error al subir la imagen.";
                }
            }
        } else {
            $error = "Debe seleccionar una imagen para el servicio.";
        }
    }

    // Insertar en la base de datos si no hay errores
    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO servicios (nombre, duracion_minutos, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sidss", $nombre, $duracion_minutos, $precio, $descripcion, $fileName);

        if ($stmt->execute()) {
            $mensaje = "Servicio agregado exitosamente.";
            // Limpiar campos para nuevo ingreso
            $_POST = [];
        } else {
            $error = "Error al guardar el servicio. Intente nuevamente.";
            // Opcional: borrar imagen subida si hubo error DB
            if ($fileName && file_exists($targetFile)) {
                unlink($targetFile);
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Nuevo Servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 600px;">
    <h1 class="mb-4 text-center">Agregar Nuevo Servicio</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del servicio</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="duracion_minutos" class="form-label">Duración (minutos)</label>
            <input type="number" class="form-control" id="duracion_minutos" name="duracion_minutos" min="1" required
                   value="<?= htmlspecialchars($_POST['duracion_minutos'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio ($)</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" min="0" required
                   value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del servicio</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
        </div>

        <div class="d-flex justify-content-between mb-3">
    <a href="servicios.php" class="btn btn-secondary">Cancelar</a>
</div>
<button type="submit" class="btn btn-secondary">Guardar Servicio</button>        
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
