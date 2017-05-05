<?php 

include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

?>

<html>
<head>
<title>.: Subsidio ERROR :.</title>
</head>
<body bgcolor="#CCCCCC" link="#D5913A" vlink="#CF8B34" alink="#D18C35">
  <div align="center">
    <h1>&iexcl;&iexcl;ERROR de Sistema!!</h1>
    <h2>Detalle Presentacion</h2>
	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
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