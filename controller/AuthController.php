<?php

// Importa el archivo del modelo 'Usuario' para poder interactuar con los datos del usuario
require_once __DIR__ . '/../model/Usuario.php';

// Controlador encargado de gestionar el acceso (login) y autenticación del sistema
class AuthController{

    // Intenta iniciar sesión con el correo y clave; devuelve true si es correcto o false si falla
    public function login(string $email, string $password): bool{
        
        // Instancia el modelo Usuario para consultar la base de datos
        $usuarioModel = new Usuario();
        
        // Llama al método login del modelo para verificar si las credenciales son válidas
        $usuario = $usuarioModel->login($email, $password);
        
        // Si el usuario existe y las credenciales son válidas
        if($usuario){ 
            $_SESSION['usuario'] = true; // Marca que existe sesión activa
            $_SESSION['id_usuario'] = $usuario['id']; 
            $_SESSION['usuario_nombre'] = $usuario['nombre']; 
            $_SESSION['usuario_email'] = $usuario['correo']; 
            return true; // Inicio de sesión exitoso
        }
        
        return false; // Credenciales incorrectas o usuario no encontrado
    }
}
?>