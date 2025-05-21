<?php
session_start();
require_once '../php/c.php';

$secundario = $_SESSION['secundario'] ?? null;
if (!$secundario) {
  die("No hay sesión activa o 'secundario' no está definido.");
}

// Carga de fechas y horas
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
  <title>Registro de Cita | ICEO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/i18n/jquery-ui-i18n.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="../style.css">

  <style>
    body {
      background: url('../Imagenes/Fondo.jpeg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }

    .form-container {
      background-color: rgba(255, 255, 255, 0.96);
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
      max-width: 800px;
      margin: 40px auto;
    }

    .hora-btn {
      margin: 5px;
      padding: 5px 12px;
      font-size: 14px;
      background-color: #f1f1f1;
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

    .top-bar {
      background-color: rgba(255, 255, 255, 0.9);
      border-bottom: 2px solid #861f41;
      padding: 1rem;
    }

    .nav-link {
      font-weight: bold;
      color: #fff;
      background-color: #861f41;
      padding: 0.3rem 0.7rem;
      border-radius: 5px;
      text-decoration: none;
    }

    .nav-link:hover {
      background-color: #6e1b37;
      color: #fff;
    }

    h2 {
      color: #343a40;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="top-bar">
      <div class="row align-items-center justify-content-center">
        <div class="col-auto">
          <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
            <img src="../Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
          </a>
        </div>
        <div class="col text-center">
          <span class="fw-bold fs-4">Registro de Cita ICEO</span>
        </div>
        <div class="col-auto">
          <a href="../login.php" class="nav-link">Iniciar sesión</a>
        </div>
      </div>
    </div>

    <div class="form-container">
      <form id="formularioCita" action="verificardatos.php" method="POST">
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
          <label for="folio" class="form-label">Folio Jurídico</label>
          <input type="text" class="form-control" name="folio">
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
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Regresar</button>

          <button type="submit" class="btn btn-primary">Siguiente</button>
        </div>
      </form>
    </div>
  </div>

<script>
  $(function () {
    $.datepicker.setDefaults($.datepicker.regional["es"]);

    const diasInhabiles = <?= json_encode($fechas_inhabiles) ?>;
    const diasCompletos = <?= json_encode($fechas_completas) ?>;
    const horasOcupadas = <?= json_encode($horas_ocupadas_por_fecha) ?>;
    const fechasInhabilitadas = diasInhabiles.concat(diasCompletos);
    const horasHoy = <?= json_encode($horas_disponibles) ?>;
    const hoy = new Date();

    function actualizarHoras(fecha) {
      const ocupadas = horasOcupadas[fecha] || [];
      const fechaSeleccionada = new Date(fecha + 'T00:00:00'); // Forzar medianoche local
      const ahora = new Date();
      $('.hora-btn').each(function () {
        const hora = $(this).data('hour');
        const [h, m] = hora.split(':');
        // Crear un objeto Date con la fecha seleccionada y la hora del botón
        const horaCompleta = new Date(fechaSeleccionada);
        horaCompleta.setHours(parseInt(h), parseInt(m), 0, 0);
        // Si la fecha es hoy y la hora ya pasó, deshabilitar
        const esHoy = fechaSeleccionada.toDateString() === ahora.toDateString();
        const esPasada = esHoy && horaCompleta.getTime() <= ahora.getTime();
        if (ocupadas.includes(hora) || esPasada) {
          $(this).addClass('disabled').prop('disabled', true);
        } else {
          $(this).removeClass('disabled').prop('disabled', false);
        }
      });
    }

    $("#fecha_cita").datepicker({
      minDate: hoy,
      maxDate: new Date(hoy.getFullYear(), 11, 31),
      changeMonth: true,
      changeYear: false,
      yearRange: `${hoy.getFullYear()}:${hoy.getFullYear()}`,
      dateFormat: "yy-mm-dd",
      beforeShowDay: function (date) {
        const d = date.toISOString().split('T')[0];
        const esFinDeSemana = (date.getDay() === 0 || date.getDay() === 6);
        const esInhabil = fechasInhabilitadas.includes(d);

        if (esFinDeSemana || esInhabil) {
          return [false, ""];
        }

        const ahora = new Date();
        const esHoy = date.toDateString() === ahora.toDateString();

        if (esHoy) {
          const ocupadas = horasOcupadas[d] || [];
          const quedanHorasDisponibles = horasHoy.some(hora => {
            if (ocupadas.includes(hora)) return false;

            const [h, m] = hora.split(':');
            const horaDate = new Date(date);
            horaDate.setHours(parseInt(h), parseInt(m), 0, 0);

            return horaDate > ahora;
          });

          return [quedanHorasDisponibles, ""];
        }

        return [true, ""];
      },
      onSelect: function (fecha) {
        actualizarHoras(fecha);
      }
    });

    const fechaInput = $('#fecha_cita').val();
    if (fechaInput) {
      actualizarHoras(fechaInput);
    }

    $('#hora_grid').on('click', '.hora-btn:not(.disabled)', function () {
      $('#hora_cita').val($(this).data('hour'));
      $('#idhora').val($(this).data('id'));
      $('.hora-btn').removeClass('selected');
      $(this).addClass('selected');
    });

    $('#formularioCita').on('submit', function (e) {
      const tel = $('input[name="telefono"]').val().trim();
      const email = $('input[name="correo"]').val().trim();
      const hora = $('#hora_cita').val().trim();

      if (!tel && !email) {
        e.preventDefault();
        $('#errorMensaje').show();
      } else if (!hora) {
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
