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

  $.get('cedulasPen2', null, function( data)
  {
    $.each( data, function( key, val) {
        fechas.push({ fecha: key, total: [val[0], val[1] ]});
     });
     // establecemos los valores limites del arreglo.
     limites(fechas);
  });

  function inArray(item, arreglo)
  {
      var length = arreglo.length;
      for(var i = 0; i < length; i++) {
          if(arreglo[i].fecha == item){
             return arreglo[i].total[0]+'-'+arreglo[i].total[1];
          }

      }
      return '';
  }
  function fechaStr(date)
  {
    var dd = date.getDate(); dd = dd < 10 ? '0' + dd : dd;
    var mm = date.getMonth()+1; mm = mm < 10 ? '0' + mm : mm;
    var yyyy = date.getFullYear();
    var fecha = dd + '-' + mm + '-' + yyyy;
    return fecha;
  }
  function limites(arreglo)
  {
    var length = arreglo.length;
    var inferior = superior = arreglo[0].total[1];
    var noEnv = new Array();
    for(var i = 0; i < length; i++) {
      noEnv.push(parseInt(arreglo[i].total[1]));
    }
    // console.log(noEnv);
    // console.log(Math.min.apply(null, noEnv));
    // console.log(Math.max.apply(null, noEnv));
    // for(var i = 0; i < length; i++) {
    //   if(arreglo[i].total[1] > 0)
    //   {
    //      inferior = (inferior <= arreglo[i].total[1]) ?  inferior : arreglo[i].total[1];
    //      superior = (superior >= arreglo[i].total[1]) ?  superior : arreglo[i].total[1];
    //   }
    // }
    rango.push(Math.min.apply(null, noEnv));
    rango.push(Math.max.apply(null, noEnv));
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
            xvalor = inArray(fechaStr(date), fechas).split('-');
            // console.log(fechaStr(date), xvalor);
                if ( xvalor[1] != '' ) {
                  // Se aplica el logaritmo para matizar las diferencias
                  if (rango[0]!=rango[1]) {
                     // Valor logaritmico para atenuar cuandl existe un valor muy grande en el arreglo
                     // rango100 = Math.round( 100*( Math.log(xvalor[1]) - Math.log(rango[0]) ) /
                     //                ( Math.log( rango[1]) - Math.log(rango[0]) ) );
                     // Valor normal cuando todos los valores fluctuan dentro de un rango pequeño
                     rango100 = Math.round( 100*(( xvalor[1] - rango[0] ) /
                                    ( rango[1] - rango[0] ) ));
                  } else {
                     rango100 = 100;
                  }
                  // console.log(rango);
                  // si son mas de un listado, se especifica, sino, solo las solicitudes
                  estatis = 'Enviadas: '+ xvalor[0] +
                           '\nNo enviados: ' +xvalor[1];
                  // asignacion de un color en función del valor de solicitudes no enviadas
                  switch(true) {
                      case (rango100>=90 && rango100<=100):
                          return [true, "event1", estatis];
                          break;
                      case (rango100>=80 && rango100<90):
                          return [true, "event2", estatis];
                          break;
                      case (rango100>=70 && rango100<80):
                           return [true, "event3", estatis];
                           break;
                      case (rango100>=60 && rango100<70):
                            return [true, "event4", estatis];
                            break;
                      case (rango100>=50 && rango100<60):
                              return [true, "event5", estatis];
                              break;
                      case (rango100>=40 && rango100<50):
                              return [true, "event6", estatis];
                              break;
                      case (rango100>=30 && rango100<40):
                              return [true, "event7", estatis];
                              break;
                      case (rango100>=20 && rango100<30):
                              return [true, "event8", estatis];
                              break;
                      case (rango100>=10 && rango100<20):
                              return [true, "event9", estatis];
                              break;
                      case (rango100>=0 && rango100<10):
                              return [true, "event10", estatis];
                              break;
                      default:
                          return [false, '','Sin información' ];
                      }
                  } else {
                    // return [false, '', 'Sin información'];
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
