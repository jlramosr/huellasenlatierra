<?php include_once("./funciones/bd.php"); ?>

<?php
	$primeravez = FALSE;
	if (empty($_POST["botonEnviar"])) {
		$primeravez = TRUE;
		$_POST["username"] = "";
		$_POST["email"] = "";
	}
?>
<html>
<head>
<title>Registro HelT</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" />
</head>

<body>

<div class="wrap">
	<div class="header">
		<div class="logo"><img src="images/Logo.gif" alt="" width="80" height="80" border="0" title="" /></div>
        <div class="titulo"><img src="images/Titulo.png" alt="" width="270" height="40" border="0" title="" /></div>
        
        <div id="menu">
          <ul> 
			<li class="selected">
            	<?php if ($invitado) $urlInvitado="s"; else $urlInvitado="n"; ?> 
            	<a href="<?php echo "index.php"; ?>"> Home </a>
            </li>
            <!--<li><a href="details.html">about us</a></li>
            <li><a href="details.html">services</a></li>
            <li><a href="details.html">work</a></li>
            <li><a href="details.html">contact us</a></li>
            -->
          </ul>
		</div>
	</div> <!--End of header-->

	<div class="registro"> 
	<?php 
		$user = htmlspecialchars($_POST["username"]); 
		$email = htmlspecialchars($_POST["email"]); 
		$pass = htmlspecialchars($_POST["pass"]);
		$rpass = htmlspecialchars($_POST["repetirpass"]);
		
   		$errorNombre = ""; $errorEmail = ""; $errorPass = "";
		if (!$primeravez)
		{
			$todoCorrecto = true;
			if (empty($user)) { 
				$errorNombre = "<p>¡Falta rellenar el nombre de usuario!</p>"; 
				$todoCorrecto = false; 
			}
			elseif (!(ereg("^[[:alnum:]]+$", $user))) {			
				$errorNombre = "<p>¡El formato del nombre de usuario no es correcto!</p>"; 
				$todoCorrecto = false;
			}
			elseif (strlen($user)<3 || strlen($user)>20) {
				$errorNombre = "<p>¡El nombre de usuario debe tener entre 3 y 20 caracteres!</p>"; 
				$todoCorrecto = false;
			}
			if (empty($email)) { 
				$errorEmail = "<p>¡Falta rellenar el email!</p>"; 
				$todoCorrecto = false; 
			} 
			elseif (!(ereg("^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$", $email))) {
				$errorEmail = "<p>¡El formato del email no es correcto!</p>"; 
				$todoCorrecto = false;
			}
			if (empty($pass)) { 
				$errorPass = "<p>¡Falta rellenar la contraseña!</p>"; 
				$todoCorrecto = false; 
			}
			elseif (!($rpass==$pass)) { 
				$errorPass = "<p>¡Las contraseñas no coinciden!</p>"; 
				$todoCorrecto = false; 
			}
			if ($todoCorrecto) {
				/* COMPROBAMOS SI EL NOMBRE DE USUARIO DADO O EL EMAIL YA EXISTEN */
				if (usuarioRegistrado($user)) { 				
					$errorNombre = "<p>¡El nombre de usuario ya está registrado!</p>"; 
					$todoCorrecto = false;
				}
				elseif (emailRegistrado($email)) {				
					$errorNombre = "<p>¡El email ya está registrado!</p>"; 
					$todoCorrecto = false;
				}
			}
		}
		
		if (!$todoCorrecto) 
		{
	?>
  	<div id="faltan" class="error"> 
  	<?php 
		echo "$errorNombre";
		echo "$errorEmail"; 
		echo "$errorPass";
  	?>
  	</div>
  
  	<form  name"formularioRegistro" method="post" action=" <?php echo $_SERVER['PHP_SELF']; ?> ">
		<p>
        	Todos los campos son obligatorios
        </p>
        <p>
 			<label for="username">Usuario</label>
			<input name="username" type="text" id="username" size="50" value="<?php echo "$user"; ?>">
		</p>
		<p>
			<label for="email">Email </label>
			<input name="email" type="text" id="email" value="<?php echo "$email"; ?>">
		</p>
        <p>
			<label for="pass">Pass </label>
			<input name="pass" type="password" id="pass" value="<?php echo "$pass"; ?>">
			<label for="repetirpass">Repetir pass </label>
			<input name="repetirpass" type="password" id="repetirpass">
		</p>
		<p>
			<input type="submit" name="botonEnviar" value="Enviar" id="botonEnviar">
		</p>
	</form>

	<?php 
		}
		else {
			/* LA INFORMACIÓN DADA ES CORRECTA. SE ENVÍA UN CORREO AL USUARIO PARA CONFIRMAR SU REGISTRO (activacion.php) */
			
			$codigo = uniqid(); // genera un id único para identificar la cuenta a traves del correo			
			
			$asunto = "Activación de tu cuenta en huellasenlatierra.com"; 
			
			$mensaje = "Registro en huellasenlatierra.com\n\n"; 
			$mensaje .= "Estos son tus datos de registro:\n"; 
			$mensaje .= "\tUsuario: $user\n"; 
			$mensaje .= "\tContraseña: $pass\n\n"; 
			$mensaje .= "Debes activar tu cuenta pulsando este enlace: http://www.huellasenlatierra.com/activacion.php?id=$codigo"; 

			$headers = "From: huellasenlatierra@huellasenlatierra.com\r\n";
			
			if (mail($email,$asunto,$mensaje,$headers)) { 
    			echo "Se ha enviado un mensaje a tu e-mail para confirmar el registro";
				insertarUsuario($user,$pass,$email,$codigo);
				mkdir("usuarios/" . $user);
				mkdir("usuarios/" . $user . "/fotos");
			} 
			else { 
    			echo "No se ha podido enviar el mensaje de confirmación a tu correo electrónico. Deberás registrarte de nuevo."; 
			} 
			
			?>
            <a href="index.php"> Volver </a>
	<?php
		}
  	?>
    
	</div>
    
</div>      
</body>
</html>