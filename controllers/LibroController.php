<?php
require_once '../models/Libro.php';
session_start();

// Verificar login
if (!isset($_SESSION['user'])) {
    header('Location: /biblioteca/views/login.php');
    exit;
}

class LibroController {
    // Método: Manejar creación
    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $titulo = $_POST['titulo'];
            $autor_id = $_POST['autor_id'];
            $isbn = $_POST['isbn'];
            $genero = $_POST['genero'];
            $cantidad_disponible = $_POST['cantidad_disponible'];
            if (Libro::crear($titulo, $autor_id, $isbn, $genero, $cantidad_disponible)) {
                $_SESSION['success'] = "Libro creado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al crear libro.";
            }
            header('Location: /biblioteca/views/libros.php'); // Redirección a vista de libros
            exit;
        }
    }

    // Método: Manejar actualización
    public static function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $titulo = $_POST['titulo'];
            $autor_id = $_POST['autor_id'];
            $isbn = $_POST['isbn'];
            $genero = $_POST['genero'];
            $cantidad_disponible = $_POST['cantidad_disponible'];
            if (Libro::actualizar($id, $titulo, $autor_id, $isbn, $genero, $cantidad_disponible)) {
                $_SESSION['success'] = "Libro actualizado.";
            } else {
                $_SESSION['error'] = "Ya existe. Por favor, usa un ISBN único.";
            }
            header('Location: /biblioteca/views/libros.php'); // Redirección a vista de libros
            exit;
        }
    }

    // Método: Manejar eliminación
    public static function eliminar() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if (Libro::eliminar($id)) {
                $_SESSION['error'] = "Libro eliminado.";
            } else {
                $_SESSION['error'] = "Error al eliminar.";
            }
            header('Location: /biblioteca/views/libros.php'); // Redirección a vista de libros
            exit;
        }
    }
}

// Enrutador
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'crear') LibroController::crear();
    elseif ($action == 'actualizar') LibroController::actualizar();
    elseif ($action == 'eliminar') LibroController::eliminar();
}
?>