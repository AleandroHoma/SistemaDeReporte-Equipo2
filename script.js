document.addEventListener("DOMContentLoaded", function () {


   console.log("Sistema de reportes inicializado.");


   // Validar formulario
   const formulario = document.getElementById("formReporte");


   if (formulario) {
       formulario.addEventListener("submit", function(event) {


           if (!formulario.checkValidity()) {
               event.preventDefault();
               alert("Por favor, completa todos los campos obligatorios.");
               formulario.reportValidity();
           }


       });
   }


   // Confirmar eliminación
   const botonesEliminar = document.querySelectorAll(".btn-eliminar");


   botonesEliminar.forEach(function(boton) {
       boton.addEventListener("click", function(event) {


           const confirmar = confirm(
               "¿Estás segura de que deseas eliminar este reporte?"
           );


           if (!confirmar) {
               event.preventDefault();
           }


       });
   });


   // Resaltar filas seleccionadas
   const filas = document.querySelectorAll("tbody tr");


   filas.forEach(function(fila) {
       fila.addEventListener("click", function() {


           filas.forEach(function(otraFila) {
               otraFila.style.outline = "none";
           });


           this.style.outline = "2px solid #31999a";
       });
   });


});document.addEventListener("DOMContentLoaded", function () {


   console.log("Sistema de reportes inicializado.");


   // Validar formulario
   const formulario = document.getElementById("formReporte");


   if (formulario) {
       formulario.addEventListener("submit", function(event) {


           if (!formulario.checkValidity()) {
               event.preventDefault();
               alert("Por favor, completa todos los campos obligatorios.");
               formulario.reportValidity();
           }


       });
   }


   // Confirmar eliminación
   const botonesEliminar = document.querySelectorAll(".btn-eliminar");


   botonesEliminar.forEach(function(boton) {
       boton.addEventListener("click", function(event) {


           const confirmar = confirm(
               "¿Estás segura de que deseas eliminar este reporte?"
           );


           if (!confirmar) {
               event.preventDefault();
           }


       });
   });


   // Resaltar filas seleccionadas
   const filas = document.querySelectorAll("tbody tr");


   filas.forEach(function(fila) {
       fila.addEventListener("click", function() {


           filas.forEach(function(otraFila) {
               otraFila.style.outline = "none";
           });


           this.style.outline = "2px solid #31999a";
       });
   });


});

























