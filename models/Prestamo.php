<?php
require_once '../config.php';
require_once '../models/Libro.php'; // Ruta corregida para apuntar a models/Libro.php

class Prestamo {
    // Propiedades: Representan los campos de la tabla prestamos
    private $id;
    private $usuario_id;
    private $libro_id;
    private $fecha_prestamo;
    private $fecha_devolucion_estimada;
    private $fecha_devolucion_real;
    private $estado;

    // Constructor: Inicializa propiedades
    public function __construct($id = null, $usuario_id = '', $libro_id = '', $fecha_prestamo = '', $fecha_devolucion_estimada = '', $fecha_devolucion_real = null, $estado = 'activo') {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->libro_id = $libro_id;
        $this->fecha_prestamo = $fecha_prestamo;
        $this->fecha_devolucion_estimada = $fecha_devolucion_estimada;
        $this->fecha_devolucion_real = $fecha_devolucion_real;
        $this->estado = $estado;
    }

    // Getters y Setters: Para acceder/modificar propiedades
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    // ... (similar para otros getters/setters)

    // Método: Crear un préstamo (verifica disponibilidad)
    public static function crear($usuario_id, $libro_id, $fecha_devolucion_estimada) {
        $pdo = Database::getConnection();
        // Verificar disponibilidad del libro usando Libro::obtenerPorId (CORREGIDO)
        $libro = Libro::obtenerPorId($libro_id);
        if (!$libro || $libro['cantidad_disponible'] <= 0) {
            return false; // Libro no disponible
        }
        $fecha_prestamo = date('Y-m-d');
        $stmt = $pdo->prepare("INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion_estimada) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$usuario_id, $libro_id, $fecha_prestamo, $fecha_devolucion_estimada])) {
            // Actualizar cantidad disponible usando Libro::actualizarCantidad (CORREGIDO)
            Libro::actualizarCantidad($libro_id, $libro['cantidad_disponible'] - 1);
            return true;
        }
        return false;
    }

    // Método: Leer préstamos (todos o por usuario)
    public static function obtenerTodos($usuario_id = null) {
        $pdo = Database::getConnection();
        $query = "SELECT prestamos.*, usuarios.nombre AS usuario_nombre, libros.titulo AS libro_titulo FROM prestamos 
                  JOIN usuarios ON prestamos.usuario_id = usuarios.id 
                  JOIN libros ON prestamos.libro_id = libros.id";
        if ($usuario_id) {
            $query .= " WHERE prestamos.usuario_id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$usuario_id]);
        } else {
            $stmt = $pdo->query($query);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método: Devolver un préstamo (actualiza estado y cantidad)
    public static function devolver($id) {
        $pdo = Database::getConnection();
        // Obtener el préstamo primero para acceder a libro_id
        $prestamo = self::obtenerPorId($id);
        if (!$prestamo) {
            return false; // Préstamo no encontrado
        }
        $fecha_devolucion = date('Y-m-d');
        $stmt = $pdo->prepare("UPDATE prestamos SET estado='devuelto', fecha_devolucion_real=? WHERE id=?");
        if ($stmt->execute([$fecha_devolucion, $id])) {
            // Obtener libro y actualizar cantidad usando Libro::obtenerPorId y Libro::actualizarCantidad (CORREGIDO)
            $libro = Libro::obtenerPorId($prestamo['libro_id']);
            if ($libro) {
                Libro::actualizarCantidad($prestamo['libro_id'], $libro['cantidad_disponible'] + 1);
            }
            return true;
        }
        return false;
    }

    // Método: Calcular multa (ej. 1 USD por día de retraso)
    public static function calcularMulta($id) {
        $prestamo = self::obtenerPorId($id);
        if ($prestamo && $prestamo['estado'] == 'devuelto' && $prestamo['fecha_devolucion_real'] > $prestamo['fecha_devolucion_estimada']) {
            $dias_retraso = (strtotime($prestamo['fecha_devolucion_real']) - strtotime($prestamo['fecha_devolucion_estimada'])) / (60*60*24);
            return $dias_retraso * 1; // 1 USD por día
        }
        return 0;
    }

    // Método auxiliar: Obtener préstamo por ID
    public static function obtenerPorId($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM prestamos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>