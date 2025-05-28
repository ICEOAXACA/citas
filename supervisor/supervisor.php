<?php
session_start();
if (!isset($_SESSION['roles']) || $_SESSION['roles'] != '2') {
    session_destroy();
    header("location:../php/log.php");
    exit();
}
require_once '../php/c.php';
$usuario = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$idUsuario = '';
$nombreCompleto = '';

// Si no hay id en sesión, buscarlo por username
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
        <!-- DEBUG temporal para mostrar valores -->
        <div style="background:#ffeeba;color:#856404;padding:8px 16px;margin-bottom:10px;border-radius:6px;">
            <strong>Debug:</strong> Usuario sesión: <b><?php echo htmlspecialchars($usuario); ?></b> | Nombre completo: <b><?php echo htmlspecialchars($nombreCompleto); ?></b>
        </div>
        <div id="vista-inicio">
            <div class="mb-4">
                <h2 class="fw-bold">Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?> 👋</h2>
                <p class="text-muted">Este es tu panel de control.</p>
            </div>
            <div class="dashboard-cards">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-person-check"></i>
                        <h5 class="card-title mt-2">Supervisores</h5>
                        <p class="card-text fs-3">125</p>
                    </div>
                </div>
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-person-check"></i>
                        <h5 class="card-title mt-2">Usuarios Atencion</h5>
                        <p class="card-text fs-3">58</p>
                    </div>
                </div>
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="bi bi-person-check"></i>
                        <h5 class="card-title mt-2">Citas</h5>
                        <p class="card-text fs-3">350</p>
                    </div>
                </div>
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
                    <button class="btn btn-secondary">Guardar cambios</button>
                </div>
            </div>
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
        document.getElementById('btn-inicio').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarVista('vista-inicio');
            activarMenu(this);
        });
        document.getElementById('btn-usuarios').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarVista('vista-usuarios');
            activarMenu(this);
        });
        document.getElementById('btn-reportes').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarVista('vista-reportes');
            activarMenu(this);
        });
        document.getElementById('btn-config').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarVista('vista-config');
            activarMenu(this);
        });
        function mostrarVista(id) {
            document.getElementById('vista-inicio').style.display = 'none';
            document.getElementById('vista-usuarios').style.display = 'none';
            document.getElementById('vista-reportes').style.display = 'none';
            document.getElementById('vista-config').style.display = 'none';
            document.getElementById(id).style.display = 'block';
        }
        function activarMenu(elemento) {
            document.querySelectorAll('.sidebar-links a').forEach(a => a.classList.remove('active'));
            elemento.classList.add('active');
        }
    </script>

</body>
</html>
