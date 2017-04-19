function invertirFecha(fecha) {
  	var dia  =  fecha.substring(0,2);
    var mes  =  fecha.substring(3,5);
    var anio =  fecha.substring(6);
	var fechaRetorno = anio+"-"+mes+"-"+dia;
	return fechaRetorno;
}

function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
    }
}

function verificaCuil(sCUIT) {
	var aMult = '5432765432';
    aMult = aMult.split('');
    
    if (sCUIT && sCUIT.length == 11) {
        aCUIT = sCUIT.split('');
        var iResult = 0;
        for(var i = 0; i <= 9; i++) {
            iResult += aCUIT[i] * aMult[i];
        }
        iResult = (iResult % 11);
		if (iResult == 1) iResult = 0;
		if (iResult != 0) iResult = 11 - iResult;
		
        if (iResult == aCUIT[10]) {
			return true;	
        } else {
			alert("CUIT INVALIDO");
			return false;
		}
    } else {
		if (sCUIT  && sCUIT.length != 11) {
			alert("CUIT INVALIDO");
			return false;	
		} else {
			alert("CUIT INVALIDO");
			return false;	
    	}
		alert("CUIT INVALIDO");
		return false;	
	}
}

function verificaCuilCuit(sCUIT) {
	var aMult = '5432765432';
    aMult = aMult.split('');
    
    if (sCUIT && sCUIT.length == 11) {
        aCUIT = sCUIT.split('');
        var iResult = 0;
        for(var i = 0; i <= 9; i++) {
            iResult += aCUIT[i] * aMult[i];
        }
        iResult = (iResult % 11);
		if (iResult == 1) iResult = 0;
		if (iResult != 0) iResult = 11 - iResult;
		
        if (iResult == aCUIT[10]) {
			return true;	
        } else {
			return false;
		}
    } else {
		if (sCUIT  && sCUIT.length != 11) {
			return false;	
		} else {
			return false;	
    	}
		return false;	
	}
}

function esFechaValida(fecha){
	if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\-\d{2}\-\d{4}$/.test(fecha)){
            alert("formato de fecha no valido (dd-mm-aaaa)");
			return (false);
        }
        var dia  =  parseInt(fecha.substring(0,2),10);
        var mes  =  parseInt(fecha.substring(3,5),10);
        var anio =  parseInt(fecha.substring(6),10);
 
    switch(mes){
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            numDias=31;
            break;
        case 4: case 6: case 9: case 11:
            numDias=30;
            break;
        case 2:
            if (comprobarSiBisisesto(anio)){ numDias=29; }else{ numDias=28 ;}
            break;
        default:
            return (false);
    }
        if (dia>numDias || dia==0){
            return (false);
        }
        return true;
    }
}

function esEntero(valor){ 
     var re = /^(-)?[0-9]*$/;
     if (!re.test(valor)) {
        return(false);
     }
     return(true);
}

function esEnteroPositivo(valor) {
	 var re = /^(-)?[0-9]*$/;
     if (!re.test(valor)) {
       return (false);
     } 
	 if (valor < 0) {
		return(false);	
	 }
     return(true);	
}

function isNumber(valor) {
	if (isNaN(valor)) {
		return(false);
	}
	return(true);
}

function isNumberPositivo(valor) {
	if (isNaN(valor)) {
		return (false);
	}
	if (valor < 0) {
		return(false);
	}
	return(true);
}

function esCorreoValido(valor) {
	if (valor != "") {
		var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
		if(valor.search(patron)!=0) {
			return false;
		}
	} else {
		return false;	
	}
	return true;
}