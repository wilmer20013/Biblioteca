<?php
require_once '../models/Autor.php';
session_start();

// Verificar login y rol admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header('Location: /biblioteca/views/login.php');
    exit;
}

class AutorController {
    // Método: Manejar creación
    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $biografia = $_POST['biografia'];
            if (Autor::crear($nombre, $apellido, $fecha_nacimiento, $biografia)) {
                $_SESSION['success'] = "Autor creado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al crear autor.";
            }
            header('Location: /biblioteca/views/autores.php'); // Redirección a vista de autores
            exit;
        }
    }

    // Método: Manejar actualización
    public static function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $biografia = $_POST['biografia'];
            if (Autor::actualizar($id, $nombre, $apellido, $fecha_nacimiento, $biografia)) {
                $_SESSION['success'] = "Autor actualizado.";
            } else {
                $_SESSION['error'] = "Error al actualizar.";
            }
            header('Location: /biblioteca/views/autores.php'); // Redirección a vista de autores
            exit;
        }
    }

    // Método: Manejar eliminación
    public static function eliminar() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if (Autor::eliminar($id)) {
                $_SESSION['success'] = "Autor eliminado.";
            } else {
                $_SESSION['error'] = "No se puede eliminar autor con libros asociados.";
            }
            header('Location: /biblioteca/views/autores.php'); // Redirección a vista de autores
            exit;
        }
    }
}

// Enrutador
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'crear') AutorController::crear();
    elseif ($action == 'actualizar') AutorController::actualizar();
    elseif ($action == 'eliminar') AutorController::eliminar();
}
?>