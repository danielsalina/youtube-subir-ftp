<?php
// Datos de configuración FTP
$ftp_server = "11.11.11.11"; // Cambia por la dirección de tu servidor FTP
$ftp_username = "user"; // Cambia por tu nombre de usuario FTP
$ftp_password = "pass"; // Cambia por tu contraseña FTP

// Archivo local a subir
$file_local = '../uploads/archivo.txt'; // Ruta relativa al archivo en tu servidor local
$file_remoto = 'archivo_remoto.txt'; // Nombre del archivo en el servidor FTP

// Ruta del log
$log_file = '../logs/ftp_log.txt';

// Iniciar log
$log = "Inicio de la subida FTP: " . date('Y-m-d H:i:s') . "\n";

// Conectar al servidor FTP
$conn_id = ftp_connect($ftp_server);
if (!$conn_id) {
    $log .= "No se pudo conectar al servidor FTP\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    die("No se pudo conectar al servidor FTP");
}

// Iniciar sesión con las credenciales proporcionadas
if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    $log .= "Conectado a $ftp_server como $ftp_username.\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    echo "Conectado a $ftp_server como $ftp_username.<br>";
} else {
    $log .= "No se pudo iniciar sesión en el servidor FTP.\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    die("No se pudo iniciar sesión en el servidor FTP.");
}

// Cambiar a modo pasivo (opcional, pero recomendado para evitar problemas con firewalls)
ftp_pasv($conn_id, true);

// Intentar subir el archivo
if (ftp_put($conn_id, $file_remoto, $file_local, FTP_ASCII)) {
    $log .= "Archivo subido exitosamente como $file_remoto\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    echo "Archivo subido exitosamente como $file_remoto.<br>";
} else {
    $log .= "Hubo un problema al subir el archivo.<br>";
    file_put_contents($log_file, $log, FILE_APPEND);
    echo "Hubo un problema al subir el archivo.<br>";
}

// Cerrar la conexión FTP
ftp_close($conn_id);

// Guardar log
$log .= "Fin de la descarga FTP: " . date('Y-m-d H:i:s') . "\n\n";
file_put_contents($log_file, $log, FILE_APPEND);
echo  "Fin de la descarga FTP: " . date('Y-m-d H:i:s') . "\n\n";
