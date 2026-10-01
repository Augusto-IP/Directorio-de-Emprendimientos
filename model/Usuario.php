<?php
// Llamamos a la base de datos para poder conectar
require_once __DIR__ . '/../config/conexion.php';

// Consulta y gestión de datos de la tabla usuarios
class Usuario{
    // Creamos una variable que guarde la conexión activa de la base de datos
    private PDO $pdo;

    // El constructor se ejecuta automáticamente al instanciar la clase y crea la conexión
    public function __construct(){
        $database = new database();
        $this->pdo = $database->conectar();
    }

    // Busca el usuario por correo y verifica si la contraseña es correcta (retorna un array o null)
    public function login(string $correo, string $password): ?array{

        // Consulta SQL para buscar al usuario por correo de forma segura (tabla 'usuarios')
        $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";

        // Prepara la consulta en el servidor para prevenir ataques de Inyección SQL
        $stmt = $this->pdo->prepare($sql);

        // Ejecuta la consulta pasando el correo real recibido como parámetro
        $stmt->execute([
            'correo' => $correo
        ]);

        // Obtiene los datos del usuario en un array asociativo
        $usuario = $stmt->fetch();

        // Si no encontró ningún usuario con ese correo, retorna null
        if(!$usuario){
            return null;
        }

        // Mejora: guardamos la contraseña almacenada de forma segura y verificamos
        // si viene como texto plano o como hash BCrypt, evitando errores al comparar.
        $storedPassword = $usuario['password'] ?? '';

        // Compara la contraseña ingresada con la de la BD (soporta texto plano o el hash $2y$10$...)
        if(
            $password === $storedPassword ||
            ($storedPassword !== '' && password_verify($password, $storedPassword))
        ){
            return $usuario; // Retorna todos los datos del usuario si la clave coincide
        }

                return null; // Si la contraseña es incorrecta, retorna null
    }

    // Verifica si un correo ya está registrado
    public function correoExiste(string $correo): bool{
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo LIMIT 1");
        $stmt->execute(['correo' => $correo]);
        return (bool) $stmt->fetchColumn();
    }

    // Registra un nuevo usuario; devuelve el id creado o false si falla
    public function registrar(string $nombre, string $correo, string $password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (:nombre, :correo, :password, 'admin')");
        $ok = $stmt->execute([
            'nombre'   => $nombre,
            'correo'   => $correo,
            'password' => $hash,
        ]);
        return $ok ? (int) $this->pdo->lastInsertId() : false;
    }

    // Busca un usuario por su id
    public function obtenerPorId(int $id): ?array{
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    // Actualiza nombre y correo de un usuario
    public function actualizarDatos(int $id, string $nombre, string $correo): bool{
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id");
            return $stmt->execute(['nombre' => $nombre, 'correo' => $correo, 'id' => $id]);
        } catch (PDOException $e) {
            error_log("Error en Usuario::actualizarDatos: " . $e->getMessage());
            return false;
        }
    }

    // Cambia la contraseña guardándola con hash seguro
    public function cambiarPassword(int $id, string $nueva): bool{
        try {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
            return $stmt->execute(['password' => $hash, 'id' => $id]);
        } catch (PDOException $e) {
            error_log("Error en Usuario::cambiarPassword: " . $e->getMessage());
            return false;
        }
    }
}
?>