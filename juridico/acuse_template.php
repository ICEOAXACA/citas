<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse de Cita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100px;
        }
        .header, .footer {
            text-align: center;
            margin-top: 20px;
        }
        .content {
            margin-top: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .requisitos {
            list-style-type: decimal;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="<?= $logo_src ?>" alt="Logo">
    </div>

    <div class="header">
        <h1>Acuse de Cita</h1>
        <p>Folio: <?= $folio ?></p>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">Datos del Ciudadano</div>
            <p><strong>Nombre:</strong> <?= $nombre ?></p>
            <p><strong>Teléfono:</strong> <?= $telefono ?></p>
            <p><strong>Correo Electrónico:</strong> <?= $correo ?></p>
            <p><strong>Departamento:</strong> <?= $departamento ?></p>
        </div>

        <div class="section">
            <div class="section-title">Detalles de la Cita</div>
            <p><strong>Fecha:</strong> <?= $fecha_cita ?></p>
            <p><strong>Hora:</strong> <?= $hora_cita ?></p>
        </div>

        <div class="section">
            <div class="section-title">Servicios Solicitados</div>
            <p><strong>Servicio Principal:</strong> <?= $nombre_servicio_principal ?></p>
            <p><strong>Servicio Secundario:</strong> <?= $nombre_servicio_secundario ?></p>
        </div>

        <?php if (!empty($requisitos_filtrados)): ?>
        <div class="section">
            <div class="section-title">Requisitos para la Cita</div>
            <ul class="requisitos">
                <?php foreach ($requisitos_filtrados as $index => $req): ?>
                <li><?= $index + 1 ?>. <?= htmlspecialchars($req) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Generado el <?= date('d/m/Y H:i:s') ?></p>
    </div>
</body>
</html>
