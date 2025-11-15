<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Biblioteca</title>
</head>
<body>
    <div>
        <h2>Registro de Usuario</h2>
        
        <!-- Mostrar mensajes de error o success -->
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div>' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div>' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <!-- Formulario de registro -->
        <form action="../controllers/AuthController.php?action=registrar" method="POST" id="registerForm">
            <div>
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ingresa tu nombre">
            </div>
            <div>
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required placeholder="ejemplo@email.com">
            </div>
            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Mínimo 8 caracteres">
            </div>
            <div>
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repite la contraseña">
            </div>
            <button type="submit">Registrarse</button>
        </form>
        
        <div>
            <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>

    <!-- JavaScript para validación básica -->
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden.');
                e.preventDefault();
            }
            if (password.length < 8) {
                alert('La contraseña debe tener al menos 8 caracteres.');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>