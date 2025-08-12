<?php
include("includes/db.php");

// Inicializar array para errores
$errors = [];

// Obtener y limpiar datos del formulario
$cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
$telefono_cliente = trim($_POST['telefono_cliente'] ?? '');
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$servicio_id = intval($_POST['servicio_id'] ?? 0);
$personal_id = intval($_POST['personal_id'] ?? 0); // Puede venir o no

// Validar datos básicos
if (!$cliente_nombre) {
    $errors[] = "El nombre del cliente es obligatorio.";
}
if (!$telefono_cliente) {
    $errors[] = "El teléfono del cliente es obligatorio.";
}
if (!$fecha) {
    $errors[] = "La fecha es obligatoria.";
}
if (!$hora) {
    $errors[] = "La hora es obligatoria.";
}
if ($servicio_id <= 0) {
    $errors[] = "El servicio seleccionado es inválido.";
}

// Si no se recibe personal_id, buscar un personal disponible
if ($personal_id <= 0 && empty($errors)) {
    // Ejemplo simple: buscar cualquier personal disponible
    $sql_personal = "SELECT id FROM personal WHERE disponible = 1 LIMIT 1";
    $result = $conn->query($sql_personal);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $personal_id = intval($row['id']);
    } else {
        $errors[] = "No hay personal disponible para este servicio en la fecha y hora seleccionadas.";
    }
}

// Si hay errores, mostrar y detener ejecución
if (!empty($errors)) {
    echo "<h3 style='color:red;'>Errores al agendar la cita:</h3><ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo "<a href='javascript:history.back()'>Volver</a>";
    exit;
}

// Preparar y ejecutar inserción segura con prepared statement
$sql_insert = "INSERT INTO citas (cliente_nombre, telefono_cliente, fecha, hora, servicio_id, personal_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("ssssii", $cliente_nombre, $telefono_cliente, $fecha, $hora, $servicio_id, $personal_id);

if ($stmt->execute()) {
    // Redirigir o mostrar mensaje de éxito
    echo "<h3 style='color:green;'>Cita agendada con éxito.</h3>";
    echo "<p><a href='index.php'>Volver al inicio</a></p>";
} else {
    echo "<h3 style='color:red;'>Error al guardar la cita. Intente nuevamente.</h3>";
    echo "<p><a href='javascript:history.back()'>Volver</a></p>";
}
?>
