<?php 

	/* 	FORMATO DEL XML DEVUELTO EN EL CASO DE UNA CONSULTA (NO MODIFICACIÓN):
		
		<respuesta>			
			<poi>
				<idPOI> ... </idPOI> 
				<posx> ... </posx>
				<posy> ... </posy>
				<usuario> ... </usuario>
				<nombre> ... </nombre>
				<contenido> ... </contenido>
				<urlIcono> ... </urlIcono>
				<idCat> ... </idCat>
				<caracter> ... </caracter>
			</poi>
			<poi>
				...
			</poi>
			...
		<respuesta>
		
	*/	

	include_once("../funciones/bd.php");
	
	/* CONSULTAS */
	if (!strcmp($_POST["modificacion"],"no")) {
		$publicos = !strcmp($_POST["publicos"],"si");
		$protegidos = !strcmp($_POST["protegidos"],"si");
		$privados = !strcmp($_POST["privados"],"si");
		$usuario = $_POST["usuario"];
		$idUsuario = dameIdUsuario("$usuario");
	
		if ($publicos) {
			$consulta = "SELECT * FROM poi WHERE caracter='publico'";
			$caracterP = "publicos";
		}
		elseif ($protegidos) {
			$consulta = "SELECT * FROM poi WHERE caracter='protegido' AND idUsuario=$idUsuario";
			$caracterP = "protegidos";
		}
		elseif ($privados) {
			$consulta = "SELECT * FROM poi WHERE caracter='privado' AND idUsuario=$idUsuario";
			$caracterP = "privados";
		}
							
		$conexion = conectarBD();
		$resultado = ejecutarSQL("$consulta",$conexion);
		
		$xml = 	"<respuesta> \n";
		while ($fila = dameFila($resultado)) { 
			if ($publicos) $usuario = dameNick($fila["idUsuario"]);
			$xml .= "\t <poi> \n";
			$xml .= "\t\t <idPOI>" . $fila["idPOI"] . "</idPOI> \n";
			$xml .= "\t\t <posx>" . $fila["posx"] . "</posx> \n";
			$xml .= "\t\t <posy>" . $fila["posy"] . "</posy> \n";
			$xml .= "\t\t <usuario>" . $usuario . "</usuario> \n";
			$xml .= "\t\t <nombre>" . $fila["nombre"] . "</nombre> \n";
			$xml .= "\t\t <contenido>" . $fila["contenido"] . "</contenido> \n";
			//$xml .= "\t\t <contenido> Contenido BD </contenido> \n";
			$xml .= "\t\t <urlIcono>" . $fila["urlIcono"] . "</urlIcono> \n";
			$xml .= "\t\t <idCat>" . $fila["idCat"] . "</idCat> \n";
			$xml .= "\t\t <caracter>" . $fila["caracter"] . "</caracter> \n";
			$xml .= "\t </poi> \n";
		}
		$xml .= "</respuesta>";
	
		cerrarEjecucionSQL($resultado);
		cerrarBD($conexion);
		
		$publicos=false;
	
		header('Content-Type: text/xml');
		echo "$xml";
	}
	
	/* MODIFICACIONES */
	else {
		$idPOI = $_POST["id"];
		$posx = $_POST["posx"];
		$posy = $_POST["posy"];
		$nick = $_POST["nick"];
		$nombre = $_POST["nombre"];
		$contenido = $_POST["contenido"];
		$urlIcono = $_POST["urlIcono"];
		$categoria = $_POST["categoria"];
		$caracter = $_POST["caracter"];
		
		$ultimoId = 0;
	
		/* INSERCIONES */
		if (!strcmp($_POST["insercion"],"si")) {
			$ultimoId = insertarPOI($posx,$posy,$nick,$nombre,$contenido,$urlIcono,1,$caracter);
			echo "$ultimoId";
		}
		/* ELIMINACIONES */
		elseif (!strcmp($_POST["eliminacion"],"si")) {
			eliminarPOI($idPOI);
			echo "";
		}
		/* ACTUALIZACIONES */
		elseif (!strcmp($_POST["actualizacion"],"si")) {
			/* DE INFORMACIÓN */ 
			if (!strcmp($_POST["informacion"],"si")) {
				actualizarInfoPOI($idPOI,$nombre,$contenido);
			}
			/* DE POSICIÓN */
			elseif (!strcmp($_POST["sitio"],"si")) {
				actualizarPosPOI($idPOI,$posx,$posy);
			}
			echo "";
		}		
	}
	
?>