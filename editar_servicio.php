<?php 
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

$error = '';
$mensaje = '';

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: servicios.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener datos actuales del servicio
$stmt = $conn->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$servicio = $result->fetch_assoc();
$stmt->close();

if (!$servicio) {
    header("Location: servicios.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $duracion_minutos = intval($_POST['duracion_minutos'] ?? 0);
    $precio = floatval($_POST['precio'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fileName = $servicio['imagen']; // Mantener imagen actual si no se sube otra

    // Validar campos
    if (!$nombre || !$duracion_minutos || !$precio || !$descripcion) {
        $error = "Por favor, complete todos los campos.";
    }

    // Procesar imagen si se sube una nueva
    if (!$error && isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['imagen']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Solo se permiten imágenes JPG, PNG, GIF o WEBP.";
            } else {
                $uploadDir = 'img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Nombre único
                $newFileName = uniqid() . '-' . basename($_FILES['imagen']['name']);
                $targetFile = $uploadDir . $newFileName;

                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                    $error = "Error al subir la imagen.";
                } else {
                    // Borrar imagen anterior si existe y es distinta
                    if ($fileName && file_exists($uploadDir . $fileName)) {
                        unlink($uploadDir . $fileName);
                    }
                    $fileName = $newFileName;
                }
            }
        } else {
            $error = "Error en la carga de la imagen.";
        }
    }

    // Actualizar si no hay errores
    if (!$error) {
        $stmt = $conn->prepare("UPDATE servicios 
                                SET nombre = ?, duracion_minutos = ?, precio = ?, descripcion = ?, imagen = ? 
                                WHERE id = ?");
        $stmt->bind_param("siissi", $nombre, $duracion_minutos, $precio, $descripcion, $fileName, $id);

        if ($stmt->execute()) {
            $mensaje = "Servicio actualizado correctamente.";
            // Actualizar datos en memoria
            $servicio['nombre'] = $nombre;
            $servicio['duracion_minutos'] = $duracion_minutos;
            $servicio['precio'] = $precio;
            $servicio['descripcion'] = $descripcion;
            $servicio['imagen'] = $fileName;
        } else {
            $error = "Error al actualizar el servicio.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 600px;">
    <h1 class="mb-4 text-center">Editar Servicio</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label class="form-label">Nombre del servicio</label>
            <input type="text" class="form-control" name="nombre" required value="<?= htmlspecialchars($servicio['nombre']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Duración (minutos)</label>
            <input type="number" class="form-control" name="duracion_minutos" min="1" required value="<?= htmlspecialchars($servicio['duracion_minutos']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Precio ($)</label>
            <input type="number" step="0.01" class="form-control" name="precio" min="0" required value="<?= htmlspecialchars($servicio['precio']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="3" required><?= htmlspecialchars($servicio['descripcion']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagen actual</label><br>
            <?php if ($servicio['imagen'] && file_exists('img/' . $servicio['imagen'])): ?>
                <img src="img/<?= htmlspecialchars($servicio['imagen']) ?>" class="img-thumbnail mb-2" style="max-width:200px;">
            <?php else: ?>
                <p class="text-muted">No hay imagen disponible.</p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Cambiar imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
            <small class="text-muted">Si no sube una nueva, se mantendrá la actual.</small>
        </div>

        <div class="d-flex justify-content-between">
            <a href="servicios.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>

</body>
</html>
