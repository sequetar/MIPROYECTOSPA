<?php
include("includes/db.php");
date_default_timezone_set('America/Bogota');

// Consultar servicios
$sql = "SELECT * FROM servicios";
$resultado = $conn->query($sql);
$servicios = [];
while ($fila = $resultado->fetch_assoc()) {
    $servicios[] = $fila;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenidos a Nuestro Spa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        body {
            background-color: #e8ecf0ff;
        }
        .hero {
            background: url('img/spa-banner.jpg') center/cover no-repeat;
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        .map-container {
            height: 400px;
        }
    </style>
</head>
<body>

<!-- Sección de presentación -->
<section class="hero">
    <h1>🌸 Bienvenidos a Spa Belleza & Relax 🌸</h1>
    <p>Tu lugar ideal para relajarte y consentirte en Medellín</p>
</section>

<div class="container my-5">
    <h2 class="text-center mb-4">Nuestro Catálogo de Servicios</h2>

    <!-- Carrusel de servicios -->
    <div id="carouselServicios" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

            <?php
            $chunks = array_chunk($servicios, 3); // 3 servicios por slide
            foreach ($chunks as $index => $grupo):
            ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="row justify-content-center">
                        <?php foreach ($grupo as $servicio): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <img src="img/<?= htmlspecialchars($servicio['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($servicio['nombre']) ?>">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><?= htmlspecialchars($servicio['nombre']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($servicio['descripcion']) ?></p>
                                        <p class="mb-1"><strong>Duración:</strong> <?= intval($servicio['duracion_minutos']) ?> min</p>
                                        <p class="fw-bold text-success">$<?= number_format($servicio['precio'], 0, ',', '.') ?></p>
                                        <a href="agendar.php?servicio_id=<?= $servicio['id'] ?>" class="btn btn-success">
                                            Agendar Cita
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselServicios" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselServicios" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<!-- Sección de contacto y mapa -->
<div class="container my-5">
    <h2 class="text-center mb-4">📍 Encuéntranos en Medellín</h2>
    <div class="row">
        <div class="col-md-6">
            <h4>Información de contacto</h4>
            <p><strong>Dirección:</strong> Calle 10 #25-30, Medellín</p>
            <p><strong>Teléfono:</strong> +57 300 123 4567</p>
            <p><strong>Email:</strong> contacto@spabelleza.com</p>
            <p><strong>Horario:</strong> Lunes a Sábado - 8:00 AM a 8:00 PM</p>
        </div>
        <div class="col-md-6">
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.903409208494!2d-75.57699748523827!3d6.244203927732774!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e4429a089cde3f5%3A0xb9c06b4225e3a38e!2sMedell%C3%ADn%2C%20Antioquia!5e0!3m2!1ses!2sco!4v1692556543210!5m2!1ses!2sco"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include("footer_visible.php"); ?>

</body>
</html>
