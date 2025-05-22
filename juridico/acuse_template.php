<?php
$bg_path = $_SERVER['DOCUMENT_ROOT'] . '/CitasIceo/Imagenes/Fondo.jpeg';
$bg_data = '';
if (file_exists($bg_path)) {
    $bg_data = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($bg_path));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        <?php if ($bg_data): ?>
        @page {
            background-image: url('<?= $bg_data ?>');
            background-size: cover;
            background-position: center center;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 20px;
            background-image: url('<?= $bg_data ?>');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
        }
        <?php else: ?>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 20px;
        }
        <?php endif; ?>
        .header { width: 100%; position: relative; margin-bottom: 20px; }
        .logo { position: absolute; top: 0; right: 0; height: 70px; }
        .title { font-size: 22px; font-weight: bold; }
        .subtitle { font-size: 14px; color: #555; margin-top: 5px; }
        .section { margin-top: 20px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #ccc; margin-bottom: 10px; }
        .info { margin-bottom: 5px; }
        .requisitos { margin-top: 10px; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?= $logo_src ?>" class="logo" alt="Logo ICEO">
        <div class="title">Acuse de Cita</div>
        <div class="subtitle">Instituto Catastral del Estado de Oaxaca</div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Ciudadano</div>
        <div class="info"><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></div>
        <div class="info"><strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></div>
        <div class="info"><strong>Correo Electrónico:</strong> <?= htmlspecialchars($correo) ?></div>
        <div class="info"><strong>Departamento:</strong> <?= htmlspecialchars($departamento) ?></div>
    </div>

    <div class="section">
        <div class="section-title">Detalles de la Cita</div>
        <div class="info"><strong>Folio Jurídico:</strong> <?= htmlspecialchars($folio) ?></div>
        <div class="info"><strong>Fecha:</strong> <?= htmlspecialchars($fecha_cita) ?></div>
        <div class="info"><strong>Hora:</strong> <?= htmlspecialchars($hora_cita) ?></div>
    </div>

    <div class="section">
        <div class="section-title">Servicios Solicitados</div>
        <div class="info"><strong>Departamento:</strong> <?= htmlspecialchars($departamento) ?></div>
        <div class="info"><strong>Área:</strong> <?= htmlspecialchars($nombre_servicio_principal) ?></div>
        <div class="info"><strong>Servicio:</strong> <?= htmlspecialchars($nombre_servicio_secundario) ?></div>
    </div>

    <div class="section">
        <div class="section-title">Requisitos para la Cita</div>
        <?php if (!empty($requisitos_filtrados)): ?>
            <ol class="requisitos">
                <?php foreach ($requisitos_filtrados as $req): ?>
                    <li><?= htmlspecialchars($req) ?></li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <div class="info">No hay requisitos para este servicio.</div>
        <?php endif; ?>
    </div>

    <div class="footer">
        Guarda este acuse como comprobante. Le recomendamos presentarse con al menos 10 minutos de anticipación.
    </div>
</body>
</html>
