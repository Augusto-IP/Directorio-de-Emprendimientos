<?php
// config/base.php — Detecta la URL base del proyecto automáticamente
// para que funcione tanto en http://localhost/Directorio-emprendimientos/
// como en http://localhost:3000/ (servidor con docroot dentro del proyecto).

if (!function_exists('app_base')) {
    function app_base(): string {
        $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__)));
        $app = str_replace('\\', '/', realpath(__DIR__ . '/..'));
        if ($docRoot !== false && $app !== false && strpos($app, $docRoot) === 0) {
            return rtrim(substr($app, strlen($docRoot)), '/');
        }
        return '';
    }

    function app_url(string $ruta = ''): string {
        return app_base() . '/' . ltrim($ruta, '/');
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', app_base());
}

// --- Ayudantes de WhatsApp ---

if (!function_exists('url_whatsapp')) {
    // Normaliza el número nacional (Perú: 51) y arma el enlace wa.me con mensaje
    function url_whatsapp(string $numero, string $mensaje): string {
        $limpio = preg_replace('/\D+/', '', $numero);
        if ($limpio !== '' && strlen($limpio) === 9 && $limpio[0] === '9') {
            $limpio = '51' . $limpio;
        }
        if ($limpio === '') {
            return '#';
        }
        return 'https://wa.me/' . $limpio . '?text=' . rawurlencode($mensaje);
    }

    // Mensaje bonito de contacto general desde el directorio
    function mensaje_contacto_directorio(string $negocio): string {
        return "¡Hola, {$negocio}! 👋\n\n" .
            "Vengo de la página de Gestión de Negocios de AgendaPro y vi tu emprendimiento.\n" .
            "Me encantaría reservar o preguntar sobre tus servicios. 🤗\n\n" .
            "¿Podrías ayudarme? ¡Gracias de antemano! ✨";
    }

    // Mensaje bonito para reservar/consultar un servicio puntual
    function mensaje_reservar_servicio(string $negocio, string $servicio): string {
        return "¡Hola, {$negocio}! 👋\n\n" .
            "Estoy en la página de Gestión de Negocios de AgendaPro y " .
            "quiero reservar el servicio \"{$servicio}\". 🗓️\n\n" .
            "¿Me confirmas disponibilidad y precio? ¡Gracias! 😊";
    }
}