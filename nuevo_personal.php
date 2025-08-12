<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    if ($nombre == "") {
        $error = "El nombre es obligatorio.";
    } else {
        $sql = "INSERT INTO personal (nombre, disponible) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $disponible);
        if ($stmt->execute()) {
            $success = "Personal agregado con éxito.";
        } else {
            $error = "Error al guardar el personal. Intenta de nuevo.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:500px;">
    <h1 class="mb-4 text-center">Agregar Nuevo Personal</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            <div class="invalid-feedback">El nombre es obligatorio.</div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="disponible" name="disponible" <?= isset($_POST['disponible']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="disponible">Disponible</label>
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar Personal</button>
        <a href="personal.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
    </form>
</div>

<script>
//VALIDADCIÓN BOOTSTRAP
(() => {
  'use strict'
  const forms = document.querySelectorAll('form')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
