<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros - Biblioteca</title>
</head>
<body>
    <?php
    session_start();
    require_once '../models/Libro.php';
    require_once '../models/Autor.php';

    // Verificar login
    if (!isset($_SESSION['user'])) {
        header('Location: /biblioteca/views/login.php');
        exit;
    }

    $user = $_SESSION['user'];
    $libros = Libro::obtenerTodos();
    $autores = Autor::obtenerTodos(); // Para el dropdown en el formulario
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
                     <?php if ($user['rol'] == 'admin'): ?>
                    <li><a href="/biblioteca/views/usuarios.php">Usuarios</a></li>
                <?php endif; ?>
                </ul>
                <ul>
                    <li><span>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></span></li>
                    <li><a href="/biblioteca/controllers/AuthController.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div>
        <h2>Gestión de Libros</h2>
        
        <!-- Mensajes -->
        <?php if (isset($_SESSION['error'])): ?>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Botón para abrir el modal (Crear) -->
        <button type="button" onclick="resetModal()">
            Crear Libro
        </button>

        <!-- Modal -->
        <div id="modalLibro" style="display: none;">
            <div>
                <div>
                    <div>
                        <h5 id="modalLibroLabel">Crear/Editar Libro</h5>
                        <button type="button" onclick="closeModal()">Close</button>
                    </div>
                    <div>

                        <!-- Formulario de editar  -->
                        <form id="formLibro" action="/biblioteca/controllers/LibroController.php?action=crear" method="POST">
                            <input type="hidden" name="id" id="id">
                            
                            <div>
                                <label for="titulo">Título</label>
                                <input type="text" name="titulo" id="titulo" placeholder="Título" required>
                            </div>
                            
                            <div>
                                <label for="autor_id">Autor</label>
                                <select name="autor_id" id="autor_id" required>
                                    <option value="">Seleccionar Autor</option>
                                    <?php foreach ($autores as $autor): ?>
                                        <option value="<?php echo $autor['id']; ?>"><?php echo $autor['nombre'] . ' ' . $autor['apellido']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="isbn">ISBN</label>
                                <input type="text" name="isbn" id="isbn" placeholder="ISBN" required>
                            </div>
                            
                            <div>
                                <label for="genero">Género</label>
                                <input type="text" name="genero" id="genero" placeholder="Género">
                            </div>
                            
                            <div>
                                <label for="cantidad_disponible">Cantidad Disponible</label>
                                <input type="number" name="cantidad_disponible" id="cantidad_disponible" placeholder="Cantidad" required>
                            </div>
                        </form>
                    </div>
                    <div>
                        <button type="button" onclick="closeModal()">Cerrar</button>
                        <button type="submit" form="formLibro">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Libros -->
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Título</th><th>Autor</th><th>ISBN</th><th>Género</th><th>Cantidad</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($libros as $libro): ?>
                    <tr>
                        <td><?php echo $libro['id']; ?></td>
                        <td><?php echo $libro['titulo']; ?></td>
                        <td><?php echo $libro['nombre'] . ' ' . $libro['apellido']; ?></td>
                        <td><?php echo $libro['isbn']; ?></td>
                        <td><?php echo $libro['genero']; ?></td>
                        <td><?php echo $libro['cantidad_disponible']; ?></td>
                        <td>
                            <button onclick="editar(<?php echo $libro['id']; ?>, '<?php echo addslashes($libro['titulo']); ?>', <?php echo $libro['autor_id']; ?>, '<?php echo $libro['isbn']; ?>', '<?php echo $libro['genero']; ?>', <?php echo $libro['cantidad_disponible']; ?>)">Editar</button>
                            <a href="/biblioteca/controllers/LibroController.php?action=eliminar&id=<?php echo $libro['id']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function resetModal() {
            // Resetear el formulario para "crear" (limpia campos y cambia acción)
            document.getElementById('formLibro').action = '/biblioteca/controllers/LibroController.php?action=crear';
            document.getElementById('id').value = '';
            document.getElementById('titulo').value = '';
            document.getElementById('autor_id').value = '';
            document.getElementById('isbn').value = '';
            document.getElementById('genero').value = '';
            document.getElementById('cantidad_disponible').value = '';
            document.getElementById('modalLibroLabel').textContent = 'Crear Libro';
            document.getElementById('modalLibro').style.display = 'block';
        }

        function editar(id, titulo, autor_id, isbn, genero, cantidad) {
            // Poblar campos para editar y cambiar acción
            document.getElementById('formLibro').action = '/biblioteca/controllers/LibroController.php?action=actualizar';
            document.getElementById('id').value = id;
            document.getElementById('titulo').value = titulo;
            document.getElementById('autor_id').value = autor_id;
            document.getElementById('isbn').value = isbn;
            document.getElementById('genero').value = genero;
            document.getElementById('cantidad_disponible').value = cantidad;
            document.getElementById('modalLibroLabel').textContent = 'Editar Libro';
            
            // Abrir el modal
            document.getElementById('modalLibro').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalLibro').style.display = 'none';
        }
    </script>
</body>
</html>