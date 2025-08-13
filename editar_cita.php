<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: citas.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener cita con joins para mostrar servicio y personal
$stmt = $conn->prepare("SELECT c.*, s.nombre AS servicio_nombre, p.nombre AS personal_nombre 
                        FROM citas c
                        LEFT JOIN servicios s ON c.servicio_id = s.id
                        LEFT JOIN personal p ON c.personal_id = p.id
                        WHERE c.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cita = $result->fetch_assoc();
$stmt->close();

if (!$cita) {
    header("Location: citas.php");
    exit();
}

// Obtener lista de servicios y personal disponibles para select
$servicios = $conn->query("SELECT id, nombre FROM servicios ORDER BY nombre ASC");
$personal = $conn->query("SELECT id, nombre FROM personal WHERE disponible = 1 ORDER BY nombre ASC");

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_nombre = trim($_POST['cliente_nombre']);
    $telefono_cliente = trim($_POST['telefono_cliente']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $servicio_id = intval($_POST['servicio_id']);
    $personal_id = intval($_POST['personal_id']);

    if ($cliente_nombre === '' || $telefono_cliente === '' || empty($fecha) || empty($hora) || $servicio_id <= 0 || $personal_id <= 0) {
        $mensaje = "Por favor complete todos los campos correctamente.";
    } else {
        $stmt = $conn->prepare("UPDATE citas SET cliente_nombre = ?, telefono_cliente = ?, fecha = ?, hora = ?, servicio_id = ?, personal_id = ? WHERE id = ?");
        $stmt->bind_param("ssssiii", $cliente_nombre, $telefono_cliente, $fecha, $hora, $servicio_id, $personal_id, $id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: citas.php?msg=actualizado");
            exit();
        } else {
            $mensaje = "Error al actualizar la cita.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="container py-4">

<h1 class="mb-4">Editar Cita</h1>

<?php if ($mensaje): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="post" class="needs-validation" novalidate>
    <div class="mb-3">
        <label for="cliente_nombre" class="form-label">Nombre del Cliente</label>
        <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" value="<?= htmlspecialchars($cita['cliente_nombre']) ?>" required>
        <div class="invalid-feedback">Ingrese el nombre del cliente.</div>
    </div>

    <div class="mb-3">
        <label for="telefono_cliente" class="form-label">Teléfono</label>
        <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente" value="<?= htmlspecialchars($cita['telefono_cliente']) ?>" required>
        <div class="invalid-feedback">Ingrese el teléfono del cliente.</div>
    </div>

    <div class="mb-3">
        <label for="servicio_id" class="form-label">Servicio</label>
        <select class="form-select" id="servicio_id" name="servicio_id" required>
            <option value="">-- Seleccionar servicio --</option>
            <?php while ($row = $servicios->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= $row['id'] == $cita['servicio_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="invalid-feedback">Seleccione un servicio.</div>
    </div>

    <div class="mb-3">
        <label for="personal_id" class="form-label">Personal</label>
        <select class="form-select" id="personal_id" name="personal_id" required>
            <option value="">-- Seleccionar personal --</option>
            <?php while ($row = $personal->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= $row['id'] == $cita['personal_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="invalid-feedback">Seleccione un personal disponible.</div>
    </div>

    <div class="mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" class="form-control" id="fecha" name="fecha" value="<?= htmlspecialchars($cita['fecha']) ?>" required>
        <div class="invalid-feedback">Seleccione una fecha válida.</div>
    </div>

    <div class="mb-3">
        <label for="hora" class="form-label">Hora</label>
        <input type="time" class="form-control" id="hora" name="hora" value="<?= htmlspecialchars(substr($cita['hora'], 0, 5)) ?>" required>
        <div class="invalid-feedback">Seleccione una hora válida.</div>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="citas.php" class="btn btn-secondary ms-2">Cancelar</a>
</form>

<script>
// Bootstrap Validación de form 
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
