<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];

Header("Location: presentacion.reversiones.php?id=$idPresentacion");
?>
