<?php include_once 'include/conector.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Men� Informes S.S.S. :.</title>

</head>
<body bgcolor="#CCCCCC">
<div align="center">
	<p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
  	<h2>Informes S.S.S.</h2>
  	<table width="600" border="1" style="text-align: center">
      <tr>
	      <td width="200"> 
	        <p>DETALLE</br> X PRESENTACION</p>
	        <p><a href="informe.selectpresentacion.php?informe=detalle"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td>
  <!--   <td width="200"> 
	        <p>DETALLE RETE.</br> X PRESENTACION</p>
	        <p><a href="informe.selectpresentacion.php?informe=retenciones"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td> -->
		  <td width="200"> 
	        <p>DETALLE PAGOS</br> X PRESENTACION</p>
	        <p><a href="informe.selectpresentacion.php?informe=pagos"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td>
		  <td width="200"> 
	        <p>DETALLE</br> X DELEGACION</p>
	        <p><a href="informe.selectpresentacion.php?informe=delegacion"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td>   
     </tr>	
     <tr>
     	  <td>
		    <p>RECIBO ADEUDADOS</p>
		    <p><a href="informe.recibosadeudados.php"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td>
		  <td>
		  	 <p>FACTURAS CONTROL</br> REPETICIONES</p>
		    <p><a href="informe.controlrepeticiones.php"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td>
		  <td>
		  	 <p>FACTURAS PENDIENTES</br> DE PAGO</p>
		    <p><a href="informe.pendientespago.php"><img src="img/excellogo.png" width="90" height="90" border="0"/></a></p>
		  </td>
	  </tr>
	  <tr>
		  <td>
		  	<p>CONTROL</br> DE FACTURAS</p>
		    <p><a href="informe.controlfacturas.php"><img src="img/pantalla.png" width="90" height="90" border="0"/></a></p>
		  </td>
	  	  <td>
		  	<p>DETALLE CABECERAS</br>FINALIZADAS</p>
		    <p><a href="informe.detallecabeceras.php"><img src="img/pantalla.png" width="90" height="90" border="0"/></a></p>
		  </td>
		  <td></td>
	  </tr>
    </tr>
  </table>
</div>
</body>
</html>