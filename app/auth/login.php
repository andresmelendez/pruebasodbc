<?php

require '../../database/ConectorBD.php';
require '../../class/SAP/OUSR.php';

header('Content-Type: application/json');
session_start();

// Código para la actualización automática desde GitHub

// Configuración del repositorio
$repo_version_url = 'https://raw.githubusercontent.com/andresmelendez/pruebasodbc/main/version.txt'; // URL directa del archivo version.txt en el repositorio
$repo_zip_url = 'https://github.com/andresmelendez/pruebasodbc/archive/refs/heads/main.zip'; // URL para descargar el proyecto como ZIP
$local_version_file = __DIR__ . '../../version.txt'; // Ruta al archivo version.txt en el cliente

// Leer la versión local
$local_version = file_exists($local_version_file) ? trim(file_get_contents($local_version_file)) : '0.0';

// Leer la versión remota
$remote_version = trim(file_get_contents($repo_version_url));

// Verificar si hay una actualización
if (version_compare($remote_version, $local_version, '>')) {
    // Descargar el archivo ZIP del repositorio
    $zip_file = __DIR__ . '../../proyecto.zip';
    file_put_contents($zip_file, file_get_contents($repo_zip_url));

    // Descomprimir el archivo ZIP y reemplazar archivos
    $zip = new ZipArchive;
    if ($zip->open($zip_file) === TRUE) {
        $zip->extractTo(__DIR__);
        $zip->close();
        unlink($zip_file); // Eliminar el archivo ZIP después de la extracción

        // Actualizar la versión local
        file_put_contents($local_version_file, $remote_version);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al descomprimir la actualización']);
        exit;
    }
}

// Fin del código de actualización

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Saneamiento de entradas
$usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
$clave = hash('sha3-512', filter_input(INPUT_POST, 'clave', FILTER_SANITIZE_STRING));

// Verificar si los campos están presentes
if (empty($usuario) || empty($clave)) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario y clave son requeridos']);
    exit;
}

// Validar formato del nombre de usuario (solo letras, números y espacios)
if (!preg_match('/^[a-zA-Z0-9 ]+$/', $usuario)) {
    echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario contiene caracteres no permitidos']);
    exit;
}

// Validar clave
$claveValidar = hash('sha3-512', '$' . $usuario . '$');
if ($clave !== $claveValidar) {
    echo json_encode(['status' => 'error', 'message' => 'usuario o clave es incorrecta']);
    exit;
}

// Intentar validar el usuario
$persona = OUSR::validar($usuario, $clave);

// Verificar si la validación fue exitosa
if ($persona === null) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario o clave incorrectos']);
    exit;
}

// Si la validación es correcta, iniciar sesión
$_SESSION['usuario'] = $persona;

// Respuesta exitosa con redirección
echo json_encode([
    'status' => 'success',
    'message' => 'Autenticación exitosa',
    'data' => ['redirect' => 'principal.php?CONTENIDO=app/dashboard/dashboard.php']
]);
exit;
