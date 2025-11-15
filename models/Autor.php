<?php
require_once '../config.php'; // Ajusta la ruta según tu estructura

class Autor {
    // Propiedades: Representan los campos de la tabla autores
    private $id;
    private $nombre;
    private $apellido;
    private $fecha_nacimiento;
    private $biografia;

    // Constructor: Inicializa propiedades
    public function __construct($id = null, $nombre = '', $apellido = '', $fecha_nacimiento = null, $biografia = '') {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->biografia = $biografia;
    }

    // Getters y Setters: Para acceder/modificar propiedades
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getApellido() { return $this->apellido; }
    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function getFechaNacimiento() { return $this->fecha_nacimiento; }
    public function setFechaNacimiento($fecha_nacimiento) { $this->fecha_nacimiento = $fecha_nacimiento; }
    public function getBiografia() { return $this->biografia; }
    public function setBiografia($biografia) { $this->biografia = $biografia; }

    // Método: Crear un nuevo autor
    public static function crear($nombre, $apellido, $fecha_nacimiento, $biografia) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO autores (nombre, apellido, fecha_nacimiento, biografia) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $apellido, $fecha_nacimiento, $biografia]);
    }

    // Método: Leer todos los autores
    public static function obtenerTodos() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM autores ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método: Leer un autor por ID
    public static function obtenerPorId($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM autores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método: Actualizar un autor
    public static function actualizar($id, $nombre, $apellido, $fecha_nacimiento, $biografia) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE autores SET nombre=?, apellido=?, fecha_nacimiento=?, biografia=? WHERE id=?");
        return $stmt->execute([$nombre, $apellido, $fecha_nacimiento, $biografia, $id]);
    }

    // Método: Eliminar un autor (solo si no tiene libros asociados)
    public static function eliminar($id) {
        $pdo = Database::getConnection();
        // Verificar si tiene libros
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM libros WHERE autor_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false; // No eliminar si tiene libros
        }
        $stmt = $pdo->prepare("DELETE FROM autores WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>