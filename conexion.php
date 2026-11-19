<?php


// Guardar los reportes en un archivo JSON temporal.
$archivo = sys_get_temp_dir() . "/reportes_banos_escolares.json";


if (!file_exists($archivo)) {
   file_put_contents($archivo, "[]");
}


// Obtener todos los reportes.
function obtenerReportes() {
   global $archivo;


   if (!file_exists($archivo)) {
       return [];
   }


   $contenido = file_get_contents($archivo);
   $datos = json_decode($contenido, true);


   return is_array($datos) ? $datos : [];
}


// Guardar los reportes.
function guardarReportes($reportes) {
   global $archivo;


   $json = json_encode(
       array_values($reportes),
       JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
   );


   if ($json === false) {
       return false;
   }


   return file_put_contents($archivo, $json, LOCK_EX) !== false;
}


?>