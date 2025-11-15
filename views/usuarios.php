<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Biblioteca</title>
</head>
<body>
    <?php
    session_start();
    require_once '../models/Usuario.php';

    // Verificar login
    if (!isset($_SESSION['user'])) {
        header('Location: /biblioteca/views/login.php');
        exit;
    }

    $user = $_SESSION['user'];

    // Solo admin puede gestionar usuarios
    if ($user['rol'] !== 'admin') {
        header('Location: /biblioteca/views/dashboard.php');
        exit;
    }

    $usuarios = Usuario::obtenerTodos();
    ?>

    <nav>
        <div>
            <a href="/biblioteca/views/dashboard.php">Biblioteca</a>
            <div>
                <ul>
                    <li><a href="/biblioteca/views/libros.php">Libros</a></li>
                    <li><a href="/biblioteca/views/autores.php">Autores</a></li>
                    <li><a href="/biblioteca/views/usuarios.php">Usuarios</a></li>
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
        <h2>Gestión de Usuarios</h2>
        
        <!-- Mensajes -->
        <?php if (isset($_SESSION['error'])): ?>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Formulario Crear/Editar -->
        <form action="/biblioteca/controllers/UsuarioController.php?action=crear" method="POST">
            <input type="hidden" name="id" id="id">
            <div>
                <div><input type="text" name="nombre" placeholder="Nombre" required></div>
                <div><input type="email" name="email" placeholder="Email" required></div>
                <div><input type="password" name="password" placeholder="Contraseña" required></div>
                <div><input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required></div>
                <div>
                    <select name="rol" required>
                        <option value="usuario">Usuario</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div><button type="submit">Guardar</button></div>
            </div>
        </form>

        <!-- Tabla de Usuarios -->
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                        <td>
                            <button onclick="editar(<?php echo $usuario['id']; ?>, '<?php echo addslashes($usuario['nombre']); ?>', '<?php echo addslashes($usuario['email']); ?>', '<?php echo addslashes($usuario['rol']); ?>')">Editar</button>
                            <a href="/biblioteca/controllers/UsuarioController.php?action=eliminar&id=<?php echo $usuario['id']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editar(id, nombre, email, rol) {
            document.getElementById('id').value = id;
            document.querySelector('input[name="nombre"]').value = nombre;
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('select[name="rol"]').value = rol;
            // Para editar, hacer password no requerido (vacío significa no cambiar)
            document.querySelector('input[name="password"]').required = false;
            document.querySelector('input[name="confirm_password"]').required = false;
            document.querySelector('input[name="password"]').placeholder = "Dejar vacío para no cambiar";
            document.querySelector('input[name="confirm_password"]').placeholder = "Dejar vacío para no cambiar";
            document.querySelector('form').action = '/biblioteca/controllers/UsuarioController.php?action=actualizar';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>