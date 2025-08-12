<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}

include("includes/db.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Verificar si hay citas con ese servicio
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM citas WHERE servicio_id = $id");
    $row = mysqli_fetch_assoc($res);

    if ($row['total'] > 0) {
        // No se puede eliminar
        $_SESSION['error'] = "No se puede eliminar el servicio porque tiene citas agendadas.";
    } else {
        mysqli_query($conn, "DELETE FROM servicios WHERE id = $id");
        $_SESSION['success'] = "Servicio eliminado correctamente.";
    }
}

header("Location: servicios.php");
exit();
?>
