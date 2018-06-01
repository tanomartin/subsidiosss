<?php 
include_once 'include/conector.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Menú Subsidio S.S.S. :.</title>

</head>
<body bgcolor="#CCCCCC">
<div align="center">
  	<h2>INTEGRACION S.S.S.</h2>
  	<table width="600" border="1" style="text-align: center">
      <tr>
      <td width="200"> 
        <p>CRONOGRAMA</p>
        <p><a href="cronograma.php"><img src="img/cronograma.png" width="90" height="90" border="0"/></a></p>
	  </td>
      <td width="200">
	    <p>PRESENTACIONES</p>
	    <p><a href="presentacion.php"><img src="img/presentacion.png" width="90" height="90" border="0" alt="enviar"/></a></p>
	  </td>
	  <td width="200">
	    <p>BUSCADOR</p>
	    <p><a href="buscador.php"><img src="img/buscar.png" width="90" height="90" border="0" /></a></p>
	  </td>
	  
     </tr>	
     <tr>
       <td>
	    <p>ERRORES</p>
	    <p><a href="errores.php"><img src="img/error.png" width="90" height="90" border="0" /></a></p>
	  </td>
	  <td>
	    <p>COMPROBANTES</p>
	    <p><a href="comprobantes.php"><img src="img/comprobante.png" width="90" height="90" border="0" alt="enviar"/></a></p>
	  </td>
	  <td>
	    <p>NOMENCLADOR</p>
	    <p><a href="nomenclador.php"><img src="img/nomenclador.png" width="90" height="90" border="0" /></a></p>
	  </td>
	  </tr>
    </tr>
     <tr>
       <td>
	    <p>INTERBANKING</p>
	    <p><a href="interbanking.php"><img src="img/interbanking.png" width="90" height="90" border="0" alt="enviar"/></a></p>
	  </td>
	  <td>
	    <p>INFORMES</p>
	    <p><a href="informes.php"><img src="img/excellogo.png" width="90" height="90" border="0" alt="enviar"/></a></p>
	  </td>
	  <td>
	    <p></p>
	    <p></p>
	  </td>
	  </tr>
    </tr>
  </table>
   <p><input type="button" name="salir" value="SALIR" onclick="location.href='logout.php'" /></p>
</div>
</body>
</html>
