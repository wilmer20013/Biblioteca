<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca</title>
</head>
<body>
    <?php
    session_start();
    require_once '../config.php'; // Para conexiones a BD

    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    $user = $_SESSION['user'];
    $isAdmin = ($user['rol'] == 'admin');

    // Consultas rápidas para estadísticas (usando PDO)
    $pdo = Database::getConnection();

    // Número total de libros
    $stmt = $pdo->query("SELECT COUNT(*) FROM libros");
    $totalLibros = $stmt->fetchColumn();
    
    // Número total de autores
    $stmt = $pdo->query("SELECT COUNT(*) FROM autores");
    $totalAutores = $stmt->fetchColumn();
    
    // Número de préstamos activos
    $stmt = $pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado = 'activo'");
    $prestamosActivos = $stmt->fetchColumn();
    
    // Número total de usuarios
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $totalUsuarios = $stmt->fetchColumn();
    
    // Préstamos del usuario actual (si no es admin)
    if (!$isAdmin) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = ? AND estado = 'activo'");
        $stmt->execute([$user['id']]);
        $misPrestamosActivos = $stmt->fetchColumn();
    }
    ?>

    <!-- Barra de navegación -->
    <nav>
        <div>
            <a href="dashboard.php">Biblioteca</a>
            <div>
                <ul>
                     <?php if ($isAdmin): ?>
                        <li><a href="autores.php">Autores</a></li>
                    <li><a href="libros.php">Libros</a></li>
                    <?php endif; ?>
                    <li><a href="prestamos.php">Préstamos</a></li>
                     <?php if ($isAdmin): ?>
                     <li><a href="usuarios.php">Usuarios</a></li>
                <?php endif; ?>
                </ul>
                <ul>
                    <li><span>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?> (<?php echo $user['rol']; ?>)</span></li>
                    <li><a href="../controllers/AuthController.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div>
                
  <?php if (isset($_SESSION['error'])): ?>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <h1>Dashboard de Biblioteca</h1>
        <!-- Tarjetas de estadísticas -->
            
        <div>
            <?php if ($isAdmin): ?>
            <div>
                <div>
                    <div>
                        <h5>Total de Autores</h5>
                        <p><?php echo $totalAutores; ?></p>
                    </div>
                </div>
            </div>
            <div>
                <div>
                    <div>
                        <h5>Total de Libros</h5>
                        <p><?php echo $totalLibros; ?></p>
                    </div>
                </div>
            </div>
             <?php endif; ?>
            <div>
                <div>
                    <div>
                        <h5>Préstamos Activos</h5>
                        <p><?php echo $prestamosActivos; ?></p>
                        <?php if (!$isAdmin): ?>
                            <small>Tus préstamos activos: <?php echo $misPrestamosActivos; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if ($isAdmin): ?>
                <div>
                    <div>
                        <div>
                            <h5>Total de Usuarios</h5>
                            <p><?php echo $totalUsuarios; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Secciones de navegación -->
        <div>
            <?php if ($isAdmin): ?>
            <div>
                <div>
                    <div>Gestión de Libros</div>
                    <div>
                        <p>Administra el catálogo de libros: agregar, editar y eliminar.</p>
                        <a href="libros.php">Ir a Libros</a>
                    </div>
                </div>
            </div>
                <div>
                    <div>
                        <div>Gestión de Autores</div>
                        <div>
                            <p>Administra autores: crear, actualizar y eliminar perfiles.</p>
                            <a href="autores.php">Ir a Autores</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <div>
                <div>
                    <div>Préstamos</div>
                    <div>
                        <p><?php echo $isAdmin ? 'Visualiza y gestiona todos los préstamos.' : 'Solicita préstamos y devuelve libros.'; ?></p>
                        <a href="prestamos.php">Ir a Préstamos</a>
                    </div>
                </div>
            </div>
            <?php if ($isAdmin): ?>
                <div>
                    <div>
                        <div>Gestión de Usuarios</div>
                        <div>
                            <p>Administra usuarios: crear, actualizar y eliminar cuentas.</p>
                            <a href="usuarios.php">Ir a Usuarios</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>