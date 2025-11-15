<?php
require_once '../config.php'; // Ajusta la ruta si es necesario

class Usuario {
    // Propiedades: Representan los campos de la tabla usuarios
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $rol;

    // Constructor: Inicializa propiedades
    public function __construct($id = null, $nombre = '', $email = '', $password = '', $rol = 'usuario') {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol = $rol;
    }

    // Getters y Setters: Para acceder/modificar propiedades
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }
    public function getRol() { return $this->rol; }
    public function setRol($rol) { $this->rol = $rol; }

    // Método: Registrar un nuevo usuario (básico, sin validaciones)
    public static function registrar($nombre, $email, $password, $rol = 'usuario') {
        $pdo = Database::getConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash seguro
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, $hashedPassword, $rol]);
    }

    // Método: Registrar con validaciones completas (adaptado del controlador, ahora incluye rol)
    public static function registrarConValidacion($nombre, $email, $password, $confirmPassword, $rol = 'usuario') {
        // Validaciones
        if (empty($nombre) || empty($email) || empty($password)) {
            return "Todos los campos son obligatorios.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email inválido.";
        }
        if (strlen($password) < 8) {
            return "La contraseña debe tener al menos 8 caracteres.";
        }
        if ($password !== $confirmPassword) {
            return "Las contraseñas no coinciden.";
        }
        if (!in_array($rol, ['usuario', 'admin'])) {
            return "Rol inválido.";
        }

        // Verificar unicidad de email
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return "El email ya está registrado.";
        }

        // Registrar si validaciones pasan
        if (self::registrar($nombre, $email, $password, $rol)) {
            return true; // Éxito
        }
        return "Error en el registro. Inténtalo de nuevo.";
    }

    // Método: Verificar login
    public static function login($email, $password) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Retorna datos del usuario si válido
        }
        return false;
    }

    // Método: Obtener todos los usuarios (para admin)
    public static function obtenerTodos() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método: Actualizar usuario
    public static function actualizar($id, $nombre, $email, $hashedPassword = null, $rol = 'usuario') {
        $pdo = Database::getConnection();
        if ($hashedPassword) {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?");
            return $stmt->execute([$nombre, $email, $hashedPassword, $rol, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
            return $stmt->execute([$nombre, $email, $rol, $id]);
        }
    }

    // Método: Eliminar usuario
    public static function eliminar($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
