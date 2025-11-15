<?php
require_once '../models/Usuario.php';
session_start();

// Verificar login
if (!isset($_SESSION['user'])) {
    header('Location: /biblioteca/views/login.php');
    exit;
}

// Verificar que sea admin
$user = $_SESSION['user'];
if ($user['rol'] !== 'admin') {
    header('Location: /biblioteca/views/dashboard.php');
    exit;
}

class UsuarioController {
    // Método: Manejar creación
    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $rol = $_POST['rol'];
            
            $resultado = Usuario::registrarConValidacion($nombre, $email, $password, $confirmPassword, $rol);
            if ($resultado === true) {
                // Asignar rol si es necesario (asumiendo que registrarConValidacion no maneja rol, o ajusta)
                // Si registrarConValidacion no incluye rol, necesitarías un método separado o actualizar después
                // Para simplicidad, asumamos que agregamos un método registrarConRol en Usuario
                // Aquí lo llamo directamente; ajusta según tu implementación
                if (Usuario::registrarConValidacion($nombre, $email, $password, $confirmPassword, $rol)) {
                    $_SESSION['success'] = "Usuario creado exitosamente.";
                } else {
                    $_SESSION['error'] = "Error al crear usuario.";
                }
            } else {
                $_SESSION['error'] = $resultado; // Mensaje de error de validación
            }
            header('Location: /biblioteca/views/usuarios.php');
            exit;
        }
    }

    // Método: Manejar actualización
    public static function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $rol = $_POST['rol'];
            
            // Validaciones básicas
            if (empty($nombre) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Nombre y email válidos son obligatorios.";
                header('Location: /biblioteca/views/usuarios.php');
                exit;
            }
            
            // Si password no está vacío, validar
            if (!empty($password)) {
                if (strlen($password) < 8 || $password !== $confirmPassword) {
                    $_SESSION['error'] = "Contraseña inválida o no coincide.";
                    header('Location: /biblioteca/views/usuarios.php');
                    exit;
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $hashedPassword = null; // No cambiar password
            }
            
            if (Usuario::actualizar($id, $nombre, $email, $hashedPassword, $rol)) {
                $_SESSION['success'] = "Usuario actualizado.";
            } else {
                $_SESSION['error'] = "Error al actualizar.";
            }
            header('Location: /biblioteca/views/usuarios.php');
            exit;
        }
    }

    // Método: Manejar eliminación
    public static function eliminar() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            // Evitar que un admin se elimine a sí mismo
            if ($id == $user['id']) {
                $_SESSION['error'] = "No puedes eliminarte a ti mismo.";
                header('Location: /biblioteca/views/usuarios.php');
                exit;
            }
            if (Usuario::eliminar($id)) {
                $_SESSION['success'] = "Usuario eliminado.";
            } else {
                $_SESSION['error'] = "Error al eliminar.";
            }
            header('Location: /biblioteca/views/usuarios.php');
            exit;
        } 
    }
}

// Enrutador
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'crear') UsuarioController::crear();
    elseif ($action == 'actualizar') UsuarioController::actualizar();
    elseif ($action == 'eliminar') UsuarioController::eliminar();
}
?>