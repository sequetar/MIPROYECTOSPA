<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: personal.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener datos del personal con sentencia preparada
$stmt = $conn->prepare("SELECT * FROM personal WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$personal = $result->fetch_assoc();
$stmt->close();

if (!$personal) {
    header("Location: personal.php");
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    if ($nombre === '') {
        $mensaje = "El nombre no puede estar vacío.";
    } else {
        $stmt = $conn->prepare("UPDATE personal SET nombre = ?, disponible = ? WHERE id = ?");
        $stmt->bind_param("sii", $nombre, $disponible, $id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: personal.php?msg=actualizado");
            exit();
        } else {
            $mensaje = "Error al actualizar los datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="container py-4">

<h1 class="mb-4">Editar Personal</h1>

<?php if ($mensaje): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="post" class="needs-validation" novalidate>
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($personal['nombre']) ?>" required>
        <div class="invalid-feedback">Por favor ingrese un nombre.</div>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" value="1" id="disponible" name="disponible" <?= $personal['disponible'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="disponible">Disponible</label>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="personal.php" class="btn btn-secondary ms-2">Cancelar</a>
</form>

<script>
// Bootstrap form validation
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

</body>
</html>
