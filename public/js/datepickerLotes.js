$(document).ready(function(){

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

  $.get('lotes', null, function( data)
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

  // $('select:not(.normal)').each(function () {
  //     $(this).select2({
  //         dropdownParent: $(this).parent()
  //     });
  // });

  $('#search').keyup(function()
  {
      // Procedimiento para buscar  y resaltar en numero de cuenta
      textocta = $(this).val();
      cuentas = $( "p.matriculas" ).toArray();
      if (textocta.length!=0)
      {
        for (var i = 0; i < cuentas.length; i++)
        {
          contenido = $(cuentas[i]).text();
          searchExp = new RegExp(textocta, "ig");
          matches = contenido.match(searchExp);
          if (matches) {
            $(cuentas[i]).show();
            $(cuentas[i]).html(contenido.replace(searchExp,function(match){
              return "<span class='highlight'>" + match + "</span>";
              }));
          } else {
            $(cuentas[i]).html(contenido);$(cuentas[i]).hide();
          }
        }
      } else {
        for (var i = 0; i < cuentas.length; i++)
          {
            contenido = $(cuentas[i]).text();
            $(cuentas[i]).html(contenido);$(cuentas[i]).show();
          }
      }
    });
});
