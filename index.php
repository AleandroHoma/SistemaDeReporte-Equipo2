<?php require_once __DIR__ . '/reportes.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sistema de Reportes Escolares</title>

<link rel="stylesheet" href="css/estilo.css">
</head>

<body>

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

<script src="js/script.js"></script>
</body>
</html>
