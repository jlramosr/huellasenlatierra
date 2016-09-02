<?php include_once("./funciones/bd.php"); ?>

<html>
<head>
<title>Confirmacion registro HelT</title>
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
	if (isset($_GET["id"])) {
		if (activarUsuario($_GET["id"])) {		
			echo "Proceso de registro completado satisfactoriamente ";
			?> <a href="index.php"> Volver </a> <?php
		}
	}
?>

	</div>
</div>
</body>
</html>