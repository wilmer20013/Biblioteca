<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Autores - Biblioteca</title>
</head>
<body>
    <?php
    session_start();
    require_once '../models/Autor.php';

    // Verificar login y rol admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
        header('Location: /biblioteca/views/login.php');
        exit;
    }

    $user = $_SESSION['user'];
    $autores = Autor::obtenerTodos();
    ?>

    <nav>
        <div>
            <a href="/biblioteca/views/dashboard.php">Biblioteca</a>
            <div>
                <ul>
                    <li><a href="/biblioteca/views/libros.php">Libros</a></li>
                    <li><a href="/biblioteca/views/autores.php">Autores</a></li>
                    <li><a href="/biblioteca/views/prestamos.php">Préstamos</a></li>
                </ul>
                <ul>
                    <li><span>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?> (Admin)</span></li>
                    <li><a href="/biblioteca/controllers/AuthController.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div>
        <h2>Gestión de Autores</h2>
        
        <!-- Mensajes -->
        <?php if (isset($_SESSION['error'])): ?>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Formulario Crear/Editar -->
        <form action="/biblioteca/controllers/AutorController.php?action=crear" method="POST">
            <input type="hidden" name="id" id="id">
            <div>
                <div><input type="text" name="nombre" placeholder="Nombre" required></div>
                <div><input type="text" name="apellido" placeholder="Apellido" required></div>
                <div><input type="date" name="fecha_nacimiento"></div>
                <div><textarea name="biografia" placeholder="Biografía"></textarea></div>
                <div><button type="submit">Guardar</button></div>
            </div>
        </form>

        <!-- Tabla de Autores -->
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Nombre</th><th>Apellido</th><th>Fecha Nacimiento</th><th>Biografía</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($autores as $autor): ?>
                    <tr>
                        <td><?php echo $autor['id']; ?></td>
                        <td><?php echo $autor['nombre']; ?></td>
                        <td><?php echo $autor['apellido']; ?></td>
                        <td><?php echo $autor['fecha_nacimiento']; ?></td>
                        <td><?php echo substr($autor['biografia'], 0, 50) . '...'; ?></td>
                        <td>
                            <button onclick="editar(<?php echo $autor['id']; ?>, '<?php echo addslashes($autor['nombre']); ?>', '<?php echo addslashes($autor['apellido']); ?>', '<?php echo $autor['fecha_nacimiento']; ?>', '<?php echo addslashes($autor['biografia']); ?>')">Editar</button>
                            <a href="/biblioteca/controllers/AutorController.php?action=eliminar&id=<?php echo $autor['id']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editar(id, nombre, apellido, fecha, biografia) {
            document.getElementById('id').value = id;
            document.querySelector('input[name="nombre"]').value = nombre;
            document.querySelector('input[name="apellido"]').value = apellido;
            document.querySelector('input[name="fecha_nacimiento"]').value = fecha;
            document.querySelector('textarea[name="biografia"]').value = biografia;
            document.querySelector('form').action = '/biblioteca/controllers/AutorController.php?action=actualizar';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>