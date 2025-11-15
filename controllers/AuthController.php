<?php
require_once '../models/Usuario.php'; // Ajusta la ruta si es necesario
session_start();

class AuthController {
    // Método: Manejar registro (con validaciones adicionales en el controlador)
    public static function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $rol = isset($_POST['rol']) ? $_POST['rol'] : 'usuario'; // Permitir rol si se pasa (para admins)

            // Validaciones básicas en el controlador (antes de llamar al modelo)
            if (empty($nombre) || empty($email) || empty($password) || empty($confirmPassword)) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                header('Location: ../views/register.php'); // Cambia a ruta relativa si es necesario
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "El email no es válido.";
                header('Location: ../views/register.php');
                exit;
            }
            if (strlen($password) < 8) {
                $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
                header('Location: ../views/register.php');
                exit;
            }
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = "Las contraseñas no coinciden.";
                header('Location: ../views/register.php');
                exit;
            }

            // Llamar al modelo con validaciones adicionales (ej. email único)
            $resultado = Usuario::registrarConValidacion($nombre, $email, $password, $confirmPassword, $rol);
            if ($resultado === true) {
                $_SESSION['success'] = "Registro exitoso. Inicia sesión.";
                header('Location: ../views/login.php'); // Ruta relativa
            } else {
                $_SESSION['error'] = $resultado; // Mensaje de error del modelo
                header('Location: ../views/register.php');
            }
            exit;
        }
    }

    // Método: Manejar login (agregué validaciones faltantes)
    public static function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validaciones básicas en el controlador
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Email y contraseña son obligatorios.";
                header('Location: ../views/login.php');
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "El email no es válido.";
                header('Location: ../views/login.php');
                exit;
            }

            $user = Usuario::login($email, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                $_SESSION['success'] = "Login exitoso."; // Agregué mensaje de éxito
                header('Location: ../views/dashboard.php'); // Ruta relativa
            } else {
                $_SESSION['error'] = "Credenciales inválidas.";
                header('Location: ../views/login.php');
            }
            exit;
        }
    }

    // Método: Logout (sin cambios)
    public static function logout() {
        session_destroy();
        header('Location: ../index.php'); // Ruta relativa
        exit;
    }

    // Método: Crear usuario (para admins, con validaciones)
    public static function crearUsuario() {
        // Verificar que sea admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
            header('Location: ../views/dashboard.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $rol = $_POST['rol'];

            // Validaciones básicas
            if (empty($nombre) || empty($email) || empty($password) || empty($confirmPassword)) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                header('Location: ../views/usuarios.php');
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "El email no es válido.";
                header('Location: ../views/usuarios.php');
                exit;
            }
            if (strlen($password) < 8 || $password !== $confirmPassword) {
                $_SESSION['error'] = "Contraseña inválida o no coincide.";
                header('Location: ../views/usuarios.php');
                exit;
            }

            $resultado = Usuario::registrarConValidacion($nombre, $email, $password, $confirmPassword, $rol);
            if ($resultado === true) {
                $_SESSION['success'] = "Usuario creado exitosamente.";
            } else {
                $_SESSION['error'] = $resultado;
            }
            header('Location: ../views/usuarios.php');
            exit;
        }
    }

    // Método: Actualizar usuario (para admins, sin cambios mayores)
    public static function actualizarUsuario() {
        // Verificar que sea admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
            header('Location: ../views/dashboard.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $rol = $_POST['rol'];
            
            // Validaciones básicas
            if (empty($nombre) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Nombre y email válidos son obligatorios.";
                header('Location: ../views/usuarios.php');
                exit;
            }
            
            // Si password no está vacío, validar
            if (!empty($password)) {
                if (strlen($password) < 8 || $password !== $confirmPassword) {
                    $_SESSION['error'] = "Contraseña inválida o no coincide.";
                    header('Location: ../views/usuarios.php');
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
            header('Location: ../views/usuarios.php');
            exit;
        }
    }

    // Método: Eliminar usuario (para admins, sin cambios)
    public static function eliminarUsuario() {
        // Verificar que sea admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
            header('Location: ../views/dashboard.php');
            exit;
        }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            // Evitar que un admin se elimine a sí mismo
            if ($id == $_SESSION['user']['id']) {
                $_SESSION['error'] = "No puedes eliminarte a ti mismo.";
                header('Location: ../views/usuarios.php');
                exit;
            }
            if (Usuario::eliminar($id)) {
                $_SESSION['success'] = "Usuario eliminado.";
            } else {
                $_SESSION['error'] = "Error al eliminar.";
            }
            header('Location: ../views/usuarios.php');
            exit;
        }
    }
}

// Enrutador simple: Maneja acciones desde vistas
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'registrar') {
        AuthController::registrar();
    } elseif ($action == 'login') {
        AuthController::login();
    } elseif ($action == 'logout') {
        AuthController::logout();
    } elseif ($action == 'crearUsuario') {
        AuthController::crearUsuario();
    } elseif ($action == 'actualizarUsuario') {
        AuthController::actualizarUsuario();
    } elseif ($action == 'eliminarUsuario') {
        AuthController::eliminarUsuario();
    }
}
?>