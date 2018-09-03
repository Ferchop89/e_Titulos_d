$(document).ready(function(){

  // Json que nos arroja el último corte registrado en Revisiones.
  $.get('fechaCorte', null, function(data)
  {
      //Si no existen listas, la fecha inicial será la del dia actual...

      if (data!="") {
        var pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
        var dt = new Date(data.replace(pattern,'$2-$1-$3'));
      } else {
        var dt = new Date();
      }

      // var dt = new Date(data.replace(pattern,'$2-$1-$3'));

      // Si no se ha especificado una fecha (como en la primera carga) la establecemos.
      if ($('#datepicker').val()=="") {
          $('#datepicker').datepicker('setDate', dt);
      }
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
      defaultDate: new Date('07/01/2018'),
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      yearSuffix: ''
    };
  $.datepicker.setDefaults($.datepicker.regional['es']);
  $('select:not(.normal)').each(function () {
      $(this).select2({
          dropdownParent: $(this).parent()
      });
  });

  // datepicker
  var fechas = new Array();
  var rango = new Array();

  $.get('grupoListas', null, function( data)
  {
    $.each( data, function( key, val) {
        fechas.push({ fecha: val.corte, total: [val.listas,val.cuenta] });
     });
     // alert(fechas[0].total[0] + " ** " + fechas[0].fecha);
     limites(fechas);
  });

  function inArray(item, arreglo)
  {
      var length = arreglo.length;
      for(var i = 0; i < length; i++) {
          if(arreglo[i].fecha == item)
              return arreglo[i].total[1]+'-'+arreglo[i].total[0];
      }
      return '';
  }
  function fechaStr(date)
  {
    var dd = date.getDate(); dd = dd < 10 ? '0' + dd : dd;
    var mm = date.getMonth()+1; mm = mm < 10 ? '0' + mm : mm;
    var yyyy = date.getFullYear();
    var fecha = dd + '.' + mm + '.' + yyyy;
    return fecha;
  }
  function limites(arreglo)
  {
    var length = arreglo.length;
    var inferior = superior = arreglo[0].total[1];
    for(var i = 0; i < length; i++) {
      inferior = inferior <= arreglo[i].total[1] ?  inferior : arreglo[i].total[1];
      superior = superior <= arreglo[i].total[1] ?  arreglo[i].total[1] : superior;
    }
    rango.push(inferior);
    rango.push(superior);
  }
  $('#datepicker').datepicker(
  {
      beforeShowDay: function( date )
      {
            // var xvalor = new Array();
            xvalor = inArray(fechaStr(date), fechas).split('-');
                if ( xvalor[0] != '' ) {
                  // Se aplica el logaritmo para matizar las diferencias
                  rango100 = 100*( Math.log(xvalor[0]) - Math.log(rango[0]) ) /
                                 ( Math.log( rango[1]) - Math.log(rango[0]) );
                  // si son mas de un listado, se especifica, sino, solo las solicitudes
                  if (xvalor[1] > 1) {
                      estatis = xvalor[0] + ' solicitudes; ' + xvalor[1] + ' listados.';
                  } else {
                      estatis = xvalor[0] + ' solicitudes;';
                  }

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
                          return [true, "event", estatis];
                      }
                  } else {
                    return [true, '', ''];  }
       }
  });
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
  if ($('#FacEsc').is(':checked') != true) {
        $('#xproc').fadeOut('slow');
      }
  $('#FacEsc').change(function(){
      if (this.checked) {
          $('#xproc').fadeIn('slow');
      }
      else {
          $('#xproc').fadeOut('slow');
      }
    });
  // $( "#datepicker" ).datepicker( "hide" );

  // Fijamos la fecha inicial de corte en el DatePicker
  // $('#datepicker').datepicker('setDate', today)
 // $( ".selector" ).datepicker( "refresh" );
});
