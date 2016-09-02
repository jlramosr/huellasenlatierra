<?php 

	include_once("../funciones/bd.php");
	$conexion = conectarBD();
	
	/* ---------------------------------MANTENIMIENTO 1: TRANSFORMACIÓN DE NOMBRES-------------------------------------
	$xml = "<respuesta>\n";
	
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, '-', ' ')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, '\'', ' ')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, '(', '')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, ')', '')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, 'ä', 'a')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, 'ë', 'e')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, 'ï', 'i')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, 'ö', 'o')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, 'ü', 'u')", $conexion);
	ejecutarSQL ("UPDATE coordenadas SET poblacion = replace(poblacion, '/', ', ')", $conexion);
	
	$xml .= "</respuesta>\n";	
	header('Content-Type: text/xml');
	echo "$xml";
	/* ----------------------------------------------------------------------------------------------------------------- */
	
	/* ---------------MANTENIMIENTO 2: ACTUALIZACIÓN DE IDENTIFICADORES -> ir modificando estas variables --------------	
	$idProvinciaIni = 48;
	$idProvinciaFin = 49;
		
	$miId = "51bbudytnp13";
	
	$idContinenteDeseado = 1;
	$idPaisDeseado = 18;
	
	$xml = "<respuesta>\n";
	if ($file1 = simplexml_load_file("http://api.meteored.com/?continente=0&affiliate_id=$miId")) {
		// CONTINENTES
		$contador1 = $file1->location->attributes()->num;
		for ($i1=0; $i1<$contador1; $i1++) {
			$xml .= "\t<continente>\n"; 
			$xml .= "\t\t<nombre>";
			$xml .= $file1->location->data[$i1]->name;
			$xml .= "</nombre>\n";
			$idContinente = $file1->location->data[$i1]->name->attributes()->id;
			$xml .= "\t\t<id>";
			$xml .= "$idContinente";
			$xml .= "</id>\n";
			$urlContinente = $file1->location->data[$i1]->url;
			$xml .= "\t\t<url>";
			$xml .= "$urlContinente";
			$xml .= "</url>\n";
			if (($idContinente==$idContinenteDeseado) && ($file2 = simplexml_load_file("$urlContinente&affiliate_id=$miId"))) {
				// PAÍSES
				$contador2 = $file2->location->attributes()->num;
				for ($i2=0; $i2<$contador2; $i2++) {
					$xml .= "\t\t<pais>\n";
					$xml .= "\t\t\t<nombre>";
					$xml .= $file2->location->data[$i2]->name;
					$xml .= "</nombre>\n";
					$idPais = $file2->location->data[$i2]->name->attributes()->id;
					$xml .= "\t\t\t<id>";
					$xml .= "$idPais";
					$xml .= "</id>\n";
					$urlPais = $file2->location->data[$i2]->url;
					$xml .= "\t\t\t<url>";
					$xml .= "$urlPais";
					$xml .= "</url>\n";
							// COMUNIDADES: la api de meteored pasa directamente de países a provincias en los xml devueltos
							if (($idPais==$idPaisDeseado) && ($file4 = simplexml_load_file("$urlPais&affiliate_id=$miId"))) {
								// PROVINCIAS
								$contador4 = $file4->location->attributes()->num;
								for ($i4=0; $i4<$contador4; $i4++) {
									$xml .= "\t\t\t\t<provincia>\n";
									$xml .= "\t\t\t\t\t<nombre>";
									$xml .= $file4->location->data[$i4]->name;
									$xml .= "</nombre>\n";
									$idProvincia = $file4->location->data[$i4]->name->attributes()->id;
									$xml .= "\t\t\t\t\t<id>";
									$xml .= "$idProvincia";
									$xml .= "</id>\n";
									$urlProvincia = $file4->location->data[$i4]->url;
									$xml .= "\t\t\t\t\t<url>";
									$xml .= "$urlProvincia";
									$xml .= "</url>\n";
									if (($idProvincia>=$idProvinciaIni) 
					 					    && ($idProvincia<=$idProvinciaFin) 
											&& ($file5 = simplexml_load_file("$urlProvincia&affiliate_id=$miId"))) {
										// LOCALIDADES
										$contador5 = $file5->location->attributes()->num;
										for ($i5=0; $i5<$contador5; $i5++) {
											$xml .= "\t\t\t\t\t<localidad>\n";
											$nombreLocalidad = $file5->location->data[$i5]->name;
											$xml .= "\t\t\t\t\t\t<nombre>";
											$xml .= "$nombreLocalidad";
											$xml .= "</nombre>\n";
											$idLocalidad = $file5->location->data[$i5]->name->attributes()->id;
											$xml .= "\t\t\t\t\t\t<id>";
											$xml .= "$idLocalidad";
											$xml .= "</id>\n";
											$xml .= "\t\t\t\t\t</localidad>\n";
				ejecutarSQL ("UPDATE coordenadas SET idCoordenada='$idLocalidad' WHERE poblacion = '$nombreLocalidad'",$conexion);
										}
									}
									$xml .= "\t\t\t\t</provincia>\n";
								}
							}
					$xml .= "\t\t</pais>\n";
				}
			}
		$xml .= "</continente>\n";
		}
	}
	$xml .= "</respuesta>\n";
	
	header('Content-Type: text/xml');
	echo "$xml";
	/* ----------------------------------------------------------------------------------------------------------------- */	
	
	/* -------------------------------------MANTENIMIENTO 3: ACTUALIZACIÓN ZOOM-----------------------------------------
	$xml = "<respuesta>\n";
	
	ejecutarSQL("UPDATE coordenadas SET zoom = 0", $conexion);
	$resultado = ejecutarSQL("SELECT C.idCoordenada FROM coordenadas C, provincias P WHERE C.poblacion=P.nombre AND C.idProvincia=P.idProvincia AND C.idCoordenada<>'0'", $conexion);
	while ($fila = dameFila($resultado)) { 
		$idCoordenada = $fila["idCoordenada"];
		ejecutarSQL("UPDATE coordenadas SET zoom = 1 WHERE idCoordenada='$idCoordenada'", $conexion);
	}	
	cerrarEjecucionSQL($resultado);
	
	$xml .= "</respuesta>\n";	
	header('Content-Type: text/xml');
	echo "$xml";
	/* ----------------------------------------------------------------------------------------------------------------- */
	
	/* --------------------MANTENIMIENTO 4: INSERCIÓN DE PROVINCIAS Y LOCALIDADES (SIN COORDENADAS)--------------------- 		
	$miId = "51bbudytnp13";
	
	$idContinenteDeseado = 1; //dejar a 0 si todos
	$idPaisDeseado = 19; //dejar a 0 si todos
	
	$xml = "<respuesta>\n";
	
	if ($file1 = simplexml_load_file("http://api.meteored.com/?continente=0&affiliate_id=$miId")) {
		// CONTINENTES
		$contador1 = $file1->location->attributes()->num;
		for ($i1=0; $i1<$contador1; $i1++) {
			$nombreContinente = $file1->location->data[$i1]->name;
			$idContinente = $file1->location->data[$i1]->name->attributes()->id;
			$urlContinente = $file1->location->data[$i1]->url;
			if ((($idContinente==$idContinenteDeseado) || ($idContinenteDeseado==0)) 
			 && ($file2 = simplexml_load_file("$urlContinente&affiliate_id=$miId"))) {
				// PAÍSES
				$contador2 = $file2->location->attributes()->num;
				for ($i2=0; $i2<$contador2; $i2++) {
					$nombrePais = $file2->location->data[$i2]->name;
					$idPais = $file2->location->data[$i2]->name->attributes()->id;
					$urlPais = $file2->location->data[$i2]->url;
							// COMUNIDADES: la api de meteored pasa directamente de países a provincias en los xml devueltos
							if ((($idPais==$idPaisDeseado) || ($idPaisDeseado==0)) 
							 && ($file4 = simplexml_load_file("$urlPais&affiliate_id=$miId"))) {
								// PROVINCIAS
								$contador4 = $file4->location->attributes()->num;
								for ($i4=0; $i4<$contador4; $i4++) {
									$nombreProvincia = $file4->location->data[$i4]->name;
									$idProvincia = $file4->location->data[$i4]->name->attributes()->id;
									$urlProvincia = $file4->location->data[$i4]->url;
									
					$resultado4 = ejecutarSQL ("SELECT * FROM provincias WHERE idProvincia='$idProvincia'",$conexion);
					if (!($fila4 = dameFila($resultado4))) { 
						ejecutarSQL ("INSERT INTO provincias VALUES ('$idProvincia','$nombreProvincia','$idPais')",$conexion);
					}
					cerrarEjecucionSQL($resultado4);
					
									if ($file5 = simplexml_load_file("$urlProvincia&affiliate_id=$miId")) {
										// LOCALIDADES
										$contador5 = $file5->location->attributes()->num;
										for ($i5=0; $i5<$contador5; $i5++) {
											$nombreLocalidad = $file5->location->data[$i5]->name;
											$idLocalidad = $file5->location->data[$i5]->name->attributes()->id;
											
					$resultado5 = ejecutarSQL ("SELECT * FROM coordenadas WHERE idCoordenada='$idLocalidad'",$conexion);
					if (!($fila5 = dameFila($resultado5))) { 
						ejecutarSQL ("INSERT INTO coordenadas VALUES ('$idLocalidad','$nombreLocalidad','0','0','0','$idProvincia','0')",$conexion);
					}
					cerrarEjecucionSQL($resultado5);
										}
									}
								}
							}
				}
			}
		}
	}
	
	$xml .= "</respuesta>\n";
	
	header('Content-Type: text/xml');
	echo "$xml";
	/* ----------------------------------------------------------------------------------------------------------------- */
	
	cerrarBD($conexion);

?>