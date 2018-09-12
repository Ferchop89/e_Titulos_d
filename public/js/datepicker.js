$('select:not(.normal)').each(function () {
    $(this).select2({
        dropdownParent: $(this).parent()
    });
});

// funcion para activar DatePicker
$( function() {
    $( "#datepicker" ).datepicker(
    );
    $( "#datepicker" ).datepicker( "option", "dateFormat", 'dd/mm/yy');
} );

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
