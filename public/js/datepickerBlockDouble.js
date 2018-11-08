$(document).ready(function(){
   var date = new Date();
   var fdate = new Date();
   var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

   var fechas = new Array();
   var femision = new Array();
   var rango = new Array();
   var range = new Array();

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
       // defaultDate: new Date('10/04/2018'),
       firstDay: 1,
       isRTL: false,
       showMonthAfterYear: false,
       yearSuffix: ''
     };

   $.datepicker.setDefaults($.datepicker.regional['es']);

   $.get('../emisionTitulos/CONDOC', null, function( datos)
   {
     $.each( datos, function( fec, val) {
       var xfecha = fec;
       xfecha = xfecha.split('-');
       var dmy = xfecha[2]+'-'+xfecha[1]+'-'+xfecha[0];
       femision.push({ fecha: dmy, total: val['total'], enviadas: val['enviadas'], noenviadas: val['noenviadas'], pendientes: val['pendientes']});
      });
      // establecemos los valores limites del arreglo.
      limitesCondoc(femision);
   });

   function limitesCondoc(arreglo)
   {
     var length = arreglo.length;
     var inferior = superior = arreglo[0]['total'];
     // alert(length, inferior);
     for(var i = 0; i < length; i++) {
       inferior = (inferior <= arreglo[i]['total']) ?  inferior : arreglo[i]['total'];
       superior = (superior >= arreglo[i]['total']) ?  superior : arreglo[i]['total'];
     }
     range.push(inferior);
     range.push(superior);
   }

   function gradiente(fdate, rangoz)
   {
      xvalor = inArrayCondoc(fechaStr(fdate), femision).split("*");
      if ( xvalor[1] != '' ) {
         if(rangoz[0]!=rangoz[1]){
            rango100 = Math.round( 100*( xvalor[1] - rangoz[0] ) /
               ( rangoz[1] - rangoz[0] ) );
            // rango100 = Math.round( 100*( Math.log(xvalor[1]) - Math.log(rangoz[0]) ) /
            //                ( Math.log( rangoz[1]) - Math.log(rangoz[0]) ) );
         }
         else{
            rango100 = 100;
         }
      }
      estatis = 'Títulos: ' +xvalor[1] + '\n'+
                  'Pendientes: '+xvalor[4] + '\n'+
                  'Enviadas: '+xvalor[2] + '\n'+
                  'No enviadas: '+xvalor[3];

      return [rango100, estatis];
   }

   function inArrayCondoc(item, arreglo)
   {
      var length = arreglo.length;
      for(var i = 0; i < length; i++) {
           if(arreglo[i].fecha == item){
              return arreglo[i].fecha+'*'+
                     arreglo[i].total+'*'+
                     arreglo[i].enviadas+'*'+
                     arreglo[i].noenviadas+'*'+
                     arreglo[i].pendientes;
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
   function gradienteColor(rango100, estatis){
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
   }
   $('#datepicker').datepicker(
   {
      /*Boton para eliminar fecha*/
      showButtonPanel: true,
      /*Botones para años y meses*/
      changeMonth: true,
      changeYear: true,
      /*Boton con imagen, para cargar el calendario*/
      showOn: "button",
      buttonImage: "../../images/calendar.png",
      buttonImageOnly: true,
      buttonText: "Elige un día",
       beforeShowDay: function( fdate )
       {
         valores = gradiente(fdate, range);
         var rango100 = valores[0];
         var estatis = valores[1];
         return gradienteColor(rango100, estatis);
         // return [true, '', ''];
        }
   });


});
