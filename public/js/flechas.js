//Si se selecciona flecha derecha
function f_der(){
	var actual;
	var total = parseInt($("[id='seleccionar']").val());
	for(actual = 0; actual<total; actual++){
		if($("[id="+actual.toString()+"]").css('display') != 'none'){ //Acción si el elemento es visible
			var oculta = actual.toString();
			var muestra = ((actual+1)%total).toString();
			document.getElementById(oculta).style.display = "none";//Ocultamos el actual
			document.getElementById(muestra).style.display = "block";//Hacemos visible el siguiente
			actual = total; //Terminamos con el ciclo
		}
	}
}

//Si se selecciona flecha izquierda
function f_izq(){
	var actual;
	var total = parseInt($("[id='seleccionar']").val());
	for(actual = 0; actual<total; actual++){
		if($("[id="+actual.toString()+"]").css('display') != 'none'){// Acción si el elemento es visible
			var oculta = actual.toString();
			var muestra = (((actual-1)+total)%total).toString();
			document.getElementById(oculta).style.display = "none";//Ocultamos el actual
			document.getElementById(muestra).style.display = "block";//Hacemos visible el anterior
			actual = total; //Terminamos con el ciclo
		}
	}
}