<?php 	
/* Se ha podido llegar aquí de las siguientes formas:
	(0) Es la primera vez que se accede y todavía no se ha enviado información mediante el método POST ni mediante el método GET
	(1) USUARIO LOGUEADO: $_POST["botonLogin"] activado. $_POST["username"],$_POST["password"] son el usuario y la contraseña introducidos 	
	(2) USUARIO DESCONECTADO: $_POST["botonDesconectar"] activado.
	(3) SE HA PULSADO EN "home". $_GET["home"] activado.
*/
		
	session_start();
	if (!$_POST && !$_GET) {
		/* CASO (0) */
	}
	elseif (isset($_POST["botonLogin"])) {
		/* CASO (1) */	
		/* se inicia la sesión del usuario */
		$_SESSION["usuario"] = htmlspecialchars($_POST["username"]); 
		$_SESSION["pass"] = htmlspecialchars($_POST["password"]);
		/* crear cookies si se ha marcado la opción "Recordarme" */
		if (isset($_POST["checkRecordarme"])) {
		/* se recordará durante un año */
			setcookie("recordarme",true,time()+(60*60*24*7*365),"/");
			setcookie("usuario",$_SESSION["usuario"],time()+(60*60*24*7*365),"/");
			setcookie("pass",$_SESSION["pass"],time()+(60*60*24*7*365),"/");
		}
		else {
			/* no se recordará. Se borran las cookies (time() - ...) */
			setcookie("recordarme",false,time()-(60*60*24*7*365));
			setcookie("usuario",$_SESSION["usuario"],time()-(60*60*24*7*365));
			setcookie("pass",$_SESSION["pass"],time()-(60*60*24*7*365));
		}
	}
	elseif (isset($_POST["botonDesconectar"])) {
		/* CASO (2) */
	}
	elseif (isset($_GET["home"])) {
		/* CASO (3) */
	}
	else {
		/* MÁS CASOS */
	}	
?>			

<?php 	include_once("funciones/mapa.php"); ?>
<?php 	include_once("funciones/bd.php"); ?>
<?php 	include_once("errores.php"); ?>
<?php 	error_reporting(E_ALL & ~E_NOTICE); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
    <title>Huellas en la Tierra</title> 
    
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" />
    
    
    <script type="text/javascript" src="js/tabpane.js"></script>
	<script type="text/javascript" src="js/jquery.js"></script>	
    <script type="text/javascript" src="js/markermanager.js"></script>	
        
	<script type="text/javascript">
	function hideall() {
		$("#tab").hide();
		$("#tab1").hide();
		$("#tab2").hide();
		$("#tab3").hide();
		$("#tab4").hide();			
	}
	
	$(document).ready(function(){
		$("#icon1").mouseover(function () {
			hideall();
			$("#tab1").css("display","block");     
		});

		$("#icon2").mouseover(function () {
			hideall();
			$("#tab2").css("display","block");     
		});

		$("#icon3").mouseover(function () {
			hideall();
			$("#tab3").css("display","block");     
		});  

		hideall();
		$("#tab").show();       
	});
	
	</script>
    
</head> 

<?php 
	if (!$_POST && !$_GET) {
		/* CASO (0) */
		$mensaje = "";
		 ?>
		<script type="text/javascript">
			var fotosUsuario = null;
			var indiceFotoActual = -1;
		</script> <?php
	}
	elseif (isset($_POST["botonLogin"])) {
		/* CASO (1) */				
		/* COMPROBAMOS SI EL NOMBRE DE USUARIO DADO Y LA CONTRASEÑA SON VÁLIDOS */
		$user = $_SESSION["usuario"]; $pass = $_SESSION["pass"];
		if (loginCorrecto($user,$pass)) { 
			$mensaje = "";
			$_SESSION["invitado"] = false; 
			
			//Contar el nº de fotos que tiene el usuario
			/*$nombreDir = "usuarios/" . $_SESSION["usuario"] . "/fotos/"; ?>
			<script type="text/javascript">
					var fotosUsuario = new Array();
					var indiceFotoActual = -1;
			</script> <?php
			if (file_exists($nombreDir)) {
				$carpetaFotos = opendir($nombreDir); 
				while ($foto=readdir($carpetaFotos)) {
					if ($foto != "." && $foto != "..") { ?>
						<script type="text/javascript">
							fotosUsuario.push("<?php echo "$foto"; ?>");
							if (fotosUsuario.length == 1) indiceFotoActual = 0;
						</script> <?php
					}
				}
				closedir($carpetaFotos);
			}*/
		}
		else {
			$mensaje = "¡Login incorrecto!";
			$_SESSION["invitado"] = true; 
			?>
			<script type="text/javascript">
				var fotosUsuario = null;
				var indiceFotoActual = -1;
			</script> <?php
		}
	}
	elseif (isset($_POST["botonDesconectar"])) {
		/* CASO (2) */
		$mensaje = "Usuario desconectado satisfactoriamente";
		session_destroy();
		$_SESSION = array();
		 ?>
		<script type="text/javascript">
			var fotosUsuario = null;
			var indiceFotoActual = -1;
		</script> <?php
	}
	elseif (isset($_GET["home"])) {
		/* CASO (3) */
		$mensaje = "";
		if (strcmp($_GET["inv"],"s") == 0) {
			$_SESSION["invitado"] = true;
		}
		else {
			$_SESSION["invitado"] = false;
		}
		 ?>
		<script type="text/javascript">
			var fotosUsuario = null;
			var indiceFotoActual = -1;
		</script> <?php
	}
	else {
		/* MÁS CASOS */
		?>
		<script type="text/javascript">
			var numFotos = 0
		</script> <?php
	}
	if (isset($_SESSION["invitado"])) {
		$invitado = $_SESSION["invitado"];
	}
	else {
		$invitado = true;
	}
?>

<body>
<div id="usuarioActual" title="<?php if($invitado) echo "invitado"; else {$user = $_SESSION["usuario"]; echo $user;}?>"></div>
<div class="wrap">
	<div class="header">
		<div class="logo"><img src="images/Logo.gif" alt="" width="80" height="80" border="0" title="" /></div>
        <div class="titulo"><img src="images/Titulo.png" alt="" width="270" height="40" border="0" title="" /></div>
        
        <div id="menu">
          <ul> 
			<li class="selected">
            	<?php if ($invitado) $urlInvitado="s"; else $urlInvitado="n"; ?> 
            	<a href="<?php echo $_SERVER['PHP_SELF'] . "?home=s&inv=$urlInvitado"; ?>"> Home </a>
            </li>
            <!--<li><a href="details.html">about us</a></li>
            <li><a href="details.html">services</a></li>
            <li><a href="details.html">work</a></li>
            <li><a href="details.html">contact us</a></li>
            -->
          </ul>
		</div>
	</div> <!--End of header-->
  
<script type="text/javascript">
		
function nuevaMarca(boton){
	var nombre = "<?php if($invitado) echo "invitado"; else {$user = $_SESSION["usuario"]; echo $user;}?>";
	iconoSeleccionado(boton,nombre);
}

cargarPOIS();

</script>  
  
	<div class="main_content">
		<div class="left_content">
			<div class="menu_sup">
				<!--<a href="javascript: void ad_tiempo()"><img id="generales_tiempo" class="ImgAdTiempo" src="images/generales_plantilla.png" alt="" width="100" border="0" title="" /></a>-->
                <a href="javascript: void ad_tiempo()"><img id="generales_tiempo" class="ImgAdTiempo_carga" src="images/cargando.gif" alt="" width="100" border="0" title="" /></a>
                <a href="javascript: void ad_fronteras()"><img id="generales_fronteras" class="ImgAdFronteras" src="images/generales_plantilla.png" alt="" width="100" border="0" title="" /></a>
				<a href="javascript: void ad_publicos()"><img id="generales_publicos" class="ImgAdPublicos" src="images/generales_plantilla.png" alt="" width="100" border="0" title="" /></a>    
                <?php if(!$invitado){ ?>
				<a href="javascript: void ad_protegidos()"><img id="generales_protegidos" class="ImgAdProtegidos" src="images/generales_plantilla.png" alt="" width="100" border="0" title="" /></a>
                <a href="javascript: void ad_privados()"><img id="generales_privados" class="ImgAdPrivados" src="images/generales_plantilla.png" alt="" width="100" border="0" title=""/></a>    
                <?php }?>
                                   
            	<!-- <input type="text" class="buscar" value="Buscar" onclick="this.value=''"/> -->         
            </div>
            <div id="contentMapa" class="contentMapa">
			<div id="mapa"></div>
            </div>						       
		</div> <!--End of left_content-->
            
		<div class="right_content">
        
        	<div id="refresco" class="menu_sup_derecho"> </div>
        
			<?php if ($invitado) {  ?>
			<div class="menu_login">
			<fieldset id="login">
					<h4>Usuarios</h4>
					<!-- Los datos que envía el formulario son recogidos en este mismo archivo php mediante el método POST -->
						<?php 
							if (!empty($mensaje)) echo "<p><font size=1; color='red'>&nbsp&nbsp$mensaje</font></p>"; 
							if (isset($_COOKIE['usuario'])) $usuarioCookie = $_COOKIE['usuario'];
							if (isset($_COOKIE['pass'])) $passCookie = $_COOKIE['pass'];
							if (isset($_COOKIE['recordarme'])) 
								$recordCookie = true; 
							else 
								$recordCookie = false;
						?> 
               			<form action=" <?php echo $_SERVER['PHP_SELF']; ?> " method="post">
                        	<p class="clearfix">
                        		<label for="username">Nombre</label>
                            	<input name="username" id="username" type="text" value=
									"<?php if ($recordCookie) echo "$usuarioCookie"; ?>" /></p>
                            <p class="clearfix">
                            	<label for="password">Pass</label>
								<input name="password" id="password" type="password" value=
									"<?php if ($recordCookie) echo "$passCookie"; ?>" />
                            </p>
                            <p class="clearfix check">
                            	<input name="checkRecordarme" type="checkbox" id="remember" value="activado" 
                                <?php if ($recordCookie) echo "checked"; ?> />
                            	<label for="checkRecordarme" id="remlabel">Recordarme</label>
                        		<input name="botonLogin" id="submit" type="submit" value="" />
                          </p>
                    	</form>
                        <p class="member">
                        	<a href="registro.php">Regístrate ahora</a>
                        </p>
			</fieldset>               
			</div>
			<?php } 
				else {
			?>
			<div class="menu_login">
				<h4><?php echo "Bienvenido, " . $_SESSION["usuario"]; ?>
                <form action=" <?php echo $_SERVER['PHP_SELF']; ?> " method="post"> 
                	<input type="submit" name="botonDesconectar" value="Cerrar sesion" id="botonDesconectar" />
				</form></h4>
                <div id="solicitudes"></div>
                <div id="paginacionSolicitudes" align="center"></div> 
			</div>
			<?php } ?>
                
      		<!--<div class="menu_user"><h1>MENU USER</h1>
			</div>-->
            
			<div class="tab-pane" id="tab-pane-1">
				<div class="tab-page">
					<h2 class="tab">Añadir Huella</h2>
					<form id="form1" name="form1" method="post" action="">
                    	<!-- IMPORTANTE EL "VALUE" DE CADA ENTRADA: "publico", "privado", "protegido" -->
						<p>Qui&eacute;n va a ver esta huella:<br />
						<label><input name="CaracterHuella" type="radio" id="CaracterHuella_publica" value="publico" checked="checked" />Todo el mundo</label> <br/>       
			 			<?php if(!$invitado){ ?>			
                        <label><input type="radio" name="CaracterHuella" value="privado" id="CaracterHuella_privada" />Sólo yo</label><br/>
     			        <label><input type="radio" name="CaracterHuella" value="protegido" id="CaracterHuella_protegida" />Amigos y yo</label><br/>
              			<?php } else{?>
           			   <p>Para poder a&ntilde;adir huellas que sólo veais tus amigos o tú <a href="registro.php">regístrate</a>.</p>
                       <p>
                         <?php }?>
                       </p>
                       
                     	<?php $hola = "hola"; ?>
                       <p> Selecciona un icono: </p>
                       <p>&nbsp;</p>
                       <input type="radio" name="nuevoIcono" value="1.png" id="test1" style="visibility:hidden" /> <label for="test1"><img class="ImgNuevoIcono" src="iconos/1.png" id="ico1" onclick="javascript: void nuevaMarca(this);"/></label>
					   <input type="radio" name="nuevoIcono" value="2.png" id="test2" style="visibility:hidden"/> <label for="test2"><img  class="ImgNuevoIcono" src="iconos/2.png" id="ico2" onclick="javascript: void nuevaMarca(this);"/></label>
                       <input type="radio" name="nuevoIcono" value="3.png" id="test3" style="visibility:hidden"/> <label for="test3"><img  class="ImgNuevoIcono" src="iconos/3.png" id="ico3" onclick="javascript: void nuevaMarca(this);"/></label>
                       <input type="radio" name="nuevoIcono" value="4.png" id="test4" style="visibility:hidden"/> <label for="test4"><img  class="ImgNuevoIcono" src="iconos/4.png" id="ico4" onclick="javascript: void nuevaMarca(this);"/></label>
                       <input type="radio" name="nuevoIcono" value="5.png" id="test5" style="visibility:hidden"/> <label for="test5"><img  class="ImgNuevoIcono" src="iconos/5.png" id="ico5" onclick="javascript: void nuevaMarca(this);"/></label>
                       <input type="radio" name="nuevoIcono" value="6.png" id="test6" style="visibility:hidden"/> <label for="test6"><img  class="ImgNuevoIcono" src="iconos/6.png" id="ico6" onclick="javascript: void nuevaMarca(this);"/></label>
                       <input type="radio" name="nuevoIcono" value="7.png" id="test7" style="visibility:hidden"/> <label for="test7"><img  class="ImgNuevoIcono" src="iconos/7.png" id="ico7" onclick="javascript: void nuevaMarca(this);"/></label>
                       <input type="radio" name="nuevoIcono" value="8.png" id="test8" style="visibility:hidden"/> <label for="test8"><img  class="ImgNuevoIcono" src="iconos/8.png" id="ico8" onclick="javascript: void nuevaMarca(this);"/></label>
     			       <p>&nbsp;</p>
                       
	                </form>
				</div>
				
              <div class="tab-page">
			    <h2 class="tab">Amigos</h2>
                
     			  <?php if(!$invitado){ ?>			 	
                     <h4>Tus amigos</h4>  
     				 <div id="MisAmigos"></div>
                     <div id="paginacionAmigos" align="center"></div>
                	 <h4>A&ntilde;adir amigo</h4>
                	 <div id="textoErrorAmigos"></div>
                     <input type="text" class="inputNick" id="nickNuevoAmigo" onclick="this.value=''" value="Nick de tu amigo" size="20" maxlength="20" /><img class="ImgNuevoAmigo" src="images/huellasAmigo_plantilla.png" id="nuevoAmigo" onclick="javascript: void insertarAmigo();"/>
                     
              	 <?php } else{?>
                     <div id="MisAmigos"></div>
                     <div id="paginacionAmigos" align="center"></div>
	            <p>Para poder a&ntilde;adir amigos <a href="registro.php">regístrate</a></p>    
                 <?php }?>
		                              
              </div>
			</div> <!-- Fin del menú de pestañas-->
		</div> <!--End of right_content-->          
		<div class="clear"> </div>
        
	</div><!--End of main_content-->
    <div id="footer"></div>
</div><!--End of wrap-->

</body>
</html>