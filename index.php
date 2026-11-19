<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';


if (!isset($_SESSION['csrf'])) {
   $_SESSION['csrf'] = bin2hex(random_bytes(32));
}


function limpiar($valor) {
   return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}


$mensaje = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


   if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
       $error = 'La solicitud no es válida.';
   } else {


       $accion = $_POST['accion'] ?? '';
       $reportes = obtenerReportes();


       if ($accion === 'registrar') {


           $nombre = trim($_POST['nombre'] ?? '');
           $tipoUsuario = trim($_POST['tipo_usuario'] ?? '');
           $area = trim($_POST['area'] ?? '');
           $problema = trim($_POST['problema'] ?? '');
           $descripcion = trim($_POST['descripcion'] ?? '');


           if ($nombre && $tipoUsuario && $area && $problema && $descripcion) {


               $reportes[] = [
                   'id' => uniqid('REP-'),
                   'nombre' => $nombre,
                   'tipo_usuario' => $tipoUsuario,
                   'area' => $area,
                   'problema' => $problema,
                   'descripcion' => $descripcion,
                   'estado' => 'Pendiente',
                   'fecha' => date('d/m/Y H:i')
               ];


               if (guardarReportes($reportes)) {
                   $mensaje = 'El reporte se registró correctamente.';
               } else {
                   $error = 'No se pudo guardar el reporte.';
               }


           } else {
               $error = 'Completa todos los campos.';
           }
       }


       if ($accion === 'actualizar') {


           $id = $_POST['id'] ?? '';
           $estado = $_POST['estado'] ?? '';


           if (in_array($estado, ['Pendiente', 'En revisión', 'Resuelto'], true)) {


               foreach ($reportes as &$reporte) {
                   if (($reporte['id'] ?? '') === $id) {
                       $reporte['estado'] = $estado;
                       break;
                   }
               }


               unset($reporte);


               if (guardarReportes($reportes)) {
                   $mensaje = 'El reporte se actualizó correctamente.';
               } else {
                   $error = 'No se pudo actualizar el reporte.';
               }
           }
       }


       if ($accion === 'eliminar') {


           $id = $_POST['id'] ?? '';


           $reportes = array_values(array_filter(
               $reportes,
               fn($r) => ($r['id'] ?? '') !== $id
           ));


           if (guardarReportes($reportes)) {
               $mensaje = 'El reporte se eliminó correctamente.';
           } else {
               $error = 'No se pudo eliminar el reporte.';
           }
       }
   }
}


$reportes = obtenerReportes();


$total = count($reportes);


$pendientes = count(array_filter(
   $reportes,
   fn($r) => ($r['estado'] ?? '') === 'Pendiente'
));


$revision = count(array_filter(
   $reportes,
   fn($r) => ($r['estado'] ?? '') === 'En revisión'
));


$resueltos = count(array_filter(
   $reportes,
   fn($r) => ($r['estado'] ?? '') === 'Resuelto'
));


$seccion = $_GET['seccion'] ?? 'inicio';


$secciones = ['inicio', 'registrar', 'consultar', 'seguimiento', 'acerca'];


if (!in_array($seccion, $secciones, true)) {
   $seccion = 'inicio';
}
?>


<!DOCTYPE html>
<html lang="es">


<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Sistema de Reportes Escolares</title>


<style>


:root {
   --vino: #762d4d;
   --vino-oscuro: #542038;
   --rosa: #f5e7ec;
   --rosa-claro: #fcf8fa;
   --dorado: #b89a65;
   --texto: #40363b;
   --gris: #81777d;
   --blanco: #ffffff;
   --borde: #eee3e8;
}


* {
   margin: 0;
   padding: 0;
   box-sizing: border-box;
   font-family: 'Segoe UI', Arial, sans-serif;
}


body {
   background: var(--rosa-claro);
   color: var(--texto);
}


/* PORTADA */


.portada {
   min-height: 100vh;
   display: flex;
   justify-content: center;
   align-items: center;
   padding: 25px;
   background:
       radial-gradient(circle at top right, #f2dce5, transparent 35%),
       linear-gradient(135deg, #fcf8fa, #f2e5eb);
}


.portada-contenido {
   width: 100%;
   max-width: 850px;
   background: white;
   border-radius: 24px;
   padding: 55px 35px;
   text-align: center;
   border: 1px solid #eadce3;
   box-shadow: 0 15px 50px rgba(87, 40, 59, .10);
}


.logo-portada {
   width: 145px;
   height: 145px;
   object-fit: contain;
   border-radius: 50%;
   margin-bottom: 25px;
   padding: 7px;
   background: white;
   border: 3px solid #ead6df;
   box-shadow: 0 7px 20px rgba(118, 45, 77, .10);
}


.portada h1 {
   color: var(--vino);
   font-size: 34px;
   margin-bottom: 13px;
}


.portada h2 {
   font-size: 15px;
   color: var(--dorado);
   font-weight: 600;
   letter-spacing: 2px;
   text-transform: uppercase;
   margin-bottom: 22px;
}


.portada p {
   max-width: 570px;
   margin: auto;
   color: var(--gris);
   line-height: 1.8;
   font-size: 15px;
}


.btn-entrar {
   display: inline-block;
   margin-top: 30px;
   padding: 15px 35px;
   background: var(--vino);
   color: white;
   border: none;
   border-radius: 10px;
   text-decoration: none;
   font-size: 14px;
   font-weight: 600;
   cursor: pointer;
   transition: .3s;
}


.btn-entrar:hover {
   background: var(--vino-oscuro);
   transform: translateY(-2px);
}


.portada-pie {
   margin-top: 35px;
   font-size: 11px;
   color: #a99ca3;
}


/* APLICACIÓN */


#aplicacion {
   display: none;
}


.layout {
   display: flex;
   min-height: 100vh;
}


.sidebar {
   width: 255px;
   background: white;
   border-right: 1px solid var(--borde);
   padding: 25px 18px;
   position: fixed;
   height: 100vh;
}


.logo-area {
   display: flex;
   align-items: center;
   gap: 12px;
   padding: 8px 5px 28px;
   border-bottom: 1px solid var(--borde);
   margin-bottom: 25px;
}


.logo-area img {
   width: 58px;
   height: 58px;
   object-fit: contain;
   border-radius: 50%;
}


.logo-area h2 {
   color: var(--vino);
   font-size: 15px;
}


.logo-area span {
   font-size: 11px;
   color: var(--gris);
}


.menu-titulo {
   color: #a69ba1;
   font-size: 10px;
   font-weight: bold;
   letter-spacing: 1.5px;
   margin: 20px 10px 12px;
}


.menu a {
   display: flex;
   align-items: center;
   gap: 12px;
   padding: 13px;
   margin-bottom: 7px;
   border-radius: 10px;
   color: #6f6269;
   text-decoration: none;
   font-size: 14px;
   transition: .2s;
}


.menu a:hover,
.menu a.activo {
   background: var(--rosa);
   color: var(--vino);
   font-weight: 600;
}


.icono {
   width: 22px;
   text-align: center;
   font-size: 17px;
}


.main {
   margin-left: 255px;
   width: calc(100% - 255px);
   padding: 35px 45px;
}


.encabezado {
   display: flex;
   justify-content: space-between;
   align-items: center;
   margin-bottom: 30px;
}


.encabezado h1 {
   font-size: 26px;
   color: var(--vino);
}


.encabezado p {
   color: var(--gris);
   font-size: 13px;
   margin-top: 5px;
}


.fecha {
   background: white;
   padding: 12px 18px;
   border-radius: 10px;
   color: var(--vino);
   border: 1px solid var(--borde);
   font-size: 13px;
}


.tarjetas {
   display: grid;
   grid-template-columns: repeat(4, 1fr);
   gap: 18px;
   margin-bottom: 30px;
}


.tarjeta {
   background: white;
   padding: 22px;
   border-radius: 15px;
   border: 1px solid var(--borde);
   box-shadow: 0 4px 15px rgba(80, 40, 55, .035);
}


.etiqueta {
   color: var(--gris);
   font-size: 13px;
}


.tarjeta h2 {
   font-size: 30px;
   color: var(--vino);
   margin-top: 12px;
}


.simbolo {
   font-size: 23px;
   margin-bottom: 13px;
}


.panel {
   background: white;
   border: 1px solid var(--borde);
   border-radius: 15px;
   padding: 25px;
   margin-bottom: 25px;
   box-shadow: 0 4px 15px rgba(80, 40, 55, .035);
}


.panel h2 {
   color: var(--vino);
   font-size: 19px;
   margin-bottom: 8px;
}


.subtitulo {
   color: var(--gris);
   font-size: 13px;
   margin-bottom: 22px;
}


.btn {
   border: none;
   padding: 12px 18px;
   border-radius: 9px;
   background: var(--vino);
   color: white;
   cursor: pointer;
   text-decoration: none;
   font-size: 13px;
   display: inline-block;
}


.btn:hover {
   background: var(--vino-oscuro);
}


.btn-eliminar {
   background: #a94455;
}


.btn-pequeno {
   padding: 8px 11px;
   font-size: 12px;
}


.formulario {
   display: grid;
   grid-template-columns: repeat(2, 1fr);
   gap: 18px;
}


.campo {
   display: flex;
   flex-direction: column;
   gap: 8px;
}


.campo.completo {
   grid-column: 1 / -1;
}


.campo label {
   font-size: 13px;
   font-weight: 600;
   color: #5f5058;
}


.campo input,
.campo select,
.campo textarea {
   width: 100%;
   padding: 12px;
   border: 1px solid #e8dce2;
   border-radius: 9px;
   background: white;
   outline: none;
   color: var(--texto);
   font-size: 13px;
}


.campo textarea {
   min-height: 110px;
   resize: vertical;
}


.campo input:focus,
.campo select:focus,
.campo textarea:focus {
   border-color: var(--dorado);
}


.tabla-contenedor {
   width: 100%;
   overflow-x: auto;
}


table {
   width: 100%;
   border-collapse: collapse;
   min-width: 750px;
}


th {
   background: #faf3f6;
   color: var(--vino);
   font-size: 12px;
   text-align: left;
   padding: 14px;
}


td {
   padding: 14px;
   border-bottom: 1px solid var(--borde);
   font-size: 12px;
}


.estado {
   padding: 6px 10px;
   border-radius: 20px;
   font-size: 11px;
   font-weight: 600;
   display: inline-block;
}


.Pendiente {
   background: #fff0dc;
   color: #9b6722;
}


.En-revisión {
   background: #e9e5fb;
   color: #6552a0;
}


.Resuelto {
   background: #e0f3e9;
   color: #347650;
}


.alerta {
   padding: 13px 16px;
   border-radius: 9px;
   margin-bottom: 20px;
   font-size: 13px;
}


.exito {
   background: #e5f5eb;
   color: #267344;
}


.error {
   background: #fbe8e8;
   color: #a52c37;
}


.vacio {
   text-align: center;
   padding: 35px;
   color: var(--gris);
}


.vacio span {
   display: block;
   font-size: 35px;
   margin-bottom: 10px;
}


.bienvenida {
   background: linear-gradient(120deg, #762d4d, #a76b80);
   color: white;
   padding: 35px;
   border-radius: 17px;
   margin-bottom: 25px;
}


.bienvenida h2 {
   font-size: 25px;
   margin-bottom: 10px;
}


.bienvenida p {
   font-size: 14px;
   line-height: 1.7;
   max-width: 650px;
}


.bienvenida .btn {
   background: white;
   color: var(--vino);
   margin-top: 20px;
}


.pasos {
   display: grid;
   grid-template-columns: repeat(3, 1fr);
   gap: 15px;
}


.paso {
   background: #fcf8fa;
   border: 1px solid var(--borde);
   padding: 20px;
   border-radius: 12px;
}


.paso strong {
   color: var(--vino);
   display: block;
   margin-bottom: 8px;
}


.paso p {
   font-size: 12px;
   color: var(--gris);
   line-height: 1.6;
}


.pie {
   text-align: center;
   padding: 20px;
   color: #a69ba1;
   font-size: 11px;
}


@media(max-width: 950px) {
   .sidebar {
       width: 210px;
   }


   .main {
       margin-left: 210px;
       width: calc(100% - 210px);
       padding: 25px;
   }


   .tarjetas {
       grid-template-columns: repeat(2, 1fr);
   }
}


@media(max-width: 650px) {
   .layout {
       display: block;
   }


   .sidebar {
       position: relative;
       width: 100%;
       height: auto;
   }


   .menu {
       display: flex;
       flex-wrap: wrap;
   }


   .menu-titulo {
       width: 100%;
   }


   .main {
       margin: 0;
       width: 100%;
       padding: 20px;
   }


   .tarjetas,
   .pasos,
   .formulario {
       grid-template-columns: 1fr;
   }


   .campo.completo {
       grid-column: auto;
   }


   .encabezado {
       align-items: flex-start;
       flex-direction: column;
       gap: 12px;
   }


   .portada-contenido {
       padding: 35px 20px;
   }


   .portada h1 {
       font-size: 26px;
   }
}


</style>
</head>


<body>


<!-- PORTADA DE BIENVENIDA -->


<section class="portada" id="portada">


   <div class="portada-contenido">


       <img src="imagen.png" alt="Logo" class="logo-portada">


       <h2>Bienvenido</h2>


       <h1>Sistema de Reportes Escolares</h1>


       <p>
           Una herramienta para registrar, consultar y dar seguimiento
           a los problemas que se presenten en las diferentes instalaciones
           de la institución educativa.
       </p>


       <button class="btn-entrar" onclick="entrarSistema()">
           Entrar al sistema →
       </button>


       <div class="portada-pie">
           Gestión y seguimiento de incidencias escolares
       </div>


   </div>


</section>




<!-- APLICACIÓN PRINCIPAL -->


<div id="aplicacion">


<div class="layout">


<aside class="sidebar">


   <div class="logo-area">


       <img src="imagen.png" alt="Logo">


       <div>
           <h2>Sistema de Reportes</h2>
           <span>Gestión escolar</span>
       </div>


   </div>


   <div class="menu-titulo">MENÚ PRINCIPAL</div>


   <nav class="menu">


       <a href="?seccion=inicio" class="<?= $seccion === 'inicio' ? 'activo' : '' ?>">
           <span class="icono">⌂</span> Inicio
       </a>


       <a href="?seccion=registrar" class="<?= $seccion === 'registrar' ? 'activo' : '' ?>">
           <span class="icono">＋</span> Registrar reporte
       </a>


       <a href="?seccion=consultar" class="<?= $seccion === 'consultar' ? 'activo' : '' ?>">
           <span class="icono">▤</span> Consultar reportes
       </a>


       <a href="?seccion=seguimiento" class="<?= $seccion === 'seguimiento' ? 'activo' : '' ?>">
           <span class="icono">◷</span> Seguimiento
       </a>


       <a href="?seccion=acerca" class="<?= $seccion === 'acerca' ? 'activo' : '' ?>">
           <span class="icono">ⓘ</span> Acerca del sistema
       </a>


   </nav>


</aside>




<main class="main">


<header class="encabezado">


   <div>


       <h1>
       <?php
       $titulos = [
           'inicio' => 'Panel principal',
           'registrar' => 'Registrar reporte',
           'consultar' => 'Consultar reportes',
           'seguimiento' => 'Seguimiento de reportes',
           'acerca' => 'Acerca del sistema'
       ];


       echo $titulos[$seccion];
       ?>
       </h1>


       <p>Registro y seguimiento de incidencias en las instalaciones escolares.</p>


   </div>


   <div class="fecha">
       📅 <?= date('d/m/Y') ?>
   </div>


</header>




<?php if ($mensaje): ?>
<div class="alerta exito"><?= limpiar($mensaje) ?></div>
<?php endif; ?>


<?php if ($error): ?>
<div class="alerta error"><?= limpiar($error) ?></div>
<?php endif; ?>




<?php if ($seccion === 'inicio'): ?>


<section class="bienvenida">


   <h2>Bienvenido al sistema de reportes</h2>


   <p>
       Administra los reportes de las diferentes áreas de la escuela
       desde un solo lugar. Registra incidencias y consulta su seguimiento.
   </p>


   <a href="?seccion=registrar" class="btn">
       Registrar un reporte →
   </a>


</section>




<section class="tarjetas">


   <div class="tarjeta">
       <div class="simbolo">▤</div>
       <div class="etiqueta">Total de reportes</div>
       <h2><?= $total ?></h2>
   </div>


   <div class="tarjeta">
       <div class="simbolo">◷</div>
       <div class="etiqueta">Pendientes</div>
       <h2><?= $pendientes ?></h2>
   </div>


   <div class="tarjeta">
       <div class="simbolo">◉</div>
       <div class="etiqueta">En revisión</div>
       <h2><?= $revision ?></h2>
   </div>


   <div class="tarjeta">
       <div class="simbolo">✓</div>
       <div class="etiqueta">Resueltos</div>
       <h2><?= $resueltos ?></h2>
   </div>


</section>




<section class="panel">


   <h2>Actividad reciente</h2>


   <p class="subtitulo">
       Últimos reportes registrados en el sistema.
   </p>


   <?php if (empty($reportes)): ?>


       <div class="vacio">
           <span>▤</span>
           Todavía no hay reportes registrados.
       </div>


   <?php else: ?>


   <div class="tabla-contenedor">


   <table>


       <thead>
           <tr>
               <th>Fecha</th>
               <th>Nombre</th>
               <th>Área</th>
               <th>Problema</th>
               <th>Estado</th>
           </tr>
       </thead>


       <tbody>


       <?php foreach (array_slice(array_reverse($reportes), 0, 5) as $r): ?>


           <tr>
               <td><?= limpiar($r['fecha']) ?></td>
               <td><?= limpiar($r['nombre']) ?></td>
               <td><?= limpiar($r['area']) ?></td>
               <td><?= limpiar($r['problema']) ?></td>
               <td>
                   <span class="estado <?= limpiar(str_replace(' ', '-', $r['estado'])) ?>">
                       <?= limpiar($r['estado']) ?>
                   </span>
               </td>
           </tr>


       <?php endforeach; ?>


       </tbody>


   </table>


   </div>


   <?php endif; ?>


</section>




<?php elseif ($seccion === 'registrar'): ?>




<section class="panel">


   <h2>Nuevo reporte</h2>


   <p class="subtitulo">
       Completa los datos para registrar una incidencia.
   </p>


   <form method="POST" class="formulario">


       <input type="hidden" name="csrf" value="<?= limpiar($_SESSION['csrf']) ?>">


       <input type="hidden" name="accion" value="registrar">


       <div class="campo">
           <label>Nombre de quien reporta</label>
           <input type="text" name="nombre" required maxlength="100" placeholder="Escribe tu nombre">
       </div>


       <div class="campo">
           <label>Tipo de usuario</label>


           <select name="tipo_usuario" required>
               <option value="">Selecciona una opción</option>
               <option>Estudiante</option>
               <option>Docente</option>
               <option>Administrativo</option>
               <option>Personal de mantenimiento</option>
           </select>
       </div>


       <div class="campo">
           <label>Área de la escuela</label>


           <select name="area" required>
               <option value="">Selecciona el área</option>
               <option>Baños</option>
               <option>Aulas</option>
               <option>Laboratorios</option>
               <option>Biblioteca</option>
               <option>Pasillos</option>
               <option>Patio</option>
               <option>Áreas verdes</option>
               <option>Cafetería</option>
               <option>Oficinas</option>
               <option>Instalaciones deportivas</option>
               <option>Otra área</option>
           </select>
       </div>


       <div class="campo">
           <label>Tipo de problema</label>


           <select name="problema" required>
               <option value="">Selecciona el problema</option>
               <option>Falta de limpieza</option>
               <option>Falla eléctrica</option>
               <option>Fuga de agua</option>
               <option>Daño en mobiliario</option>
               <option>Falta de materiales</option>
               <option>Problema de infraestructura</option>
               <option>Otro problema</option>
           </select>
       </div>


       <div class="campo completo">
           <label>Descripción del problema</label>


           <textarea name="descripcion" required maxlength="1000"
           placeholder="Describe lo que sucede y dónde se encuentra..."></textarea>
       </div>


       <div class="campo completo">
           <button type="submit" class="btn">
               Guardar reporte
           </button>
       </div>


   </form>


</section>




<?php elseif ($seccion === 'consultar'): ?>




<section class="panel">


   <h2>Reportes registrados</h2>


   <p class="subtitulo">
       Consulta todos los reportes almacenados.
   </p>


   <?php if (empty($reportes)): ?>


       <div class="vacio">
           <span>▤</span>
           No hay reportes para mostrar.
       </div>


   <?php else: ?>


   <div class="tabla-contenedor">


   <table>


       <thead>
           <tr>
               <th>Fecha</th>
               <th>Nombre</th>
               <th>Usuario</th>
               <th>Área</th>
               <th>Problema</th>
               <th>Descripción</th>
               <th>Estado</th>
           </tr>
       </thead>


       <tbody>


       <?php foreach (array_reverse($reportes) as $r): ?>


           <tr>
               <td><?= limpiar($r['fecha']) ?></td>
               <td><?= limpiar($r['nombre']) ?></td>
               <td><?= limpiar($r['tipo_usuario']) ?></td>
               <td><?= limpiar($r['area']) ?></td>
               <td><?= limpiar($r['problema']) ?></td>
               <td><?= limpiar($r['descripcion']) ?></td>
               <td>
                   <span class="estado <?= limpiar(str_replace(' ', '-', $r['estado'])) ?>">
                       <?= limpiar($r['estado']) ?>
                   </span>
               </td>
           </tr>


       <?php endforeach; ?>


       </tbody>


   </table>


   </div>


   <?php endif; ?>


</section>




<?php elseif ($seccion === 'seguimiento'): ?>




<section class="panel">


   <h2>Administrar reportes</h2>


   <p class="subtitulo">
       Actualiza el estado o elimina un reporte.
   </p>


   <?php if (empty($reportes)): ?>


       <div class="vacio">
           <span>◷</span>
           No hay reportes pendientes de seguimiento.
       </div>


   <?php else: ?>


       <?php foreach (array_reverse($reportes) as $r): ?>


       <div style="border:1px solid var(--borde); border-radius:12px; padding:18px; margin-bottom:15px;">


           <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">


               <div>


                   <strong style="color:var(--vino);">
                       <?= limpiar($r['area']) ?> — <?= limpiar($r['problema']) ?>
                   </strong>


                   <p style="font-size:12px; color:var(--gris); margin-top:7px;">
                       Reportado por <?= limpiar($r['nombre']) ?> · <?= limpiar($r['fecha']) ?>
                   </p>


                   <p style="font-size:13px; margin-top:9px;">
                       <?= limpiar($r['descripcion']) ?>
                   </p>


               </div>


               <span class="estado <?= limpiar(str_replace(' ', '-', $r['estado'])) ?>">
                   <?= limpiar($r['estado']) ?>
               </span>


           </div>


           <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:18px;">


               <form method="POST">


                   <input type="hidden" name="csrf" value="<?= limpiar($_SESSION['csrf']) ?>">


                   <input type="hidden" name="accion" value="actualizar">


                   <input type="hidden" name="id" value="<?= limpiar($r['id']) ?>">


                   <select name="estado" style="padding:9px; border:1px solid var(--borde); border-radius:8px;">


                       <option <?= $r['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>


                       <option <?= $r['estado'] === 'En revisión' ? 'selected' : '' ?>>En revisión</option>


                       <option <?= $r['estado'] === 'Resuelto' ? 'selected' : '' ?>>Resuelto</option>


                   </select>


                   <button class="btn btn-pequeno" type="submit">
                       Actualizar estado
                   </button>


               </form>


               <form method="POST" onsubmit="return confirm('¿Deseas eliminar este reporte?');">


                   <input type="hidden" name="csrf" value="<?= limpiar($_SESSION['csrf']) ?>">


                   <input type="hidden" name="accion" value="eliminar">


                   <input type="hidden" name="id" value="<?= limpiar($r['id']) ?>">


                   <button class="btn btn-eliminar btn-pequeno" type="submit">
                       Eliminar
                   </button>


               </form>


           </div>


       </div>


       <?php endforeach; ?>


   <?php endif; ?>


</section>




<?php elseif ($seccion === 'acerca'): ?>




<section class="panel">


   <h2>Acerca del sistema</h2>


   <p class="subtitulo">
       Información general de la aplicación.
   </p>


   <p style="line-height:1.8; font-size:14px;">


       El Sistema de Reportes Escolares es una aplicación web
       desarrollada para facilitar el registro y seguimiento de
       problemas que puedan presentarse en las diferentes áreas
       de una institución educativa.


   </p>


   <br>


   <div class="pasos">


       <div class="paso">
           <strong>01. Registro</strong>
           <p>Permite ingresar los datos y la descripción de una incidencia.</p>
       </div>


       <div class="paso">
           <strong>02. Consulta</strong>
           <p>Facilita la visualización de los reportes registrados.</p>
       </div>


       <div class="paso">
           <strong>03. Seguimiento</strong>
           <p>Permite actualizar el estado de cada reporte hasta su resolución.</p>
       </div>


   </div>


</section>




<?php endif; ?>




<footer class="pie">
   Sistema de Reportes Escolares · Gestión y seguimiento de incidencias
</footer>


</main>


</div>


</div>




<script>
function entrarSistema() {
   sessionStorage.setItem('sistemaIniciado', 'true');
   document.getElementById('portada').style.display = 'none';
   document.getElementById('aplicacion').style.display = 'block';
}


window.addEventListener('DOMContentLoaded', function() {


   const portada = document.getElementById('portada');
   const aplicacion = document.getElementById('aplicacion');


   if (sessionStorage.getItem('sistemaIniciado') === 'true') {
       portada.style.display = 'none';
       aplicacion.style.display = 'block';
   } else {
       portada.style.display = 'flex';
       aplicacion.style.display = 'none';
   }


});
</script>
