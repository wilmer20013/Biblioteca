<?php
require_once '../config.php';

class Libro {
    private $id, $titulo, $autor_id, $isbn, $genero, $cantidad_disponible;

    public function __construct($id = null, $titulo = '', $autor_id = '', $isbn = '', $genero = '', $cantidad_disponible = 0) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->autor_id = $autor_id;
        $this->isbn = $isbn;
        $this->genero = $genero;
        $this->cantidad_disponible = $cantidad_disponible;
    }

    // Getters/Setters omitidos por brevedad

    // Método: Crear libro con validación de ISBN único
    public static function crear($titulo, $autor_id, $isbn, $genero, $cantidad) {
        $pdo = Database::getConnection();
        
        // Validar que el ISBN no exista ya
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM libros WHERE isbn = ?");
        $stmtCheck->execute([$isbn]);
        $count = $stmtCheck->fetchColumn();
        
        if ($count > 0) {
            return "El ISBN '$isbn' ya existe. Por favor, usa un ISBN único."; // Mensaje de error
        }
        
        // Si no existe, proceder con la inserción
        $stmt = $pdo->prepare("INSERT INTO libros (titulo, autor_id, isbn, genero, cantidad_disponible) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$titulo, $autor_id, $isbn, $genero, $cantidad])) {
            return true; // Éxito
        } else {
            return "Error al crear el libro."; // Otro error
        }
    }

    // Método: Leer todos los libros
    public static function obtenerTodos() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT libros.*, autores.nombre, autores.apellido FROM libros JOIN autores ON libros.autor_id = autores.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método: Leer un libro por ID (AGREGADO - usado en Prestamo.php)
    public static function obtenerPorId($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método: Actualizar libro completo
   public static function actualizar($id, $titulo, $autor_id, $isbn, $genero, $cantidad) {
    $pdo = Database::getConnection();

    // Validar que el ISBN no pertenezca a otro libro diferente al que se está actualizando
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM libros WHERE isbn = ? AND id != ?");
    $stmtCheck->execute([$isbn, $id]);
    $count = $stmtCheck->fetchColumn();
}


    // Método: Actualizar solo la cantidad disponible (AGREGADO - usado en Prestamo.php)
    public static function actualizarCantidad($id, $nuevaCantidad) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE libros SET cantidad_disponible=? WHERE id=?");
        return $stmt->execute([$nuevaCantidad, $id]);
    }

    // Método: Eliminar libro
    public static function eliminar($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM libros WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
