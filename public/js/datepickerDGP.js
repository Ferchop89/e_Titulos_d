$(document).ready(function(){

   function reset() {
     document.getElementById('datepicker').value = "";
   }

   $("button").click(function(){
     document.getElementById('datepicker').value = "";
    });

  var date = new Date();
  var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

  $.datepicker.regional['es'] =
  {
      closeText: 'Cerrar',
      prevText: '< Ant',
      nextText: 'Sig >',
      currentText: 'Hoy',
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
      dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
      dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
      dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
      weekHeader: 'Sm',
      dateFormat: 'dd/mm/yy',
      // defaultDate: new Date('07/01/2018'),
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      yearSuffix: ''
    };
  $.datepicker.setDefaults($.datepicker.regional['es']);

  // datepicker
  var fechas = new Array();
  var rango = new Array();

  $.get('cedulasDGP', null, function( data)
  {
    $.each( data, function( key, val) {
        fechas.push({fecha: val});
     });
  });

  function inArray(item, arreglo)
  {
      var length = arreglo.length;
      for(var i = 0; i < length; i++) {
          if(arreglo[i].fecha == item){
             return 1;
          }

      }
      return 0
  }
  function fechaStr(date)
  {
    var dd = date.getDate(); dd = dd < 10 ? '0' + dd : dd;
    var mm = date.getMonth()+1; mm = mm < 10 ? '0' + mm : mm;
    var yyyy = date.getFullYear();
    var fecha = dd + '-' + mm + '-' + yyyy;
    return fecha;
  }
  $('#datepicker').datepicker(
  {
      /*Boton para eliminar fecha*/
      showButtonPanel: true,
      /*Botones para años y meses*/
      changeMonth: true,
      changeYear: true,
      beforeShowDay: function( date )
      {
         // xvalor ee
            xvalor = inArray(fechaStr(date), fechas);
            if(xvalor)
            {
               return [true, 'event4', ''];
            }
            else {
               return [false, '', ''];
            }

       }
  });
});
