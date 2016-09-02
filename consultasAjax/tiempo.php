<?php
	$ruta_fichero="../funciones/datosTiempo.xml"; 

	$contenido = ""; 
	if($da = fopen($ruta_fichero,"r"))  { 
		while ($aux= fgets($da,1024)) { 
			$contenido.=$aux; 
		} 
		fclose($da); 
	} 
	else { 
		echo "Error: no se ha podido leer el archivo <strong>$ruta_fichero</strong>"; 
	} 

	header('Content-Type: text/xml');
	echo "$contenido";

?>