<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos - Biblioteca</title>
</head>
<body>
    <?php
    session_start();
    require_once '../models/Prestamo.php';
    require_once '../models/Libro.php';



    $user = $_SESSION['user'];
    $prestamos = Prestamo::obtenerTodos($user['rol'] == 'admin' ? null : $user['id']);
    $libros = Libro::obtenerTodos(); // Para dropdown de préstamo
    ?>

    <nav>
        <div>
            <a href="/biblioteca/views/dashboard.php">Biblioteca</a>
            <div>
                <ul>
                      <?php if ($user['rol'] == 'admin'): ?>
                    <li><a href="/biblioteca/views/libros.php">Libros</a></li>
                  
                        <li><a href="/biblioteca/views/autores.php">Autores</a></li>
                    <?php endif; ?>
                    <li><a href="/biblioteca/views/prestamos.php">Préstamos</a></li>
                </ul>
                <ul>
                    <li><span>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></span></li>
                    <li><a href="/biblioteca/controllers/AuthController.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div>
        <h2>Préstamos</h2>
        
        <!-- Mensajes -->
        <?php if (isset($_SESSION['error'])): ?>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Formulario para Prestar (solo si hay libros disponibles) -->
        <form action="/biblioteca/controllers/PrestamoController.php?action=crear" method="POST">
            <div>
                <div>
                    <select name="libro_id" required>
                        <option value="">Seleccionar Libro</option>
                        <?php foreach ($libros as $libro): if ($libro['cantidad_disponible'] > 0): ?>
                            <option value="<?php echo $libro['id']; ?>"><?php echo $libro['titulo']; ?> (Disponible: <?php echo $libro['cantidad_disponible']; ?>)</option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>
                <div><input type="date" name="fecha_devolucion_estimada" required></div>
                <div><button type="submit">Prestar</button></div>
            </div>
        </form>

        <!-- Tabla de Préstamos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Usuario</th><th>Libro</th><th>Fecha Préstamo</th><th>Fecha Estimada</th><th>Estado</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?php echo $prestamo['id']; ?></td>
                        <td><?php echo $prestamo['usuario_nombre']; ?></td>
                        <td><?php echo $prestamo['libro_titulo']; ?></td>
                        <td><?php echo $prestamo['fecha_prestamo']; ?></td>
                        <td><?php echo $prestamo['fecha_devolucion_estimada']; ?></td>
                        <td><?php echo $prestamo['estado']; ?></td>
                        <td>
                            <?php if ($prestamo['estado'] == 'activo'): ?>
                                <a href="/biblioteca/controllers/PrestamoController.php?action=devolver&id=<?php echo $prestamo['id']; ?>" onclick="return confirm('¿Devolver libro?')">Devolver</a>
                            <?php else: ?>
                                <span>Devuelto</span>
                                <?php if ($user['rol'] == 'admin'): ?>
                                    <small>Multa: $<?php echo Prestamo::calcularMulta($prestamo['id']); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>