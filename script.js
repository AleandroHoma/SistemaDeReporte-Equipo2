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
