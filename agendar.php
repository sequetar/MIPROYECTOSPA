<?php 
session_start();
include("includes/db.php");

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
    $telefono_cliente = trim($_POST['telefono_cliente'] ?? '');
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $servicio_id = intval($_POST['servicio_id'] ?? 0);
    $personal_id = intval($_POST['personal_id'] ?? 0);

    // Validaciones básicas
    if (!$cliente_nombre || !$telefono_cliente || !$fecha || !$hora || !$servicio_id || !$personal_id) {
        $error = "Por favor, complete todos los campos.";
    } 
    // Validar que teléfono contenga solo números y entre 7 y 15 dígitos
    elseif (!preg_match('/^\d{7,15}$/', $telefono_cliente)) {
        $error = "El teléfono debe contener solo números y tener entre 7 y 15 dígitos.";
    }
    // Validar fecha no sea anterior a hoy
    elseif ($fecha < date('Y-m-d')) {
        $error = "La fecha no puede ser anterior a hoy.";
    } 
    // Validar personal disponible
    else {
        $res = mysqli_query($conn, "SELECT * FROM personal WHERE id = $personal_id AND disponible = 1");
        if (mysqli_num_rows($res) === 0) {
            $error = "Personal inválido o no disponible.";
        } else {
            // Insertar cita con prepared statement
            $stmt = $conn->prepare("INSERT INTO citas (cliente_nombre, telefono_cliente, fecha, hora, servicio_id, personal_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $cliente_nombre, $telefono_cliente, $fecha, $hora, $servicio_id, $personal_id);

            if ($stmt->execute()) {
                $mensaje = "¡Cita agendada con éxito! Te esperamos en la fecha y hora seleccionadas.";
                $_POST = []; // Limpiar formulario
            } else {
                $error = "Error al guardar la cita. Intente nuevamente.";
            }
            $stmt->close();
        }
    }
}

// Consultar servicios para el select
$servicios = mysqli_query($conn, "SELECT * FROM servicios ORDER BY nombre ASC");
// Consultar personal disponible para el select
$personal = mysqli_query($conn, "SELECT * FROM personal WHERE disponible = 1 ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Agendar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 600px;">
    <h1 class="mb-4 text-center">Agendar Cita</h1>

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

    <form method="post" class="bg-white p-4 shadow rounded" novalidate>
        <div class="mb-3">
            <label for="cliente_nombre" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" required
                   value="<?= htmlspecialchars($_POST['cliente_nombre'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="telefono_cliente" class="form-label">Teléfono</label>
            <input type="tel" pattern="[0-9]{7,15}" title="Solo números, entre 7 y 15 dígitos" class="form-control" id="telefono_cliente" name="telefono_cliente" required
                   value="<?= htmlspecialchars($_POST['telefono_cliente'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="servicio_id" class="form-label">Servicio</label>
            <select id="servicio_id" name="servicio_id" class="form-select" required>
                <option value="" disabled <?= empty($_POST['servicio_id']) ? 'selected' : '' ?>>Seleccione un servicio</option>
                <?php while ($row = mysqli_fetch_assoc($servicios)): ?>
                    <option value="<?= $row['id'] ?>" <?= (isset($_POST['servicio_id']) && $_POST['servicio_id'] == $row['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="personal_id" class="form-label">Personal disponible</label>
            <select id="personal_id" name="personal_id" class="form-select" required>
                <option value="" disabled <?= empty($_POST['personal_id']) ? 'selected' : '' ?>>Seleccione un profesional</option>
                <?php while ($p = mysqli_fetch_assoc($personal)): ?>
                    <option value="<?= $p['id'] ?>" <?= (isset($_POST['personal_id']) && $_POST['personal_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required
                   min="<?= date('Y-m-d') ?>"
                   value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" class="form-control" id="hora" name="hora" required
                   value="<?= htmlspecialchars($_POST['hora'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-success w-100">Confirmar Cita</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Solo números en teléfono mientras escribe
    document.getElementById('telefono_cliente').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>

</body>
</html>
