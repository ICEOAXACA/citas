<!doctype html>
<html lang="es" data-bs-theme="auto">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Usuarios ICEO</title>

  <link rel="icon" type="image/png" href="../Imagenes/favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <style>
    body {
      background: url('Imagenes/Fondo.jpeg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      margin: 0;
    }

    .form-card {
      background-color: rgba(255, 255, 255, 0.95); /* fondo semitransparente para mayor legibilidad */
      padding: 2rem;
      border-radius: 0.5rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .btn-institucional {
      background-color: #861f41;
      color: white;
    }

    .btn-institucional:hover {
      background-color: #6e1b37;
    }

    .titulo-principal {
      font-weight: bold;
      font-size: 1.3rem;
      color: #343a40;
    }

    .subtitulo {
      font-size: 1rem;
      color: #555;
    }
  </style>
</head>

<body class="d-flex flex-column justify-content-center align-items-center">

  <!-- Botón para registrar cita -->
  <a href="index.php" class="btn btn-success position-absolute top-0 end-0 m-3">Registrar una cita</a>

  <!-- Formulario -->
  <main class="form-card w-100 m-3" style="max-width: 400px;">
    <div class="text-center mb-4">
      <img src="Imagenes/EscudoOaxaca.png" alt="Escudo de Oaxaca" width="60" class="mb-2">
      <div class="titulo-principal">Instituto Catastral del Estado de Oaxaca (ICEO)</div>
      <div class="subtitulo">Gobierno del Estado de Oaxaca</div>
    </div>

    <form action="php/log.php" method="post">
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingInput" placeholder="Nombre de Usuario" name="usuario">
        <label for="floatingInput">Usuario</label>
      </div>

      <div class="form-floating mb-4">
        <input type="password" class="form-control" id="floatingPassword" placeholder="Contraseña" name="pass">
        <label for="floatingPassword">Contraseña</label>
      </div>

      <button class="btn btn-institucional w-100 py-2" type="submit">Ingresar</button>
    </form>
  </main>

</body>

</html>
