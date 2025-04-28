<?php
session_start();
require_once '../php/c.php';

$secundario = $_SESSION['secundario'] ?? null;
if (!$secundario) {
  die("No hay sesión activa o 'secundario' no está definido.");
}

$fechas_inhabiles = [];
$horas_disponibles = [];
$fechas_completas = [];
$horas_ocupadas_por_fecha = [];

$sql = "SELECT fecha FROM dias_inhabiles WHERE estatus = 't' AND fecha IS NOT NULL";
$resultado = pg_query($conexion, $sql);
if ($resultado) {
  while ($fila = pg_fetch_assoc($resultado)) {
    $fechas_inhabiles[] = date("Y-m-d", strtotime($fila['fecha']));
  }
}

$sql_horas = "SELECT id, hora FROM horas WHERE estatus = 't' ORDER BY hora ASC";
$resultado_horas = pg_query($conexion, $sql_horas);
$horas_id_map = [];
if ($resultado_horas) {
  while ($fila = pg_fetch_assoc($resultado_horas)) {
    $horas_disponibles[] = $fila['hora'];
    $horas_id_map[$fila['id']] = $fila['hora'];
  }
}

$sql_ocupadas = "
  SELECT fecha
  FROM historial_citas
  WHERE servicio_principal_id = $secundario
  GROUP BY fecha
  HAVING COUNT(DISTINCT hora_id) = (
    SELECT COUNT(*) FROM horas WHERE estatus = 't'
  )
";
$resultado_ocupadas = pg_query($conexion, $sql_ocupadas);
if ($resultado_ocupadas) {
  while ($fila = pg_fetch_assoc($resultado_ocupadas)) {
    $fechas_completas[] = date("Y-m-d", strtotime($fila['fecha']));
  }
}

$sql_horas_ocupadas = "
  SELECT fecha, hora_id
  FROM historial_citas
  WHERE servicio_principal_id = $secundario
";
$res_horas_ocupadas = pg_query($conexion, $sql_horas_ocupadas);
if ($res_horas_ocupadas) {
  while ($fila = pg_fetch_assoc($res_horas_ocupadas)) {
    $fecha = date("Y-m-d", strtotime($fila['fecha']));
    $hora = $horas_id_map[$fila['hora_id']] ?? null;
    if ($hora) {
      $horas_ocupadas_por_fecha[$fecha][] = $hora;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Citas ICEO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/i18n/jquery-ui-i18n.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <style>
    .hora-btn {
      margin: 5px;
      padding: 5px 10px;
      font-size: 12px;
      background-color: #f9f9f9;
      border-radius: 5px;
      border: 1px solid #ccc;
      cursor: pointer;
    }
    .hora-btn:hover {
      background-color: #007bff;
      color: white;
    }
    .hora-btn.selected {
      background-color: #007bff;
      color: white;
    }
    .hora-btn.disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
    .hora-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
  </style>
</head>
<body>
  <div class="container mt-4">
    <h2 class="text-center">Registro de Citas ICEO</h2>

    <form id="formularioCita" action="verificardatos.php" method="POST" class="mt-4">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre completo *</label>
        <input type="text" class="form-control" name="nombre" required>
      </div>

      <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="tel" class="form-control" name="telefono" maxlength="10" pattern="[0-9]{10}">
      </div>

      <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="email" class="form-control" name="correo">
      </div>

      <div class="mb-3">
        <label for="folio" class="form-label">Folio Jurídico *</label>
        <input type="text" class="form-control" name="folio" required>
      </div>

      <div class="mb-3">
        <label for="fecha_cita" class="form-label">Fecha de cita *</label>
        <input type="text" id="fecha_cita" name="fecha_cita" class="form-control" required autocomplete="off">
      </div>

      <div class="mb-3">
        <label class="form-label">Hora de cita *</label>
        <div id="hora_grid" class="hora-grid">
          <?php foreach ($horas_disponibles as $hora): ?>
            <?php $hora_id = array_search($hora, $horas_id_map); ?>
            <button type="button" class="hora-btn disabled" data-hour="<?= htmlspecialchars($hora) ?>" data-id="<?= $hora_id ?>" disabled>
              <?= htmlspecialchars($hora) ?>
            </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="hora_cita" id="hora_cita" required>
        <input type="hidden" name="idhora" id="idhora" required>
      </div>

      <div id="errorMensaje" class="text-danger text-center mb-3" style="display:none;">
        Por favor, ingresa al menos un teléfono o correo.
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">Siguiente</button>
      </div>
    </form>
  </div>

  <script>
    $(document).ready(function () {
      $.datepicker.setDefaults($.datepicker.regional["es"]);
      const diasInhabiles = <?= json_encode($fechas_inhabiles) ?>;
      const diasCompletos = <?= json_encode($fechas_completas) ?>;
      const horasOcupadas = <?= json_encode($horas_ocupadas_por_fecha) ?>;
      const fechasInhabilitadas = diasInhabiles.concat(diasCompletos);

      const today = new Date();
      const currentYear = today.getFullYear();

      $("#fecha_cita").datepicker({
        minDate: today,
        maxDate: new Date(currentYear, 11, 31),
        changeMonth: true,
        changeYear: false,
        yearRange: `${currentYear}:${currentYear}`,
        dateFormat: "yy-mm-dd", // FORMATO COMPATIBLE CON PHP
        beforeShowDay: function (date) {
          const d = date.toISOString().split('T')[0]; // yyyy-mm-dd
          const day = date.getDay();
          if (day === 0 || day === 6 || fechasInhabilitadas.includes(d)) {
            return [false, ""];
          }
          return [true, ""];
        },
        onSelect: function (fecha) {
          const ocupadas = horasOcupadas[fecha] || [];
          const hoy = new Date();
          const fechaSeleccionada = new Date(fecha);

          $('.hora-btn').each(function () {
            const hora = $(this).data('hour');
            const [horaStr, minutoStr] = hora.split(':');
            const horaBtn = new Date(fechaSeleccionada);
            horaBtn.setHours(parseInt(horaStr), parseInt(minutoStr), 0, 0);

            const esHoraPasada = (fechaSeleccionada.toDateString() === hoy.toDateString()) && (horaBtn <= hoy);

            if (ocupadas.includes(hora) || esHoraPasada) {
              $(this).addClass('disabled').prop('disabled', true);
            } else {
              $(this).removeClass('disabled').prop('disabled', false);
            }
          });
        }
      });

      $('#hora_grid').on('click', '.hora-btn:not(.disabled)', function () {
        const hora = $(this).data('hour');
        const idhora = $(this).data('id');

        $('#hora_cita').val(hora);
        $('#idhora').val(idhora);

        $('.hora-btn').removeClass('selected');
        $(this).addClass('selected');
      });

      $('#formularioCita').on('submit', function (e) {
        const telefono = $('input[name="telefono"]').val().trim();
        const correo = $('input[name="correo"]').val().trim();
        const horaCita = $('#hora_cita').val().trim(); 

        if (!telefono && !correo) {
          e.preventDefault();
          $('#errorMensaje').show();
        } else if (!horaCita) {
          e.preventDefault();
          alert("Por favor, selecciona una hora para la cita.");
        } else {
          $('#errorMensaje').hide();
        }
      });
    });
  </script>
</body>
</html>
