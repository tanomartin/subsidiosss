<?php include_once 'include/conector.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Menú Interbanking S.S.S. :.</title>

</head>
<body bgcolor="#CCCCCC">
<div align="center">
	<p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
  	<h2>Interbanking</h2>
  	<table width="400" border="1" style="text-align: center">
      <tr>
	      <td width="200"> 
	        <p>PAGOS REALIZADOS</p>
	        <p><a href="interbanking.pago.realizado.php"><img src="img/interbanking.png" width="90" height="90" border="0"/></a></p>
		  </td>
	      <td width="200">
		    <p>GENERAR ARCHIVO</p>
		    <p><a href="interbanking.pago.php"><img src="img/pago.png" width="90" height="90" border="0" /></a></p>
		  </td>	 
     </tr>	
  </table>
</div>
</body>
</html>