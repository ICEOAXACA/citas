<?php
// Inicio la sesión y valido que el usuario sea administrador (rol 1)
session_start();
if (!isset($_SESSION['roles']) || $_SESSION['roles'] != '1') {
    session_destroy();
    header("location:../php/log.php");
    exit();
}
// Incluyo la conexión a la base de datos
require_once '../php/c.php';
// Obtengo el nombre de usuario de la sesión
$usuario = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$idUsuario = '';
$nombreCompleto = '';

// Si no hay id en sesión, lo busco por username
if (empty($_SESSION['id']) && !empty($usuario)) {
    $sql = "SELECT id FROM usuarios WHERE usuario = $1 LIMIT 1";
    $result = pg_query_params($conexion, $sql, array($usuario));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $_SESSION['id'] = $row['id'];
        $idUsuario = $row['id'];
    }
} else {
    $idUsuario = isset($_SESSION['id']) ? $_SESSION['id'] : '';
}

// Obtengo el nombre completo del usuario para mostrarlo en el dashboard
if (!empty($idUsuario)) {
    $sql = "SELECT nombre FROM usuarios WHERE id = $1 LIMIT 1";
    $result = pg_query_params($conexion, $sql, array($idUsuario));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $nombreCompleto = $row['nombre'];
    } else {
        $nombreCompleto = $usuario;
    }
} else {
    $nombreCompleto = $usuario;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../Imagenes/favicon.ico">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .top-bar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1065;
        }

        .top-bar img {
            height: 50px;
            max-width: 100%;
            z-index: 1065;
        }

        .sidebar {
            background-color: #343a40;
            color: #fff;
            height: calc(100% - 56px);
            top: 56px;
            transition: left 0.3s;
            z-index: 1055;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.3);
        }

        .sidebar-links {
            margin-top: 30px;
        }

        .sidebar-links a {
            display: block;
            padding: 0.75rem 1.25rem;
            color: #adb5bd;
            text-decoration: none;
            margin-bottom: 12px;
        }

        .sidebar-links a:hover,
        .sidebar-links a.active {
            background-color: #495057;
            color: #fff;
        }

        .sidebar-links a i {
            margin-right: 10px;
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                left: 0;
                width: 220px;
                padding-top: 1rem;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                left: -100%;
                width: 220px;
                background-color: #343a40;
                padding-top: 56px;
                transition: left 0.3s;
                z-index: 2000;
            }

            .sidebar.open {
                left: 0;
            }

            .sidebar-links {
                padding-top: 40px;
                margin-top: 0;
            }

            .top-bar {
                padding-left: 60px;
            }

            .top-bar img {
                position: relative;
                z-index: 1065;
            }

            #backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.3);
                z-index: 1999;
            }

            .sidebar.open + #backdrop {
                display: block;
            }
        }

        .main {
            padding: 90px 20px 20px 20px;
            transition: margin-left 0.3s;
        }

        @media (min-width: 992px) {
            .main {
                margin-left: 220px;
            }
        }

        @media (max-width: 991.98px) {
            .main {
                margin-left: 0;
                padding-top: 100px;
            }
        }

        .dashboard-cards {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            flex: 1 1 280px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card-body i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .menu-toggle {
            display: none;
        }

        #backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.3);
            z-index: 1050;
        }

        @media (max-width: 991.98px) {
            .menu-toggle {
                display: block;
                position: fixed;
                top: 70px; /* Despegado del logo */
                left: 15px;
                background-color: #fff;
                border: 1px solid #dee2e6;
                padding: 8px;
                border-radius: 6px;
                z-index: 1066;
                cursor: pointer;
                box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            }
        }
    </style>
</head>
<body>

    <!-- Botón hamburguesa -->
    <div class="menu-toggle" id="menuToggle">
        <i class="bi bi-list fs-3"></i>
    </div>

    <!-- Fondo oscuro cuando el menú está abierto en móviles -->
    <div id="backdrop"></div>

    <!-- Barra superior -->
    <div class="top-bar">
        <img src="../Imagenes/logo1.png" alt="Logo ICEO">
        <div class="ms-auto d-flex flex-column align-items-end">
            <span class="fw-semibold fs-5 text-dark">Panel de Administración - ICEO</span>
            <a href="../php/cerrar.php" class="text-danger mt-1" style="font-size: 1rem;"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebarMenu">
        <div class="sidebar-links">
            <a href="#" class="active" id="btn-inicio"><i class="bi bi-house-door"></i>Inicio</a>
            <a href="#" id="btn-usuarios"><i class="bi bi-people"></i>Usuarios</a>
            <a href="#" id="btn-reportes"><i class="bi bi-bar-chart-line"></i>Reportes</a>
            <a href="#" id="btn-config"><i class="bi bi-gear"></i>Configuraciones</a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="main" id="main-content">

        <div id="vista-inicio">
            <div class="mb-4">
                <h2 class="fw-bold">Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?> 👋</h2>
                <p class="text-muted">Este es tu panel de control.</p>
            </div>
            <div class="dashboard-cards">
                <div class="card bg-primary text-white" id="card-citas">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check"></i>
                        <h5 class="card-title mt-2">Citas</h5>
                        <p class="card-text fs-3">125</p>
                    </div>
                </div>
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-building"></i>
                        <h5 class="card-title mt-2">Departamentos</h5>
                        <p class="card-text fs-3">58</p>
                    </div>
                </div>
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-x"></i>
                        <h5 class="card-title mt-2">Dias Inhabiles</h5>
                        <p class="card-text fs-3">350</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="vista-citas-tipos" style="display:none;">
            <div class="mb-4">
                <button class="btn btn-link" id="btn-volver-inicio"><i class="bi bi-arrow-left"></i> Volver</button>
                <h3 class="fw-bold">Tipos de Citas</h3>
            </div>
            <div class="dashboard-cards" id="cards-tipos-citas">
                <!-- Cards dinámicas -->
            </div>
        </div>
        <div id="vista-usuarios" style="display:none;">
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><i class="bi bi-people"></i> Gestión de Usuarios</div>
                
                <div class="card-body">
                    <p>Aquí puedes administrar los usuarios del sistema.</p>
                    <button class="btn btn-primary">Agregar usuario</button>
                </div>
                
            </div>
        </div>
        <div id="vista-reportes" style="display:none;">
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><i class="bi bi-bar-chart-line"></i> Reportes</div>
                <div class="card-body">
                    <p>Visualiza y genera reportes de actividad.</p>
                    <button class="btn btn-info text-white">Generar reporte</button>
                </div>
            </div>
        </div>
        <div id="vista-config" style="display:none;">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white"><i class="bi bi-gear"></i> Configuración</div>
                <div class="card-body">
                    <p>Configura las opciones del sistema aquí.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mostrar tipos de citas:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="chk-juridico" checked>
                            <label class="form-check-label" for="chk-juridico">Citas Jurídico</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="chk-tramitacion" checked>
                            <label class="form-check-label" for="chk-tramitacion">Citas Tramitación</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="chk-cartografia" checked>
                            <label class="form-check-label" for="chk-cartografia">Citas Cartografía</label>
                        </div>
                    </div>
                    <button class="btn btn-secondary" id="btn-guardar-config">Guardar cambios</button>
                </div>
            </div>
        </div>
        <div id="vista-departamentos" style="display:none;">
            <div class="mb-4">
                <button class="btn btn-link" id="btn-volver-inicio-dep"><i class="bi bi-arrow-left"></i> Volver</button>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building"></i> Departamentos</span>
                    <button class="btn btn-sm btn-primary" id="btn-agregar-dep"><i class="bi bi-plus-circle"></i> Agregar</button>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT * FROM departamentos ORDER BY id";
                    $result = pg_query($conexion, $sql);
                    $campos = [];
                    if ($result && pg_num_rows($result) > 0) {
                        $num_fields = pg_num_fields($result);
                        for ($i = 0; $i < $num_fields; $i++) {
                            $fieldName = pg_field_name($result, $i);
                            $campos[] = $fieldName;
                        }
                        echo '<div class="table-responsive"><table class="table table-bordered table-hover" id="tabla-dep"><thead class="table-success"><tr>';
                        foreach ($campos as $fieldName) {
                            echo '<th>' . htmlspecialchars($fieldName) . '</th>';
                        }
                        echo '<th>Acciones</th>';
                        echo '</tr></thead><tbody>';
                        while ($row = pg_fetch_assoc($result)) {
                            echo '<tr>';
                            foreach ($campos as $fieldName) {
                                echo '<td>' . htmlspecialchars($row[$fieldName]) . '</td>';
                            }
                            // Botones de acción
                            echo '<td class="text-center">'
                                . '<button class="btn btn-sm btn-outline-secondary btn-editar-dep me-1" title="Editar"><i class="bi bi-pencil-square"></i></button>'
                                . '<button class="btn btn-sm btn-outline-danger btn-eliminar-dep" title="Eliminar"><i class="bi bi-trash"></i></button>'
                                . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table></div>';
                    } else {
                        // Si no hay registros, igual obtenemos los campos
                        $sql = "SELECT * FROM departamentos LIMIT 1";
                        $result = pg_query($conexion, $sql);
                        if ($result) {
                            $num_fields = pg_num_fields($result);
                            for ($i = 0; $i < $num_fields; $i++) {
                                $fieldName = pg_field_name($result, $i);
                                $campos[] = $fieldName;
                            }
                        }
                        echo '<div class="alert alert-warning">No hay departamentos registrados.</div>';
                    }
                    ?>
                </div>
            </div>
            <!-- Modal Agregar/Editar Departamento -->
            <div class="modal fade" id="modalDep" tabindex="-1" aria-labelledby="modalDepLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalDepLabel">Agregar Departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body">
                    <form id="formDep">
                      <?php
                      foreach ($campos as $campo) {
                        if ($campo === 'id') {
                          echo '<input type="hidden" id="dep-id">';
                          continue;
                        }
                        echo '<div class="mb-3">';
                        echo '<label for="dep-' . htmlspecialchars($campo) . '" class="form-label">' . htmlspecialchars(ucfirst($campo)) . '</label>';
                        echo '<input type="text" class="form-control" id="dep-' . htmlspecialchars($campo) . '" required>';
                        echo '</div>';
                      }
                      ?>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-guardar-dep">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal Confirmar Eliminación -->
            <div class="modal fade" id="modalEliminarDep" tabindex="-1" aria-labelledby="modalEliminarDepLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarDepLabel">Eliminar Departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este departamento?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-dep">Eliminar</button>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div id="vista-citas-juridico" style="display:none;">
            <div class="mb-4 d-flex align-items-center gap-3">
                <button class="btn btn-link" id="btn-volver-citas-juridico"><i class="bi bi-arrow-left"></i> Volver</button>
                <h3 class="fw-bold mb-0">Historial de Citas Jurídico</h3>
                <div class="ms-auto">
                    <label for="filtro-fecha-citas" class="form-label mb-0 me-2">Filtrar por fecha:</label>
                    <input type="date" id="filtro-fecha-citas" class="form-control d-inline-block" style="width:auto;" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div id="tabla-historial-citas-juridico-container"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const backdrop = document.getElementById('backdrop');

        menuToggle.addEventListener('click', () => {
            sidebarMenu.classList.toggle('open');
            backdrop.style.display = sidebarMenu.classList.contains('open') ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 991 && sidebarMenu.classList.contains('open')) {
                if (!sidebarMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebarMenu.classList.remove('open');
                    backdrop.style.display = 'none';
                }
            }
        });

        // Cambiar vistas al hacer click en el menú
        document.getElementById('btn-inicio').onclick = function(e) {
            e.preventDefault();
            mostrarVista('vista-inicio');
            activarMenu(this);
        };
        document.getElementById('btn-usuarios').onclick = function(e) {
            e.preventDefault();
            mostrarVista('vista-usuarios');
            activarMenu(this);
        };
        document.getElementById('btn-reportes').onclick = function(e) {
            e.preventDefault();
            mostrarVista('vista-reportes');
            activarMenu(this);
        };
        document.getElementById('btn-config').onclick = function(e) {
            e.preventDefault();
            mostrarVista('vista-config');
            activarMenu(this);
        };
        // Mostrar departamentos desde el dashboard
        var cardDepartamentos = document.querySelector('.dashboard-cards .card.bg-success');
        if(cardDepartamentos) {
            cardDepartamentos.onclick = function(e) {
                mostrarVista('vista-departamentos');
            };
        }
        // Mostrar tipos de citas al hacer click en la card de Citas
        var cardCitas = document.getElementById('card-citas');
        if(cardCitas) {
            cardCitas.onclick = function(e) {
                mostrarVista('vista-citas-tipos');
                renderizarCardsTiposCitas();
            };
        }
        // Botón volver a inicio desde tipos de citas
        document.addEventListener('click', function(e) {
            if(e.target && e.target.id === 'btn-volver-inicio') {
                mostrarVista('vista-inicio');
            }
        });
        // Botón volver a inicio desde departamentos
        document.addEventListener('click', function(e) {
            if(e.target && e.target.id === 'btn-volver-inicio-dep') {
                mostrarVista('vista-inicio');
            }
        });
        // Botón volver a citas jurídico
        document.addEventListener('click', function(e) {
            if(e.target && e.target.id === 'btn-volver-citas-juridico') {
                mostrarVista('vista-citas-tipos');
            }
        });
        function mostrarVista(id) {
            document.getElementById('vista-inicio').style.display = 'none';
            document.getElementById('vista-usuarios').style.display = 'none';
            document.getElementById('vista-reportes').style.display = 'none';
            document.getElementById('vista-config').style.display = 'none';
            document.getElementById('vista-departamentos').style.display = 'none';
            document.getElementById('vista-citas-tipos').style.display = 'none';
            document.getElementById('vista-citas-juridico').style.display = 'none';
            document.getElementById(id).style.display = 'block';
        }
        function activarMenu(elemento) {
            document.querySelectorAll('.sidebar-links a').forEach(a => a.classList.remove('active'));
            elemento.classList.add('active');
        }
        // Renderizar cards de tipos de citas según configuración
        function renderizarCardsTiposCitas() {
            var cards = [];
            var config = getConfigCitas();
            if(config.juridico) {
                cards.push(`<div class='card bg-primary text-white'><div class='card-body text-center'><i class='bi bi-briefcase'></i><h5 class='card-title mt-2'>Citas Jurídico</h5></div></div>`);
            }
            if(config.tramitacion) {
                cards.push(`<div class='card bg-info text-white'><div class='card-body text-center'><i class='bi bi-file-earmark-text'></i><h5 class='card-title mt-2'>Citas Tramitación</h5></div></div>`);
            }
            if(config.cartografia) {
                cards.push(`<div class='card bg-warning text-dark'><div class='card-body text-center'><i class='bi bi-geo-alt'></i><h5 class='card-title mt-2'>Citas Cartografía</h5></div></div>`);
            }
            document.getElementById('cards-tipos-citas').innerHTML = cards.join('');
        }
        // Configuración: guardar y cargar desde localStorage
        function getConfigCitas() {
            var config = localStorage.getItem('configCitas');
            if(config) return JSON.parse(config);
            return { juridico: true, tramitacion: true, cartografia: true };
        }
        function setConfigCitas(config) {
            localStorage.setItem('configCitas', JSON.stringify(config));
        }
        // Al abrir configuración, cargar estado
        document.getElementById('btn-config').addEventListener('click', function() {
            var config = getConfigCitas();
            document.getElementById('chk-juridico').checked = !!config.juridico;
            document.getElementById('chk-tramitacion').checked = !!config.tramitacion;
            document.getElementById('chk-cartografia').checked = !!config.cartografia;
        });
        // Guardar cambios de configuración
        document.getElementById('btn-guardar-config').onclick = function() {
            var config = {
                juridico: document.getElementById('chk-juridico').checked,
                tramitacion: document.getElementById('chk-tramitacion').checked,
                cartografia: document.getElementById('chk-cartografia').checked
            };
            setConfigCitas(config);
            alert('Configuración guardada.');
        };
        // --- DEPARTAMENTOS CRUD (funcional con backend, todos los campos) ---
        let depEditando = null;
        let depFilaEliminando = null;
        let depIdEliminando = null;
        const camposDep = <?php echo json_encode($campos); ?>;

        function asignarEventosDepartamentos() {
            // Editar departamento
            document.querySelectorAll('.btn-editar-dep').forEach(btn => {
                btn.onclick = function() {
                    depEditando = this.closest('tr');
                    document.getElementById('modalDepLabel').textContent = 'Editar Departamento';
                    camposDep.forEach((campo, idx) => {
                        if(campo === 'id') document.getElementById('dep-id').value = depEditando.children[idx].textContent;
                        else document.getElementById('dep-' + campo).value = depEditando.children[idx].textContent;
                    });
                    var modal = new bootstrap.Modal(document.getElementById('modalDep'));
                    modal.show();
                };
            });
            // Eliminar departamento
            document.querySelectorAll('.btn-eliminar-dep').forEach(btn => {
                btn.onclick = function() {
                    depFilaEliminando = this.closest('tr');
                    depIdEliminando = depFilaEliminando.children[0].textContent;
                    var modal = new bootstrap.Modal(document.getElementById('modalEliminarDep'));
                    modal.show();
                };
            });
        }

        // Llamar al cargar la tabla
        asignarEventosDepartamentos();

        // Abrir modal para agregar
        if(document.getElementById('btn-agregar-dep')) {
            document.getElementById('btn-agregar-dep').onclick = function() {
                depEditando = null;
                document.getElementById('modalDepLabel').textContent = 'Agregar Departamento';
                camposDep.forEach(campo => {
                    if(campo === 'id') document.getElementById('dep-id').value = '';
                    else document.getElementById('dep-' + campo).value = '';
                });
                var modal = new bootstrap.Modal(document.getElementById('modalDep'));
                modal.show();
            };
        }
        // Cambia el evento del botón guardar para usar el evento submit del formulario
        const formDep = document.getElementById('formDep');
        if(formDep) {
            formDep.onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData();
                camposDep.forEach(campo => {
                    if(campo === 'id') formData.append('id', document.getElementById('dep-id').value);
                    else formData.append(campo, document.getElementById('dep-' + campo).value);
                });
                if(depEditando) {
                    formData.append('action', 'editar');
                } else {
                    formData.append('action', 'agregar');
                }
                fetch('../php/departamentos_crud.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        if(depEditando) {
                            camposDep.forEach((campo, idx) => {
                                depEditando.children[idx].textContent = data.departamento[campo];
                            });
                        } else {
                            const tabla = document.getElementById('tabla-dep').getElementsByTagName('tbody')[0];
                            const nuevaFila = tabla.insertRow();
                            camposDep.forEach(campo => {
                                nuevaFila.insertCell().textContent = data.departamento[campo];
                            });
                            const cellAcciones = nuevaFila.insertCell();
                            cellAcciones.className = 'text-center';
                            cellAcciones.innerHTML = '<button class="btn btn-sm btn-outline-secondary btn-editar-dep me-1" title="Editar"><i class="bi bi-pencil-square"></i></button>' +
                                '<button class="btn btn-sm btn-outline-danger btn-eliminar-dep" title="Eliminar"><i class="bi bi-trash"></i></button>';
                        }
                        // Siempre reasignar eventos tras agregar/editar
                        asignarEventosDepartamentos();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalDep'));
                        modal.hide();
                    } else {
                        alert(data.error || 'Error al guardar');
                    }
                })
                .catch(() => alert('Error de conexión'));
            };
        }
        // Corrige el botón guardar para que no haga submit por sí solo
        document.getElementById('btn-guardar-dep').onclick = function(e) {
            e.preventDefault();
            formDep.requestSubmit();
        };
        // Confirmar eliminar
        if(document.getElementById('btn-confirmar-eliminar-dep')) {
            document.getElementById('btn-confirmar-eliminar-dep').onclick = function() {
                if(!depIdEliminando) return;
                const formData = new FormData();
                formData.append('action', 'eliminar');
                formData.append('id', depIdEliminando);
                fetch('../php/departamentos_crud.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        if(depFilaEliminando) depFilaEliminando.remove();
                        depFilaEliminando = null;
                        depIdEliminando = null;
                        asignarEventosDepartamentos();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarDep'));
                        modal.hide();
                    } else {
                        alert(data.error || 'Error al eliminar');
                    }
                })
                .catch(() => alert('Error de conexión'));
            };
        }
        // Mostrar tabla historial_citas al hacer click en Citas Jurídico
        function mostrarVistaCitasJuridico() {
            mostrarVista('vista-citas-juridico');
            cargarHistorialCitasJuridico();
        }
        function cargarHistorialCitasJuridico() {
            const fecha = document.getElementById('filtro-fecha-citas').value;
            fetch('../php/historial_citas_juridico.php?fecha=' + encodeURIComponent(fecha))
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        renderTablaHistorialCitasJuridico(data.data);
                    } else {
                        document.getElementById('tabla-historial-citas-juridico-container').innerHTML = '<div class="alert alert-danger">Error al cargar datos</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('tabla-historial-citas-juridico-container').innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
                });
        }
        function renderTablaHistorialCitasJuridico(rows) {
            if(!rows || rows.length === 0) {
                document.getElementById('tabla-historial-citas-juridico-container').innerHTML = '<div class="alert alert-warning">No hay citas para la fecha seleccionada.</div>';
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-bordered table-hover"><thead class="table-primary"><tr>';
            // Encabezados dinámicos
            Object.keys(rows[0]).forEach(col => {
                html += '<th>' + col + '</th>';
            });
            html += '</tr></thead><tbody>';
            rows.forEach(row => {
                html += '<tr>';
                Object.values(row).forEach(cell => {
                    html += '<td>' + (cell !== null ? cell : '') + '</td>';
                });
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            document.getElementById('tabla-historial-citas-juridico-container').innerHTML = html;
        }
        // Evento para filtrar por fecha
        if(document.getElementById('filtro-fecha-citas')) {
            document.getElementById('filtro-fecha-citas').addEventListener('change', cargarHistorialCitasJuridico);
        }
        // Botón volver
        if(document.getElementById('btn-volver-citas-juridico')) {
            document.getElementById('btn-volver-citas-juridico').onclick = function() {
                mostrarVista('vista-citas-tipos');
            };
        }
        // Hook para mostrar la tabla al hacer click en Citas Jurídico
        function hookCitasJuridicoCard() {
            setTimeout(() => {
                const cards = document.querySelectorAll('#cards-tipos-citas .card');
                cards.forEach(card => {
                    if(card.textContent.includes('Jurídico')) {
                        card.onclick = mostrarVistaCitasJuridico;
                    }
                });
            }, 300);
        }
        // Llama hook cada vez que se renderizan los cards de tipos de citas
        const origRenderizarCardsTiposCitas = renderizarCardsTiposCitas;
        renderizarCardsTiposCitas = function() {
            origRenderizarCardsTiposCitas();
            hookCitasJuridicoCard();
        };
    </script>

</body>
</html>
