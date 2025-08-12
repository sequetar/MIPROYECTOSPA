<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: admin.php');
    exit();
}
include("includes/db.php");

$id = $_GET['id'] ?? null;
if ($id) {
    mysqli_query($conn, "DELETE FROM citas WHERE id = $id");
}

header("Location: citas.php");
exit();
