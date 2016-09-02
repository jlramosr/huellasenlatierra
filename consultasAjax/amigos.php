<?php 

	/* 	FORMATO DEL XML DEVUELTO EN EL CASO DE UNA CONSULTA (NO MODIFICACIÓN):
		
		<respuesta>			
			<amigo>
				<idAmigo> ... </idAmigo> 
				<nickAmigo> ... </nickAmigo>
				<estado> ... </estado>
			</amigo>
			<amigo>
				...
			</amigo>
			...
		<respuesta>
		
	*/	
	
	/* 	FORMATO DEL XML DEVUELTO EN EL CASO DE UNA INSERCIÓN:
		
		<respuesta>			
			<amigo>
				<existe> ... </existe>
				<movimientoAnterior> ... </movimientoAnterior>
				<esElMismo> ... </esElMismo>
				<pendiente> ... </pendiente>
				<rechazada> ... </rechazada>
				<idAmigo> ... </idAmigo> 
				<nickAmigo> ... </nickAmigo>
			</amigo>
		<respuesta>
		
	*/
	
	include_once("../funciones/bd.php");

	$usuario = $_POST["usuario"];
	$idUsuario = dameIdUsuario("$usuario");
	$conexion = conectarBD();
	$xml = "";
	
	if (!strcmp($_POST["modificacion"],"no")) {	
		// Sólo interesan las amistades recíprocas ya aceptadas (estado=1) y las filas cuyo idAmigo coincida con el usuario 
		// y el estado de la amistad esté pendiente de aceptación o rechazo (estado=0 con $idUsuario=idUsuario)
		$resultado = ejecutarSQL("	SELECT idAmigo idAmigos, estado
									FROM  amigos 
									WHERE idUsuario = '$idUsuario' AND estado = '1'
									UNION SELECT idUsuario, estado
									FROM  amigos 
									WHERE idAmigo = '$idUsuario' AND (estado = '0' OR estado = '1')",$conexion);		
		$xml .= "<respuesta> \n";
		while ($fila = dameFila($resultado)) { 
			$nickAmigo = dameNick($fila["idAmigos"]);
			$xml .= "\t <amigo> \n";
			$xml .= "\t\t <idAmigo>" . $fila["idAmigos"] . "</idAmigo> \n";
			$xml .= "\t\t <nickAmigo>" . $nickAmigo . "</nickAmigo> \n";
			$xml .= "\t\t <estado>" . $fila["estado"] . "</estado> \n";
			$xml .= "\t </amigo> \n";
		}
		$xml .= "</respuesta>";
	
		cerrarEjecucionSQL($resultado);
	}	
	else { //modificacion=si
		$amigo = $_POST["amigo"];
		$idAmigo = -1;
		if (!strcmp($_POST["envio"],"si")) {
			$resultadoExiste = ejecutarSQL("SELECT * FROM usuarios WHERE nick='$amigo'",$conexion);		
			$xml .= "<respuesta> \n";
			$xml .= "\t <amigo> \n";
			if ($fila = dameFila($resultadoExiste)) {
				//EL USUARIO EXISTE
				$xml .= "\t\t <existe>si</existe> \n";
				$idAmigo = dameIdUsuario("$amigo");
				if ($idAmigo == $idUsuario) {
					//SE HA AÑADIDO COMO AMIGO ÉL MISMO
					$xml .= "\t\t <movimientoAnterior>no</movimientoAnterior> \n";
					$xml .= "\t\t <esElMismo>si</esElMismo> \n";
					$xml .= "\t\t <pendiente>no</pendiente> \n";
					$xml .= "\t\t <rechazada>no</rechazada> \n";
				}
				else {
					//HA AÑADIDO COMO AMIGO A OTRO QUE NO ES ÉL MISMO
					$resultadoYaEsAmigo = ejecutarSQL("SELECT idAmigo idAmigos, estado
												   FROM  amigos 
												   WHERE idUsuario = '$idUsuario'
												   UNION SELECT idUsuario, estado
												   FROM  amigos 
												   WHERE idAmigo = '$idUsuario'",$conexion);
					$encontrado = false;
					/* El estado de la amistad se define así: 
						0: está pendiente de aceptación 
						1: amistad aceptada por idAmigo a la solicitud de idUsuario 
						2: amistad rechazada siempre por idAmigo a la solicitud de idUsuario 
					*/	
					$estadoActual = -1;
					while (!$encontrado && $fila = dameFila($resultadoYaEsAmigo)) {
						if ($fila["idAmigos"] == $idAmigo) { 
							$encontrado = true;
							$estadoActual = $fila["estado"];
						}
					}
					if ($encontrado) {
						//YA HA HABIDO MOVIMIENTOS DE AMISTAD PREVIOS
						$xml .= "\t\t <movimientoAnterior>si</movimientoAnterior> \n";
						$xml .= "\t\t <esElMismo>no</esElMismo> \n";
						switch ($estadoActual) {
							case 0: //PENDIENTE DE ACEPTACIÓN																
								$xml .= "\t\t <pendiente>si</pendiente> \n";
								$xml .= "\t\t <rechazada>no</rechazada> \n";
								break; 
							case 1: //AMISTAD ACEPTADA
								$xml .= "\t\t <pendiente>no</pendiente> \n";
								$xml .= "\t\t <rechazada>no</rechazada> \n";
							default: //AMISTAD RECHAZADA
								$xml .= "\t\t <pendiente>no</pendiente> \n";
								$xml .= "\t\t <rechazada>si</rechazada> \n";
								break;
						}
					}
					else  {
						//NO HA HABIDO MOVIMIENTOS DE AMISTAD PREVIOS => INSERTAR CON ESTADO PENDIENTE DE CONFIRMACIÓN
						$xml .= "\t\t <movimientoAnterior>no</movimientoAnterior> \n";
						$xml .= "\t\t <esElMismo>no</esElMismo> \n";
						$xml .= "\t\t <pendiente>no</pendiente> \n";
						$xml .= "\t\t <rechazada>no</rechazada> \n";
						ejecutarSQL("INSERT INTO amigos(idUsuario,idAmigo,estado,fechaSolicitud) VALUES('$idUsuario','$idAmigo',0,NOW())",$conexion);
					}
					cerrarEjecucionSQL($resultadoYaEsAmigo);
				}
			}
			else {
				//EL USUARIO NO EXISTE
				$xml .= "\t\t <existe>no</existe> \n";
				$xml .= "\t\t <movimientoAnterior>no</movimientoAnterior> \n";
				$xml .= "\t\t <esElMismo>no</esElMismo> \n";
				$xml .= "\t\t <pendiente>no</pendiente> \n";
				$xml .= "\t\t <rechazada>no</rechazada> \n";
			}			
			$xml .= "\t\t <idAmigo>$idAmigo</idAmigo> \n";
			$xml .= "\t\t <nickAmigo>$amigo</nickAmigo> \n";
			$xml .= "\t </amigo> \n";
			$xml .= "</respuesta>";

			cerrarEjecucionSQL($resultadoExiste);
		}	
		elseif (!strcmp($_POST["eliminacion"],"si")) {
			$idAmigo = dameIdUsuario("$amigo");
			ejecutarSQL("DELETE FROM amigos WHERE idUsuario='$idUsuario' AND idAmigo='$idAmigo'",$conexion);
			ejecutarSQL("DELETE FROM amigos WHERE idUsuario='$idAmigo' AND idAmigo='$idUsuario'",$conexion);
		}
		elseif (!strcmp($_POST["aceptacion"],"si")) {
			$idAmigo = dameIdUsuario("$amigo");
			ejecutarSQL("UPDATE amigos SET estado=1,fechaRespuesta=NOW() WHERE idUsuario='$idAmigo' AND idAmigo='$idUsuario'",$conexion);
		}
		elseif (!strcmp($_POST["rechazo"],"si")) {
			$idAmigo = dameIdUsuario("$amigo");
			ejecutarSQL("DELETE FROM amigos WHERE idUsuario='$idAmigo' AND idAmigo='$idUsuario'",$conexion);
		}
		elseif (!strcmp($_POST["rechazosiempre"],"si")) {
			$idAmigo = dameIdUsuario("$amigo");
			ejecutarSQL("UPDATE amigos SET estado=2,fechaRespuesta=NOW() WHERE idUsuario='$idAmigo' AND idAmigo='$idUsuario'",$conexion);
		}
	}			
	cerrarBD($conexion);		
	
	header('Content-Type: text/xml');
	echo "$xml";
?>