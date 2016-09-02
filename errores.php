<?php 
function error_personalizado($tipo,$mensaje,$archivo,$linea) {
	//echo "<p class='error'> Error $tipo en la l&iacutenea $linea del archivo $archivo: $mensaje</p>"; */
	echo "<p class='error'> Error: $mensaje</p>";
}
set_error_handler("error_personalizado");
?>