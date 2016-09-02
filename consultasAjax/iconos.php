<?php 

	/* 	FORMATO DEL XML DEVUELTO:
		
		<respuesta>			
			<icono>
				<url> ... </url> 
			</icono>
			<icono>
				...
			</icono>
			...
		<respuesta>
		
	*/	
	
	include_once("../funciones/bd.php");
	
	if (!strcmp($_POST["modificacion"],"no")) {	
		$usuario = $_POST["usuario"];
		$idUsuario = dameIdUsuario("$usuario");
	
		$conexion = conectarBD();				
		$resultado = ejecutarSQL("SELECT urlIcono FROM iconos WHERE idUsuario = '$idUsuario'",$conexion);
		
		$xml = 	"<respuesta> \n";
		while ($fila = dameFila($resultado)) { 
			$xml .= "\t <icono> \n";
			$xml .= "\t\t <url>" . $fila["urlIcono"] . "</url> \n";
			$xml .= "\t </icono> \n";
		}
		$xml .= "</respuesta>";
	
		cerrarEjecucionSQL($resultado);
		cerrarBD($conexion);
	
		header('Content-Type: text/xml');
		echo "$xml";
	}
	
	else { //modificacion=si
		if (!strcmp($_POST["insercion"],"si")) {
			insertarIcono(/* PARÁMETROS NECESARIOS */);
		}
		elseif (!strcmp($_POST["eliminacion"],"si")) {
			eliminarIcono(/* PARÁMETROS NECESARIOS */);
		}
		elseif (!strcmp($_POST["actualizacion"],"si")) {
			actualizarIcono(/* PARÁMETROS NECESARIOS */);
		}
		
		echo "";
	}
	
?>