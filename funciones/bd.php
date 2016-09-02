<?php

/*_________________________ FUNCIONES DE CONEXIÓN _________________________________*/
function conectarBD() {
	$servidor = "localhost";
	$bd = "jlramosr_huellas";
	$usuario = 
		//"root";
		//"jlramosr_dani";
		"jlramosr_jose";
	$pass = 
		//"ajax137bbdd";
		//"usbw";
		"8416madrid";
	$conexion = new mysqli($servidor,$usuario,$pass,$bd);
	if (mysqli_connect_errno()) {
		echo "No se ha podido establecer la conexión con la base de datos: " . mysqli_connect_error();
		exit();
	}
   	return $conexion;
}
   
function cerrarBD($conexion) {
	$conexion->close();
}

function ejecutarSQL($comando,$conexion) {
	$resultado = $conexion->query($comando);
	return $resultado;
}

function cerrarEjecucionSQL($resultado) {
	$resultado->close();
}

function dameFila($resultado) {
	$fila = $resultado->fetch_array();
	return $fila;
}

/*__________________________ FUNCIONES DE DATOS __________________________________*/
function dameNick($idUsuario) {
	$conexion = conectarBD();				
	$resultado = ejecutarSQL("SELECT nick FROM usuarios WHERE idUsuario=$idUsuario",$conexion);
	$nick = ""; 
	if ($fila = dameFila($resultado)) { 
		$nick = $fila["nick"];
	}
	cerrarEjecucionSQL($resultado);
	cerrarBD($conexion);
	return "$nick";
}

function dameIdUsuario($nick) {
	$conexion = conectarBD();				
	$resultado = ejecutarSQL("SELECT idUsuario FROM usuarios WHERE nick='$nick'",$conexion);
	$idUsuario = -1; 
	if ($fila = dameFila($resultado)) { 
		$idUsuario = $fila["idUsuario"];
	}
	cerrarEjecucionSQL($resultado);
	cerrarBD($conexion);
	return $idUsuario;
}

function loginCorrecto($nick,$pass) {
	$b = false;
	$conexion = conectarBD();				
	$resultado = ejecutarSQL("SELECT activo FROM usuarios WHERE nick='$nick' AND pass='$pass'",$conexion);
	if ($fila = dameFila($resultado)) {
		$activo = $fila["activo"];
		if ($activo == 1) {
			$b = true;
		}
	}
	cerrarEjecucionSQL($resultado);
	cerrarBD($conexion);
	return $b;
}

function usuarioRegistrado($nick) {
	$b = false;
	$conexion = conectarBD();
	$resultado = ejecutarSQL("SELECT nick FROM usuarios WHERE nick='$nick'",$conexion);
	if ($fila = dameFila($resultado)) { 				
		$b = true;		
	}
	cerrarEjecucionSQL($resultado);
	cerrarBD($conexion);
	return $b;
}

function emailRegistrado($email) {
	$b = false;
	$conexion = conectarBD();
	$resultado = ejecutarSQL("SELECT email FROM usuarios WHERE email='$email'",$conexion);
	if ($fila = dameFila($resultado)) { 				
		$b = true;		
	}
	cerrarEjecucionSQL($resultado);
	cerrarBD($conexion);
	return $b;
}

function insertarUsuario($nick,$pass,$email,$codigo) {
	$conexion = conectarBD();
	ejecutarSQL("INSERT INTO usuarios(nick,pass,email,codigoActivacion,activo) VALUES('$nick','$pass','$email','$codigo',0)",$conexion);
	cerrarBD($conexion);
}

function activarUsuario($codigo) {
	$b = false;
	$conexion = conectarBD();
	$resultado = ejecutarSQL("SELECT activo FROM usuarios WHERE codigoActivacion='$codigo'",$conexion);
	if (($fila = dameFila($resultado)) && ($codigo != '0')) { 				
		$b = true;		
	}
	cerrarEjecucionSQL($resultado);
	ejecutarSQL("	UPDATE usuarios
					SET activo=1,
						codigoActivacion=0
					WHERE codigoActivacion='$codigo'",$conexion);
	cerrarBD($conexion);
	return $b;
}

function insertarPOI($posx,$posy,$nick,$nombre,$contenido,$urlIcono,$idCat,$caracter) {
	$conexion = conectarBD();
	$idUsuario = "admin";
	$idUsuario = dameIdUsuario($nick);
	if ($nombre == "") $nombre = "null";
	if ($contenido == "") $contenido = "null";
	$resultado = ejecutarSQL("	INSERT INTO poi(posx,posy,idUsuario,nombre,contenido,urlIcono,idCat,caracter) 
								VALUES('$posx','$posy','$idUsuario','$nombre','$contenido','$urlIcono','$idCat','$caracter')",$conexion);
	$ultimoId = mysqli_insert_id($conexion);
	cerrarBD($conexion);
	return ($ultimoId);
}

function eliminarPOI($idPOI) {
	$conexion = conectarBD();
	ejecutarSQL("DELETE FROM poi WHERE idPOI='$idPOI'",$conexion);
	cerrarBD($conexion);
}

function actualizarInfoPOI($idPOI,$nombre,$contenido) {
	$conexion = conectarBD();
	if ($nombre == "") $nombre = "null";
	if ($contenido == "") $contenido = "null";
	ejecutarSQL("	UPDATE poi 
					SET nombre='$nombre',
						contenido='$contenido' 
					WHERE idPOI='$idPOI'",$conexion);
	cerrarBD($conexion);
}

function actualizarPosPOI($idPOI,$posx,$posy) {
	$conexion = conectarBD();
	ejecutarSQL("	UPDATE poi 
					SET posx='$posx',
						posy='$posy' 
					WHERE idPOI='$idPOI'",$conexion);
	cerrarBD($conexion);
}

?>