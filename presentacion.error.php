<?php 

include_once 'include/conector.php';

if (isset($_GET['id'])) {
	$idPresentacion = $_GET['id'];	
	$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM intepresentacion p, intecronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
	$resPresentacion = mysql_query($sqlPresentacion);
	$rowPresentacion = mysql_fetch_array($resPresentacion);
}
?>

<html>
<head>
<title>.: Subsidio ERROR :.</title>
</head>
<body bgcolor="#CCCCCC" link="#D5913A" vlink="#CF8B34" alink="#D18C35">
  <div align="center">
  	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
    <h1>&iexcl;&iexcl;ERROR de Sistema!!</h1>
    <?php if (isset($_GET['id'])) {?>
   		<h2>Detalle Presentacion</h2>
		<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
    <?php } ?>
    <table border="1" style="width: 800px">
      <tr>
        <td><strong>P&aacute;gina: </strong></td>
        <td><?php echo $_GET['page']; ?></td>
      </tr>
      <tr>
        <td><strong>ERROR:</strong></td>
        <td><?php echo $_GET['error']; ?></td>
      </tr>
    </table>
    <p><input type="button" name="imprimir" value="Imprimir" onClick="window.print();" /></p>
  </div>
</body>
</html>