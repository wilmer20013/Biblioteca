<?php
require_once '../models/Prestamo.php'; // Requiere el modelo Prestamo.php
session_start();

// Verificar login
if (!isset($_SESSION['user'])) {
    header('Location: /biblioteca/views/login.php'); // Ruta absoluta
    exit;
}

class PrestamoController {
    // Método: Manejar creación de préstamo (usa Prestamo::crear)
    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $libro_id = $_POST['libro_id'];
            $fecha_devolucion_estimada = $_POST['fecha_devolucion_estimada'];
            
            // Llamar al método del modelo
            if (Prestamo::crear($usuario_id, $libro_id, $fecha_devolucion_estimada)) {
                $_SESSION['success'] = "Préstamo realizado exitosamente.";
            } else {
                $_SESSION['error'] = "Libro no disponible o error en el préstamo.";
            }
            header('Location: /biblioteca/views/prestamos.php'); // Redirección a vista de préstamos
            exit;
        }
    }

    // Método: Manejar devolución (usa Prestamo::devolver y Prestamo::calcularMulta)
    public static function devolver() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Llamar al método del modelo
            if (Prestamo::devolver($id)) {
                $multa = Prestamo::calcularMulta($id); // Calcular multa después de devolver
                $_SESSION['success'] = "Libro devuelto exitosamente. Multa: $" . $multa;
            } else {
                $_SESSION['error'] = "Error en la devolución.";
            }
            header('Location: /biblioteca/views/prestamos.php'); // Redirección a vista de préstamos
            exit;
        }
    }
}

// Enrutador (maneja acciones desde vistas)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'crear') {
        PrestamoController::crear();
    } elseif ($action == 'devolver') {
        PrestamoController::devolver();
    }
}
?>