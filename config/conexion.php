<?php
// Clase encargada de gestionar la conexión con la base de datos MySQL usando PDO
class database{
    // Datos de acceso al servidor de la base de datos
    private string $host = 'localhost';
    private string $db = 'catalogo_negocios';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = "utf8mb4";
    

    // Crea y retorna la conexión activa a la base de datos con configuraciones de seguridad
    public function conectar(): PDO{
        try{
            // Cadena con los datos del servidor, base de datos y codificación de caracteres
            $dns = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

            // Retorna un objeto PDO configurado para manejar errores de forma segura y devolver arrays asociativos
            return new PDO($dns, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

        }catch(PDOException $e){
            // Si la conexión falla, detiene el programa y muestra el mensaje de error
            die('Error de conexion a la base de datos: ' . $e->getMessage());
        }
    }
}
?>