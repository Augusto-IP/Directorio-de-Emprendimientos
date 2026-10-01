<?php
require_once __DIR__ . '/../config/conexion.php';

class ServicioModel {
    private PDO $db;

    public function __construct() {
        $this->db = (new database())->conectar();
    }

    public function obtenerTodos(): array {
        $sql = "SELECT s.*, c.nombre AS categoria
                FROM servicios s
                LEFT JOIN categorias c ON c.id = s.categoria_id
                ORDER BY s.created_at DESC, s.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorNegocio(int $negocioId): array {
        $sql = "SELECT s.*, c.nombre AS categoria
                FROM servicios s
                LEFT JOIN categorias c ON c.id = s.categoria_id
                WHERE s.negocio_id = :negocio_id
                ORDER BY s.estado ASC, s.precio ASC, s.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['negocio_id' => $negocioId]);
        return $stmt->fetchAll();
    }

    public function guardar(array $datos, int $negocioId): bool {
        try {
            // La categoría es por negocio
            $categoria = $this->db->prepare('SELECT id FROM categorias WHERE nombre = :nombre AND negocio_id = :negocio_id LIMIT 1');
            $categoria->execute(['nombre' => $datos['categoria'], 'negocio_id' => $negocioId]);
            $categoriaId = $categoria->fetchColumn();

            if (!$categoriaId) {
                $crearCategoria = $this->db->prepare('INSERT INTO categorias (negocio_id, nombre) VALUES (:negocio_id, :nombre)');
                $crearCategoria->execute(['negocio_id' => $negocioId, 'nombre' => $datos['categoria']]);
                $categoriaId = (int) $this->db->lastInsertId();
            }

            $sql = "INSERT INTO servicios
                        (negocio_id, categoria_id, nombre, descripcion, precio, duracion_minutos, imagen, estado)
                    VALUES
                        (:negocio_id, :categoria_id, :nombre, :descripcion, :precio, :duracion, :imagen, :estado)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'negocio_id' => $negocioId,
                'categoria_id' => $categoriaId,
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'],
                'precio' => $datos['precio'],
                'duracion' => $datos['duracion'],
                'imagen' => $datos['imagen'] ?? null,
                'estado' => $datos['estado'],
            ]);
        } catch (PDOException $e) {
            error_log("Error en ServicioModel::guardar: " . $e->getMessage());
            return false;
        }
    }

    // Obtiene un servicio verificando que pertenezca al negocio del usuario
    public function obtenerPorIdYNegocio(int $servicioId, int $negocioId): ?array {
        $sql = "SELECT s.*, c.nombre AS categoria
                FROM servicios s
                LEFT JOIN categorias c ON c.id = s.categoria_id
                WHERE s.id = :id AND s.negocio_id = :negocio_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $servicioId, 'negocio_id' => $negocioId]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
        return $servicio ?: null;
    }

    // Actualiza los datos (y categoría) de un servicio
    public function actualizar(array $datos, int $servicioId, int $negocioId): bool {
        try {
            // La categoría es por negocio
            $categoria = $this->db->prepare('SELECT id FROM categorias WHERE nombre = :nombre AND negocio_id = :negocio_id LIMIT 1');
            $categoria->execute(['nombre' => $datos['categoria'], 'negocio_id' => $negocioId]);
            $categoriaId = $categoria->fetchColumn();

            if (!$categoriaId) {
                $crearCategoria = $this->db->prepare('INSERT INTO categorias (negocio_id, nombre) VALUES (:negocio_id, :nombre)');
                $crearCategoria->execute(['negocio_id' => $negocioId, 'nombre' => $datos['categoria']]);
                $categoriaId = (int) $this->db->lastInsertId();
            }

            $sql = "UPDATE servicios SET
                        categoria_id = :categoria_id,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio = :precio,
                        duracion_minutos = :duracion,
                        imagen = :imagen,
                        estado = :estado
                    WHERE id = :id AND negocio_id = :negocio_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'categoria_id' => $categoriaId,
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'],
                'precio' => $datos['precio'],
                'duracion' => $datos['duracion'],
                'imagen' => $datos['imagen'] ?? null,
                'estado' => $datos['estado'],
                'id' => $servicioId,
                'negocio_id' => $negocioId,
            ]);
        } catch (PDOException $e) {
            error_log("Error en ServicioModel::actualizar: " . $e->getMessage());
            return false;
        }
    }

    // Elimina un servicio verificando que pertenezca al negocio del usuario
    public function eliminar(int $servicioId, int $negocioId): bool {
        try {
            $sql = "DELETE FROM servicios WHERE id = :id AND negocio_id = :negocio_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $servicioId, 'negocio_id' => $negocioId]);
        } catch (PDOException $e) {
            error_log("Error en ServicioModel::eliminar: " . $e->getMessage());
            return false;
        }
    }
}
