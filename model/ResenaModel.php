<?php
require_once __DIR__ . '/../config/conexion.php';

class ResenaModel {
    private PDO $db;

    public function __construct() {
        $this->db = (new database())->conectar();
    }

    // Guarda una reseña pública de un cliente
    public function crear(array $datos, int $negocioId): bool {
        try {
            $sql = "INSERT INTO resenas (negocio_id, cliente_nombre, rating, comentario)
                    VALUES (:negocio_id, :cliente_nombre, :rating, :comentario)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'negocio_id' => $negocioId,
                'cliente_nombre' => $datos['cliente_nombre'],
                'rating' => (int) $datos['rating'],
                'comentario' => $datos['comentario'],
            ]);
        } catch (PDOException $e) {
            error_log("Error en ResenaModel::crear: " . $e->getMessage());
            return false;
        }
    }

    // Todas las reseñas de un negocio (más recientes primero)
    public function obtenerPorNegocio(int $negocioId): array {
        $sql = "SELECT * FROM resenas WHERE negocio_id = :negocio_id ORDER BY created_at DESC, id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['negocio_id' => $negocioId]);
        return $stmt->fetchAll();
    }

    // Promedio y total de reseñas de un negocio (o null si no tiene)
    public function promedioPorNegocio(int $negocioId): ?array {
        $sql = "SELECT ROUND(AVG(rating), 1) AS promedio, COUNT(*) AS total FROM resenas WHERE negocio_id = :negocio_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['negocio_id' => $negocioId]);
        $fila = $stmt->fetch();
        return ($fila && (int) $fila['total'] > 0) ? $fila : null;
    }

    // Promedios de varios negocios a la vez (para listas: directorio), clave = negocio_id
    public function promediosPorNegocios(array $ids): array {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (empty($ids)) return [];
        $in = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT negocio_id, ROUND(AVG(rating), 1) AS promedio, COUNT(*) AS total
                FROM resenas WHERE negocio_id IN ($in) GROUP BY negocio_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $mapa = [];
        foreach ($stmt->fetchAll() as $fila) {
            $mapa[(int) $fila['negocio_id']] = $fila;
        }
        return $mapa;
    }
}