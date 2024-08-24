<?php
// Datos de configuración FTP
$ftp_server = "11.11.11.11"; // Cambia por la dirección de tu servidor FTP
$ftp_username = "user"; // Cambia por tu nombre de usuario FTP
$ftp_password = "pass"; // Cambia por tu contraseña FTP

// Conectar al servidor FTP
$conn_id = ftp_connect($ftp_server);

// Ruta del archivo o directorio remoto a descargar y la ubicación de almacenamiento local
$remote_path = "/domains/tusitiowebpro.tech/public_html/"; // Directorio o archivo en el servidor FTP
$local_path = '../downloads/'; // Directorio local donde se guardará el archivo o directorio descargado

// Ruta del log
$log_file = '../logs/ftp_log.txt';

// Iniciar log
$log = "Inicio de la descarga FTP: " . date('Y-m-d H:i:s') . "\n";

// Verificar si la conexión fue exitosa
if (!$conn_id) {
    $log .= "No se pudo conectar al servidor FTP\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    exit;
}

// Iniciar sesión con las credenciales proporcionadas
if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    $log .= "Conectado a $ftp_server como $ftp_username\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    echo "Conectado a $ftp_server como $ftp_username.<br>";
} else {
    $log .= "No se pudo iniciar sesión en el servidor FTP\n";
    ftp_close($conn_id);
    file_put_contents($log_file, $log, FILE_APPEND);
    exit;
}

// Cambiar a modo pasivo (opcional, pero recomendado)
ftp_pasv($conn_id, true);

// Función para descargar archivos y/o directorios
function descargar($conn_id, $remote_path, $local_path) {
    global $log;

    // Verificar si es un archivo o directorio
    $size = ftp_size($conn_id, $remote_path);
    if ($size == -1) {
        // Es un directorio, descargarlo recursivamente
        $log .= "Descargando directorio: $remote_path\n";
        file_put_contents($log_file, $log, FILE_APPEND);
        echo "Descargando directorio: $remote_path\n";
        descargar_directorio($conn_id, $remote_path, $local_path);
    } else {
        // Es un archivo, descargarlo
        $log .= "Descargando archivo: $remote_path a $local_path\n";
        file_put_contents($log_file, $log, FILE_APPEND);
        echo "Descargando archivo: $remote_path a $local_path\n";
        if (ftp_get($conn_id, $local_path, $remote_path, FTP_ASCII)) {
            $log .= "Archivo descargado exitosamente: $local_path\n";
            file_put_contents($log_file, $log, FILE_APPEND);
            echo "Archivo descargado exitosamente: $local_path\n";
        } else {
            $log .= "Error al descargar: $remote_path\n";
            file_put_contents($log_file, $log, FILE_APPEND);
            echo "Error al descargar: $remote_path\n";
        }
    }
}

// Función recursiva para descargar directorios
function descargar_directorio($conn_id, $remote_dir, $local_dir) {
    global $log;

    // Obtener la lista de archivos y directorios
    $items = ftp_nlist($conn_id, $remote_dir);

    // Crear el directorio local si no existe
    if (!is_dir($local_dir)) {
        mkdir($local_dir, 0777, true);
    }

    foreach ($items as $item) {
        $basename = basename($item);

        // Ignorar "." y ".."
        if ($basename == '.' || $basename == '..') {
            continue;
        }

        // Ruta completa para el archivo o directorio
        $remote_path = $remote_dir . $basename;
        $local_path = $local_dir . $basename;

        // Verificar si es un directorio o un archivo
        if (ftp_size($conn_id, $remote_path) == -1) {
            // Es un directorio, llamar recursivamente
            $log .= "Descargando subdirectorio: $remote_path\n";
            file_put_contents($log_file, $log, FILE_APPEND);
            echo "Descargando subdirectorio: $remote_path\n";
            descargar_directorio($conn_id, $remote_path . '/', $local_path . '/');
        } else {
            // Es un archivo, descargarlo
            $log .= "Descargando archivo: $remote_path a $local_path\n";
            file_put_contents($log_file, $log, FILE_APPEND);
            echo "Descargando archivo: $remote_path a $local_path\n";
            if (ftp_get($conn_id, $local_path, $remote_path, FTP_ASCII)) {
                $log .= "Archivo descargado exitosamente: $local_path\n";
                file_put_contents($log_file, $log, FILE_APPEND);
                echo "Archivo descargado exitosamente: $local_path\n";
            } else {
                $log .= "Error al descargar: $remote_path\n";
                file_put_contents($log_file, $log, FILE_APPEND);
                echo "Error al descargar: $remote_path\n";
            }
        }
    }
}

// Descargar el archivo o directorio remoto
descargar($conn_id, $remote_path, $local_path);

// Cerrar la conexión FTP
ftp_close($conn_id);

// Guardar log
$log .= "Fin de la descarga FTP: " . date('Y-m-d H:i:s') . "\n\n";
file_put_contents($log_file, $log, FILE_APPEND);
echo  "Fin de la descarga FTP: " . date('Y-m-d H:i:s') . "\n\n";
