<?php
// model/NegocioModel.php

require_once __DIR__ . '/../config/conexion.php'; // Clase de conexión a BD con PDO

class NegocioModel {
    private $db;

    public function __construct() {
        $database = new database();
        $this->db = $database->conectar();
    }

    // Crear un nuevo negocio para un usuario; devuelve el id creado o false
    public function crear(array $datos, int $usuarioId) {
        try {
            $slug = $this->generarSlug($datos['nombre'] ?? 'negocio');

            $sql = "INSERT INTO negocios (usuario_id, nombre, slug, categoria, descripcion, whatsapp, telefono, ubicacion, correo, logo, horario, banner_local)
                    VALUES (:usuario_id, :nombre, :slug, :categoria, :descripcion, :wsp, :telefono, :ubicacion, :correo, :logo, :horario, :banner_local)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'usuario_id'  => $usuarioId,
                'nombre'      => $datos['nombre'],
                'slug'        => $slug,
                'categoria'   => $datos['categoria'],
                'descripcion' => $datos['descripcion'] ?? '',
                'wsp'         => $datos['wsp'] ?? '',
                'telefono'    => $datos['telefono'] ?? '',
                'ubicacion'   => $datos['ubicacion'] ?? '',
                'correo'      => $datos['correo'] ?? null,
                'logo'        => $datos['logo'] ?? null,
                'horario'     => $datos['horario'] ?? null,
                'banner_local' => 'default_banner.png',
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en NegocioModel::crear: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar los datos de un negocio específico
    public function actualizar(int $id, array $datos): bool {
        try {
            $sql = "UPDATE negocios SET
                        nombre = :nombre,
                        categoria = :categoria,
                        descripcion = :descripcion,
                        whatsapp = :wsp,
                        correo = :correo,
                        telefono = :telefono,
                        ubicacion = :ubicacion,
                        horario = :horario";
            if (!empty($datos['logo'])) {
                $sql .= ", logo = :logo";
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':nombre', $datos['nombre']);
            $stmt->bindValue(':categoria', $datos['categoria']);
            $stmt->bindValue(':descripcion', $datos['descripcion'] ?? '');
            $stmt->bindValue(':wsp', $datos['wsp'] ?? '');
            $stmt->bindValue(':correo', $datos['correo'] ?? null);
            $stmt->bindValue(':telefono', $datos['telefono'] ?? '');
            $stmt->bindValue(':ubicacion', $datos['ubicacion'] ?? '');
            $stmt->bindValue(':horario', $datos['horario'] ?? null);
            if (!empty($datos['logo'])) {
                $stmt->bindValue(':logo', $datos['logo']);
            }
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en NegocioModel::actualizar: " . $e->getMessage());
            return false;
        }
    }

    // Todos los negocios visibles del catálogo público
    public function obtenerTodos(): array {
        $sql = "SELECT * FROM negocios
                WHERE estado IS NULL OR estado = 'activo'
                ORDER BY created_at DESC, id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Un negocio del catálogo público por id
    public function obtenerPorId(int $id) {
        $sql = "SELECT * FROM negocios WHERE id = :id AND (estado IS NULL OR estado = 'activo') LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $negocio = $stmt->fetch(PDO::FETCH_ASSOC);
        return $negocio ?: null;
    }

    // Todos los negocios que pertenecen a un usuario (para el panel)
    public function obtenerPorUsuario(int $usuarioId): array {
        $sql = "SELECT * FROM negocios
                WHERE usuario_id = :usuario_id
                ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Un negocio específico del usuario (para editar)
    public function obtenerPorIdYUsuario(int $id, int $usuarioId) {
        $sql = "SELECT * FROM negocios WHERE id = :id AND usuario_id = :usuario_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'usuario_id' => $usuarioId]);
        $negocio = $stmt->fetch(PDO::FETCH_ASSOC);
        return $negocio ?: null;
    }

    // Elimina un negocio del usuario
    public function eliminar(int $id, int $usuarioId): bool {
        try {
            $sql = "DELETE FROM negocios WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id, 'usuario_id' => $usuarioId]);
        } catch (PDOException $e) {
            error_log("Error en NegocioModel::eliminar: " . $e->getMessage());
            return false;
        }
    }

    // Cambia la visibilidad de un negocio del usuario: 'activo' (visible) o 'inactivo' (oculto)
    public function cambiarEstado(int $id, int $usuarioId, string $estado): bool {
        try {
            if (!in_array($estado, ['activo', 'inactivo'], true)) return false;
            $sql = "UPDATE negocios SET estado = :estado WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['estado' => $estado, 'id' => $id, 'usuario_id' => $usuarioId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en NegocioModel::cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    // --- GALERÍA DE FOTOS DEL NEGOCIO ---

    // Guarda una o varias fotos en la galería del negocio
    public function guardarFotos(int $negocioId, array $rutas): bool {
        try {
            if (empty($rutas)) return true;
            $sql = "INSERT INTO negocios_galeria (negocio_id, imagen) VALUES (:negocio_id, :imagen)";
            $stmt = $this->db->prepare($sql);
            foreach ($rutas as $ruta) {
                $stmt->execute(['negocio_id' => $negocioId, 'imagen' => $ruta]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error en NegocioModel::guardarFotos: " . $e->getMessage());
            return false;
        }
    }

    // Todas las fotos de un negocio
    public function obtenerFotos(int $negocioId): array {
        $sql = "SELECT id, imagen, titulo FROM negocios_galeria
                WHERE negocio_id = :negocio_id
                ORDER BY creado_at ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['negocio_id' => $negocioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina una foto verificando que pertenezca a un negocio del usuario
    public function eliminarFoto(int $fotoId, int $usuarioId): bool {
        try {
            $sql = "DELETE g FROM negocios_galeria g
                    INNER JOIN negocios n ON n.id = g.negocio_id
                    WHERE g.id = :foto_id AND n.usuario_id = :usuario_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['foto_id' => $fotoId, 'usuario_id' => $usuarioId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en NegocioModel::eliminarFoto: " . $e->getMessage());
            return false;
        }
    }

    // Genera un slug único a partir del nombre
    private function generarSlug(string $nombre): string {
        $slug = strtolower(trim($nombre));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-') ?: 'negocio';
        $slug = substr($slug, 0, 90);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM negocios WHERE slug LIKE :slug");
        $stmt->execute(['slug' => $slug . '%']);
        $n = (int) $stmt->fetchColumn();
        return $n > 0 ? $slug . '-' . ($n + 1) : $slug;
    }
}