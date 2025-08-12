<?php
// Configuración de conexión a la base de datos
$servername = "localhost"; // Servidor (en XAMPP es localhost)
$username   = "root";      // Usuario de la base de datos
$password   = "";          // Contraseña (vacía por defecto en XAMPP)
$database   = "spa";       // Nombre de la base de datos

// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $database);

// Verificar conexión
if (!$conn) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}
?>
