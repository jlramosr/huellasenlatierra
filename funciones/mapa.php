 <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAeuUg612MjAgEDDhHED8AyhT5jFKWkjqs4sTJuRInI9IXSOlexxTbWK8iaZCgItW5pqJiEWG_j6lsSQ" type="text/javascript">
</script> 

<script type="text/javascript">
//<![CDATA[ 	
	   		   	 
/* _______________________ VARIABLES GLOBLALES ________________________________ */
var mantenimiento = 0; //si es 0 es que no; si es distinto de 0 es que sí

var READY_STATE_UNINITIALIZED = 0; 
var READY_STATE_LOADING = 1; 
var READY_STATE_LOADED = 2;
var READY_STATE_INTERACTIVE = 3; 
var READY_STATE_COMPLETE = 4;

var AMIGOS_POR_PAGINA = 5;
var MAX_PAGINAS_AMIGOS = 10;
var SOLICITUDES_POR_PAGINA = 3;
var MAX_PAGINAS_SOLICITUDES = 10;

/* Peticiones*/
var peticionAjax1 = null; //carga de POIs públicos
var peticionAjax2 = null; //carga de POIs protegidos
var peticionAjax3 = null; //carga de POIs privados
var peticionAjax4 = null; //carga de los amigos
var peticionAjax5 = null; //carga de los iconos
var peticionAjax6 = null; //inserción de POIs
var peticionAjax71 = null; //actualización de localización de POIs
var peticionAjax72 = null; //actualización de nombre-contenido de POIs
var peticionAjax73 = null; //actualización de icono de POIs
var peticionAjax74 = null; //actualización de categoria de POIs
var peticionAjax75 = null; //actualización de caracter de POIs
var peticionAjax8 = null; //eliminación de POIs 
var peticionAjax9 = null; //inserción de amigos
var peticionAjax10 = null; //eliminación de amigos
var peticionAjax11 = null; //inserción de iconos
var peticionAjax12 = null; //actualización de iconos
var peticionAjax13 = null; //eliminación de iconos
var peticionAjax14 = null; //carga de POIs de amigos
var peticionAjax15 = null; //aceptar a un amigo
var peticionAjax16 = null; //rechazar a un amigo
var peticionAjax17 = null; //rechazar siempre a un amigo
var peticionAjax18 = null; //carga del tiempo

var tiempoActivado = false;
var publicosActivados= false;
var protegidosActivados= false;
var privadosActivados= false;
var nuevoMarcadorActivado = false;
var amigosActivados = false;
var fronterasActivados = false;

/* Contadores de tiempo */
var ahora = null;
var reinicio = null;
var tiempoFuncionRefrescar = 1; //en segundos
var tiempoRefresco = 600; //en segundos y mayor o igual que "tiempoFuncionRefrescar"
var cargaTiempoFin = true;
var tiempoReinicio;

/* Mapa principal */
var gmap = null;

/* Fronteras */
var geoXmlEspana = null;
var urlEspana = "http://www.huellasenlatierra.com/funciones/datosProvincias.kml";

/* Objetos de la clase GMarkerManager para gestionar conjuntos grandes de marcadores */
var controladorTiempo = null;
var controladorMarcadoresPublicos = null;
var controladorMarcadoresProtegidos = null;
var controladorMarcadoresPrivados = null;
var controladorMarcadoresAmigos = null;

/* Arrays con todos los marcadores */
var TIEMPO = null;
var PUBLICOS = null;
var PROTEGIDOS = null;
var PRIVADOS = null;
var MARCADORESAMIGOS = null;

/* Arrays con todos los marcadores y con información de cada uno de ellos */
var PUBLICOSINF = null;
var PROTEGIDOSINF = null;
var PRIVADOSINF = null;
var MARCADORESAMIGOSINF = null;

/* Amigos, solicitudes de amistad e iconos del usuario */
var AMIGOS = null;
var SOLICITUDES = null;
var ICONOS = null;

/* Página que se muestra de la lista de amigos */
var paginaActualAmigos = 1;

/* Página que se muestra de la lista de solicitudes de amistad */
var paginaActualSolicitudes = 1;

/* Último idPOI insertado en la tabla de los POIs de la base de datos */
var ultimoIdPOI = 0;

/* Usuario que tiene abierta la sesion actualmente, si no hay ninguno "invitado" */
var usuarioActual = "invitado";

/* Texto para mostrar lo que queda para actualizarse la página */
var htmlRefresco = "";

/* __________________________ FUNCIONES AJAX __________________________________*/
function cargarAjax() {
	var peticion = null;
	if(window.XMLHttpRequest) {
		//MOZILLA, CHROME, SAFARI, ...
    	peticion = new XMLHttpRequest();
  	}
  	else if(window.ActiveXObject) {
		//IE
    	peticion  = new ActiveXObject("Msxml2.XMLHTTP");
  	} 
	else {
		//OTROS
		peticion = new ActiveXObject ("Microsoft.XMLHTTP");
	}
	return peticion;
}

function peticionAjaxCompleta(peticion) {
	var b = false;
	if(peticion.readyState == READY_STATE_COMPLETE) {		
    	if(peticion.status == 200) {
     	 	b = true;
    	}
  	}
	return b;
}

/*_________________________ FUNCIONES DE CARGA _________________________________*/
window.onload = function() {
	geoXmlEspana = new GGeoXml(urlEspana);
	cargarMapa();
	
	controladorTiempo = new MarkerManager(gmap);
	controladorMarcadoresPublicos = new MarkerManager(gmap);
	controladorMarcadoresProtegidos = new MarkerManager(gmap);
	controladorMarcadoresPrivados = new MarkerManager(gmap);
	controladorMarcadoresAmigos = new MarkerManager(gmap);
	
	
	TIEMPO = new Array(10);
	TIEMPO[1] = new Array(); //Resto
	TIEMPO[4] = new Array(); //Capital Pais
	TIEMPO[5] = new Array(); //Capital de autonomia
	TIEMPO[6] = new Array(); //provincias
	
	PUBLICOS = new Array();
	PUBLICOSINF = new Array();
	PROTEGIDOS = new Array();
	PROTEGIDOSINF = new Array();
	PRIVADOS = new Array();
	PRIVADOSINF = new Array();
	MARCADORESAMIGOS = new Array();
	MARCADORESAMIGOSINF = new Array();
	AMIGOS = new Array();
	SOLICITUDES = new Array(); 
	ICONOS= new Array();
					
	usuarioActual = document.getElementById("usuarioActual").title;
	
	paginaActualAmigos = 1;
	paginaActualSolicitudes = 1;
	
	if (mantenimiento != 0) {
		mantenimientoBBDD();
	}
		
	refrescar();
}

window.onresize = function(){
	//Hallamos el alto y ancho de la pantalla
	var ancho = window.innerWidth;
	var alto = window.innerHeight;
		
	if (ancho > 970) ancho = ancho - 360;
	else ancho = 610;
		
	if (alto > 670) alto = alto - 160;
	else alto = 560;
	
	document.getElementById("mapa").width = ancho;
	document.getElementById("mapa").height = alto;

	gmap.checkResize();
	
	//alert("Resize: alto-> "+alto+" ancho-> "+ancho);
}

window.onunload="GUnload()";

/*_________________________ FUNCIONES DEL MAPA _________________________________*/
function cargarMapa() {
	/* Cargar mapa de Google */
	if (GBrowserIsCompatible()) {
		var mapa = document.getElementById("mapa");
		
		//Hallamos el alto y ancho de la pantalla
		var ancho = window.innerWidth;
		var alto = window.innerHeight;
		
		if (ancho > 970) ancho = ancho - 363;
		else ancho = 610;
		
		if (alto > 670) alto = alto - 163;
		else alto = 560;
		
		document.getElementById("mapa").width = ancho;
		document.getElementById("mapa").height = alto;
		
		document.getElementById("contentMapa").style.width = ancho;
		document.getElementById("contentMapa").style.height = alto;
		
		gmap = new GMap2(mapa, { size: new GSize(ancho,alto) } );
		// Añadir controles por defecto al mapa
		var controlesPorDefecto = gmap.getDefaultUI();
		//controlesPorDefecto.maptypes.hybrid = false;
        gmap.setUI(controlesPorDefecto);
		// Centrar en Madrid
        gmap.setCenter(new GLatLng(40.420088, -3.688810), 6);
		
		gmap.setMapType(G_HYBRID_MAP);
		
		//gmap.enableGoogleBar(); //Activa el buscador de google en el mapa
	}		
}

function cargarPOIpublicos() {	
	peticionAjax1 = cargarAjax();
	if (peticionAjax1) {
    	peticionAjax1.onreadystatechange = cargarPOIpublicosRespuesta;
    	peticionAjax1.open("POST", "consultasAjax/POI.php", false);
		peticionAjax1.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax1.send("modificacion=no&publicos=si&protegidos=no&privados=no&nocache="+Math.random());
  	}
}

function cargarPOIpublicosRespuesta() { 
	if(peticionAjaxCompleta(peticionAjax1)) {
		// recibe la respuesta en un documento XML
		var documentoXML = peticionAjax1.responseXML;
		// obtener la raiz del documento */
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		// obtener todos los POIs reflejados en el documento XML
		var pois = raiz.getElementsByTagName("poi");
		// reiniciamos para no acumular marcadores anteriores
		controladorMarcadoresPublicos = new MarkerManager(gmap);
		// Vamos añadiendo al array de marcadores todos los POIs
		for (var i=0; i<pois.length; i++) {	
			// obtiene toda la información del marcador del xml devuelto
			var idPOI = pois[i].getElementsByTagName("idPOI")[0].firstChild.nodeValue;
			var urlIcono = pois[i].getElementsByTagName("urlIcono")[0].firstChild.nodeValue;
			var lat = pois[i].getElementsByTagName("posx")[0].firstChild.nodeValue;
			var lon = pois[i].getElementsByTagName("posy")[0].firstChild.nodeValue;
			var usuario = pois[i].getElementsByTagName("usuario")[0].firstChild.nodeValue;
			var nombre = pois[i].getElementsByTagName("nombre")[0].firstChild.nodeValue;
			var contenido = pois[i].getElementsByTagName("contenido")[0].firstChild.nodeValue;
			var caracter = pois[i].getElementsByTagName("caracter")[0].firstChild.nodeValue;
			var punto = new GLatLng(lat,lon);
			var icono = dameIcono(urlIcono);
			//crea el marcador
    		var _marcador = new GMarker(punto, {icon: icono, draggable:true});
			_marcador.disableDragging(); //Por defecto esta fijo
			
			//Comprobamos si el usuarioActual tiene privilegios sobre esta marca
			var privilegios = false;
			if((usuarioActual == usuario) || (usuarioActual == "admin") || (usuario == "invitado")) privilegios = true;
			
			//aplica al marcador toda la información antes recibida
			_marcador.value = crearHtmlHuellaFija(idPOI,usuario,caracter,urlIcono,nombre,contenido, privilegios);
			_marcador.bindInfoWindowHtml(_marcador.value);
			
  			//actualiza los eventos del marcador
			GEvent.addListener(_marcador, "click", function() {	    
				this.openInfoWindowHtml(this.value);
			});	
		
			GEvent.addListener(_marcador, "dragstart", function() {                         
				gmap.closeInfoWindow();
			});	
			
			GEvent.addListener(_marcador, "dragend", function() {                         
				var posx = this.getPoint().lat();
				var posy = this.getPoint().lng();
				var idHuella = obtenerIdHuella(this.value);
				actualizarPosMarcador(idHuella,posx,posy,caracter);
			});					
			GEvent.addListener(_marcador, "dblclick", function() { 
				//this.openInfoWindowHtml("Lat: " + this.getPoint().lat() + "<br/>Lon: " + this.getPoint().lng());
			});
			
			//introduce el marcador en los arrays correspondientes
			PUBLICOSINF.push( {	marcador: _marcador, 
						  		id: idPOI} );
			PUBLICOS.push(_marcador);
		}
	}
}

function cargarPOIprotegidos(usuario) {
	peticionAjax2 = cargarAjax();
	if (peticionAjax2) {
    	peticionAjax2.onreadystatechange = cargarPOIprotegidosRespuesta;
    	peticionAjax2.open("POST", "consultasAjax/POI.php", false);
		peticionAjax2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax2.send("modificacion=no&publicos=no&protegidos=si&privados=no&usuario="+usuario+"&nocache="+Math.random());
  	}
}

function cargarPOIprotegidosRespuesta() {
	if(peticionAjaxCompleta(peticionAjax2)) {
		var documentoXML = peticionAjax2.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var pois = raiz.getElementsByTagName("poi");
		controladorMarcadoresProtegidos = new MarkerManager(gmap);
		for (var i=0; i<pois.length; i++) {	
			var idPOI = pois[i].getElementsByTagName("idPOI")[0].firstChild.nodeValue;
			var urlIcono = pois[i].getElementsByTagName("urlIcono")[0].firstChild.nodeValue;
			var lat = pois[i].getElementsByTagName("posx")[0].firstChild.nodeValue;			
			var lon = pois[i].getElementsByTagName("posy")[0].firstChild.nodeValue;
			var usuario = pois[i].getElementsByTagName("usuario")[0].firstChild.nodeValue;
			var nombre = pois[i].getElementsByTagName("nombre")[0].firstChild.nodeValue;
			var contenido = pois[i].getElementsByTagName("contenido")[0].firstChild.nodeValue;
			var caracter = pois[i].getElementsByTagName("caracter")[0].firstChild.nodeValue;
			var punto = new GLatLng(lat,lon);
			var icono = dameIcono(urlIcono);
 
    		var _marcador = new GMarker(punto, {icon: icono, draggable:true});	
			_marcador.disableDragging(); //Por defecto esta fijo
			_marcador.value = crearHtmlHuellaFija(idPOI,usuario,caracter,urlIcono,nombre,contenido, true);
			_marcador.bindInfoWindowHtml(_marcador.value);
			
			GEvent.addListener(_marcador, "click", function() {	    
				this.openInfoWindowHtml(this.value);
			});	 
			
			GEvent.addListener(_marcador, "dragstart", function() {                         
				gmap.closeInfoWindow();
			});	
			
			GEvent.addListener(_marcador, "dragend", function() {                         
				var posx = this.getPoint().lat();
				var posy = this.getPoint().lng();
				var idHuella = obtenerIdHuella(this.value);
				actualizarPosMarcador(idHuella,posx,posy,caracter);
			});					
			GEvent.addListener(_marcador, "dblclick", function() { 
				//this.openInfoWindowHtml("Lat: " + this.getPoint().lat() + "<br/>Lon: " + this.getPoint().lng());
			});
			
			PROTEGIDOSINF.push( {	marcador: _marcador, 
						  			id: idPOI} );
			PROTEGIDOS.push(_marcador);
		}
	}
}

function cargarPOIprivados(usuario) {
	peticionAjax3 = cargarAjax();
	if (peticionAjax3) {
    	peticionAjax3.onreadystatechange = cargarPOIprivadosRespuesta;
    	peticionAjax3.open("POST", "consultasAjax/POI.php", false);
		peticionAjax3.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax3.send("modificacion=no&publicos=no&protegidos=no&privados=si&usuario="+usuario+"&nocache=" + Math.random());
  	}
}

function cargarPOIprivadosRespuesta() {
	if(peticionAjaxCompleta(peticionAjax3)) {
		var documentoXML = peticionAjax3.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var pois = raiz.getElementsByTagName("poi");
		controladorMarcadoresPrivados = new MarkerManager(gmap);
		for (var i=0; i<pois.length; i++) {	
			var idPOI = pois[i].getElementsByTagName("idPOI")[0].firstChild.nodeValue;
			var urlIcono = pois[i].getElementsByTagName("urlIcono")[0].firstChild.nodeValue;
			var lat = pois[i].getElementsByTagName("posx")[0].firstChild.nodeValue;
			var lon = pois[i].getElementsByTagName("posy")[0].firstChild.nodeValue;
			var usuario = pois[i].getElementsByTagName("usuario")[0].firstChild.nodeValue;
			var nombre = pois[i].getElementsByTagName("nombre")[0].firstChild.nodeValue;
			var contenido = pois[i].getElementsByTagName("contenido")[0].firstChild.nodeValue;
			var caracter = pois[i].getElementsByTagName("caracter")[0].firstChild.nodeValue;
			var punto = new GLatLng(lat,lon);
			var icono = dameIcono(urlIcono);
			
    		var _marcador = new GMarker(punto, {icon: icono, draggable:true});		
			_marcador.disableDragging(); //Por defecto esta fijo
			_marcador.value = crearHtmlHuellaFija(idPOI,usuario,caracter,urlIcono,nombre,contenido, true);
			_marcador.bindInfoWindowHtml(_marcador.value);
			
			GEvent.addListener(_marcador, "click", function() {	    
				this.openInfoWindowHtml(this.value);
			});	  
			
			GEvent.addListener(_marcador, "dragstart", function() {                         
				gmap.closeInfoWindow();
			});	
			
			GEvent.addListener(_marcador, "dragend", function() {                         
				var posx = this.getPoint().lat();
				var posy = this.getPoint().lng();
				var idHuella = obtenerIdHuella(this.value);
				actualizarPosMarcador(idHuella,posx,posy,caracter);
			});					
			GEvent.addListener(_marcador, "dblclick", function() { 
				//this.openInfoWindowHtml("Lat: " + this.getPoint().lat() + "<br/>Lon: " + this.getPoint().lng());
			});
			
			PRIVADOSINF.push( {	marcador: _marcador, 
						  		id: idPOI} );
			PRIVADOS.push(_marcador);		
		}
	}
}

function cargarAmigosRespuesta() {
	if(peticionAjaxCompleta(peticionAjax4)) {
		var documentoXML = peticionAjax4.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var cjtoAmigos = raiz.getElementsByTagName("amigo");
		for (var i=0; i<cjtoAmigos.length; i++) { 		
			var nickAmigo = cjtoAmigos[i].getElementsByTagName("nickAmigo")[0].firstChild.nodeValue;
			var idAmigo = cjtoAmigos[i].getElementsByTagName("idAmigo")[0].firstChild.nodeValue;
			var estado = cjtoAmigos[i].getElementsByTagName("estado")[0].firstChild.nodeValue;
					/* El estado de la amistad se define así: 
						0: está pendiente de aceptación 
						1: amistad aceptada por idAmigo a la solicitud de idUsuario 
						2: amistad rechazada siempre por idAmigo a la solicitud de idUsuario 
					*/	
			if (estado == 1) AMIGOS.push(nickAmigo);
			else if (estado == 0) SOLICITUDES.push(nickAmigo);
		}
		mostrarAmigos();
		mostrarSolicitudes();
	}
}

function cargarIconos() {
	usuarioActual = "admin"; //CAMBIAR	
	peticionAjax5 = cargarAjax();
	if (peticionAjax5) {
    	peticionAjax5.onreadystatechange = cargarIconosRespuesta;
    	peticionAjax5.open("POST", "consultasAjax/iconos.php", false);
		peticionAjax5.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax5.send(	"modificacion=no&usuario="+usuarioActual+"&nocache=" + Math.random());
  	}
}

function cargarIconosRespuesta() {
	if(peticionAjaxCompleta(peticionAjax5)) {		
		var documentoXML = peticionAjax5.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var cjtoIconos = raiz.getElementsByTagName("icono");
		for (var i=0; i<cjtoIconos.length; i++) {	
			var urlIcono = cjtoIconos[i].getElementsByTagName("url")[0].firstChild.nodeValue;
			ICONOS.push(urlIcono);
		}
	}
}

function insertarPOI(posx,posy,usuario,nombre,contenido,urlIcono,categoria,caracter) { 
	peticionAjax6 = cargarAjax();
	if (peticionAjax6) {
    	peticionAjax6.onreadystatechange = insertarPOIRespuesta;
    	peticionAjax6.open("POST", "consultasAjax/POI.php", false);
		peticionAjax6.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax6.send(	"modificacion=si&insercion=si&eliminacion=no&actualizacion=no"+
							"&posx="+posx+"&posy="+posy+"&nick="+usuario+
							"&nombre="+nombre+"&contenido="+contenido+"&urlIcono="+urlIcono+
							"&categoria="+categoria+"&caracter="+caracter+
							"&nocache=" + Math.random());
  	}
}

function insertarPOIRespuesta() {
	if(peticionAjaxCompleta(peticionAjax6)) {
		/* Obtenemos el último ID puesto en la tabla de POIs */ 
		ultimoIdPOI = parseInt(peticionAjax6.responseText);	
	}
}

function actualizarSitioPOI(id,posx,posy) {
	peticionAjax71 = cargarAjax();
	if (peticionAjax71) {
    	peticionAjax71.onreadystatechange = actualizarPOIRespuesta;
    	peticionAjax71.open("POST", "consultasAjax/POI.php", false);
		peticionAjax71.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax71.send("modificacion=si&insercion=no&eliminacion=no&actualizacion=si"+
							"&sitio=si&informacion=no&icono=no&categoria=no&caracter=no"+
							"&id="+id+"&posx="+posx+"&posy="+posy+
							"&nocache=" + Math.random());
  	}
}

function actualizarInformacionPOI(id,nombre,contenido) {
	peticionAjax72 = cargarAjax();
	if (peticionAjax72) {
    	peticionAjax72.onreadystatechange = actualizarPOIRespuesta;
    	peticionAjax72.open("POST", "consultasAjax/POI.php", false);
		peticionAjax72.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax72.send("modificacion=si&insercion=no&eliminacion=no&actualizacion=si"+
							"&sitio=no&informacion=si&icono=no&categoria=no&caracter=no"+
							"&id="+id+"&nombre="+nombre+"&contenido="+contenido+
							"&nocache=" + Math.random());
  	}
}

function actualizarIconoPOI(id,urlIcono) {
	peticionAjax73 = cargarAjax();
	if (peticionAjax73) {
    	peticionAjax73.onreadystatechange = actualizarPOIRespuesta;
    	peticionAjax73.open("POST", "consultasAjax/POI.php", false);
		peticionAjax73.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax73.send("modificacion=si&insercion=no&eliminacion=no&actualizacion=si"+
							"&sitio=no&informacion=no&icono=si&categoria=no&caracter=no"+
							"&id="+id+"&icono="+icono+
							"&nocache=" + Math.random());
  	}
}

function actualizarCategoriaPOI(id,categoria) {
	peticionAjax74 = cargarAjax();
	if (peticionAjax74) {
    	peticionAjax74.onreadystatechange = actualizarPOIRespuesta;
    	peticionAjax74.open("POST", "consultasAjax/POI.php", false);
		peticionAjax74.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax74.send("modificacion=si&insercion=no&eliminacion=no&actualizacion=si"+
							"&sitio=no&informacion=no&icono=no&categoria=si&caracter=no"+
							"&id="+id+"&categoria="+categoria+
							"&nocache=" + Math.random());
  	}
}

function actualizarCaracterPOI(id,caracter) {
	peticionAjax75 = cargarAjax();
	if (peticionAjax75) {
    	peticionAjax75.onreadystatechange = actualizarPOIRespuesta;
    	peticionAjax75.open("POST", "consultasAjax/POI.php", false);
		peticionAjax75.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax75.send("modificacion=si&insercion=no&eliminacion=no&actualizacion=si"+
							"&sitio=no&informacion=no&icono=no&categoria=no&caracter=si"+
							"&id="+id+"&caracter="+caracter+
							"&nocache=" + Math.random());
  	}
}

function actualizarPOIRespuesta() {
	if(peticionAjaxCompleta(peticionAjax7)) {	
		/* No se hace nada */
	}
}

function eliminarPOI(id) {
	peticionAjax8 = cargarAjax();
	if (peticionAjax8) {
    	peticionAjax8.onreadystatechange = eliminarPOIRespuesta;
    	peticionAjax8.open("POST", "consultasAjax/POI.php", false);
		peticionAjax8.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax8.send(	"modificacion=si&insercion=no&eliminacion=si&actualizacion=no"+
							"&id="+id+
							"&nocache=" + Math.random());
  	}
}

function eliminarPOIRespuesta() {
	if(peticionAjaxCompleta(peticionAjax8)) {
		/* No se hace nada */
	}
}

function insertarAmigo() {
	//Borramos el texto de error anterior
	document.getElementById("textoErrorAmigos").innerHTML ="";	
	var amigo= document.getElementById("nickNuevoAmigo").value;	
	peticionAjax9 = cargarAjax();
	if (peticionAjax9) {
    	peticionAjax9.onreadystatechange = insertarAmigoRespuesta;
    	peticionAjax9.open("POST", "consultasAjax/amigos.php", false);
		peticionAjax9.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax9.send("modificacion=si&envio=si&usuario="+usuarioActual+"&amigo="+amigo+"&nocache=" + Math.random());		
  	}
}

function insertarAmigoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax9)) {	
		var documentoXML = peticionAjax9.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var amigo = raiz.getElementsByTagName("amigo");
		var existeAmigo = amigo[0].getElementsByTagName("existe")[0].firstChild.nodeValue;
		var movimientoAnterior = amigo[0].getElementsByTagName("movimientoAnterior")[0].firstChild.nodeValue;
		var esElMismo = amigo[0].getElementsByTagName("esElMismo")[0].firstChild.nodeValue;
		var amistadPendiente = amigo[0].getElementsByTagName("pendiente")[0].firstChild.nodeValue;
		var amistadRechazada = amigo[0].getElementsByTagName("rechazada")[0].firstChild.nodeValue;
		var nickAmigo = amigo[0].getElementsByTagName("nickAmigo")[0].firstChild.nodeValue;
		var idAmigo = amigo[0].getElementsByTagName("idAmigo")[0].firstChild.nodeValue;
		if (existeAmigo == "no") {
			errorAmigo("El usuario indicado no existe");
		}
		else {
			if (esElMismo == "si") { 
				errorAmigo ("No te puedes añadir a tí como amigo");
			}
			else {
				if (movimientoAnterior == "no") {
					//AMIGOS.push(nickAmigo);
					//mostrarAmigos();
					errorAmigo("Solicitud de amistad enviada a "+nickAmigo);
				}
				else {
					if (amistadPendiente == "si") {
						errorAmigo("Solicitud de amistad con "+nickAmigo+" ya enviada");
					}
					else {
						if (amistadRechazada == "si") {
							errorAmigo(nickAmigo +" ha rechazado tu solicitud de amistad");
						}
						else {
							errorAmigo(nickAmigo + " ya es tu amigo");
						}
					}
				}
			}
		}
	}
}

function eliminarAmigo(amigo) {
	peticionAjax10 = cargarAjax();
	if (peticionAjax10) {
    	peticionAjax10.onreadystatechange = eliminarAmigoRespuesta;
    	peticionAjax10.open("POST", "consultasAjax/amigos.php", true);
		peticionAjax10.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax10.send("modificacion=si&eliminacion=si&usuario="+usuarioActual+"&amigo="+amigo+"&nocache=" + Math.random());
  	}
}

function eliminarAmigoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax10)) {	
		if (esPaginaAmigosVacia()) 
			paginaActualAmigos--;
		mostrarAmigos();
	}
}

function insertarIcono(posx,posy,usuario,nombre,contenido,urlIcono,categoria,caracter) {
}

function insertarIconoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax11)) {		
	}
}

function actualizarIcono(posx,posy,usuario,nombre,contenido,urlIcono,categoria,caracter) {
}

function actualizarIconoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax12)) {		
	}
}

function eliminarIcono(posx,posy,usuario,nombre,contenido,urlIcono,categoria,caracter) {
}

function eliminarIconoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax13)) {		
	}
}

function cargarPOIAmigo(usuario) {
	peticionAjax14 = cargarAjax();
	if (peticionAjax14) {
    	peticionAjax14.onreadystatechange = cargarPOIAmigoRespuesta;
    	peticionAjax14.open("POST", "consultasAjax/POI.php", false);
		peticionAjax14.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax14.send("modificacion=no&publicos=no&protegidos=si&privados=no&usuario="+usuario+"&nocache="+Math.random());
  	}
}

function cargarPOIAmigoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax14)) {
		var documentoXML = peticionAjax14.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var pois = raiz.getElementsByTagName("poi");
		controladorMarcadoresAmigos = new MarkerManager(gmap);
		for (var i=0; i<pois.length; i++) {	
			var idPOI = pois[i].getElementsByTagName("idPOI")[0].firstChild.nodeValue;
			var urlIcono = pois[i].getElementsByTagName("urlIcono")[0].firstChild.nodeValue;
			var lat = pois[i].getElementsByTagName("posx")[0].firstChild.nodeValue;			
			var lon = pois[i].getElementsByTagName("posy")[0].firstChild.nodeValue;
			var usuario = pois[i].getElementsByTagName("usuario")[0].firstChild.nodeValue;
			var nombre = pois[i].getElementsByTagName("nombre")[0].firstChild.nodeValue;
			var contenido = pois[i].getElementsByTagName("contenido")[0].firstChild.nodeValue;
			var caracter = pois[i].getElementsByTagName("caracter")[0].firstChild.nodeValue;
			var punto = new GLatLng(lat,lon);
			var icono = dameIcono(urlIcono);
 
    		var _marcador = new GMarker(punto, {icon: icono, draggable:true});	
			_marcador.disableDragging(); //Por defecto esta fijo
			_marcador.value = crearHtmlHuellaFija(idPOI,usuario,caracter,urlIcono,nombre,contenido, false);
			_marcador.bindInfoWindowHtml(_marcador.value);
			
			GEvent.addListener(_marcador, "click", function() {	    
				this.openInfoWindowHtml(this.value);
			});	 
			
			GEvent.addListener(_marcador, "dragstart", function() {                         
				gmap.closeInfoWindow();
			});	
			
			GEvent.addListener(_marcador, "dragend", function() {                         
				var posx = this.getPoint().lat();
				var posy = this.getPoint().lng();
				var idHuella = obtenerIdHuella(this.value);
				actualizarPosMarcador(idHuella,posx,posy,caracter);
			});					
			GEvent.addListener(_marcador, "dblclick", function() { 
				//this.openInfoWindowHtml("Lat: " + this.getPoint().lat() + "<br/>Lon: " + this.getPoint().lng());
			});
			MARCADORESAMIGOSINF.push( {	marcador: _marcador, 
						  			id: idPOI, idAmigo: usuario} );
			MARCADORESAMIGOS.push(_marcador);
		}
	}
}

function cargarAmigos() {
	peticionAjax4 = cargarAjax();
	if (peticionAjax4) {
    	peticionAjax4.onreadystatechange = cargarAmigosRespuesta;
    	peticionAjax4.open("POST", "consultasAjax/amigos.php", false);
		peticionAjax4.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax4.send("modificacion=no&usuario="+usuarioActual+"&amigo=noimporta&nocache=" + Math.random());
  	}
}

function aceptarAmigo(amigo) {
	peticionAjax15 = cargarAjax();
	if (peticionAjax15) {
		//Eliminamos al amigo del array de solicitudes y lo añadimos al de amigos
		for(var i=0;i < SOLICITUDES.length;i++) {
		  if (SOLICITUDES[i] == amigo) SOLICITUDES.splice(i, 1);
	  	}
		AMIGOS.push(amigo);
    	peticionAjax15.onreadystatechange = aceptarAmigoRespuesta;
    	peticionAjax15.open("POST", "consultasAjax/amigos.php", true);
		peticionAjax15.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax15.send("modificacion=si&aceptacion=si&usuario="+usuarioActual+"&amigo="+amigo+"&nocache=" + Math.random());
  	}	
}

function aceptarAmigoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax15)) {
		mostrarAmigos();	
		if (esPaginaSolicitudVacia())
			paginaActualSolicitudes--;
		mostrarSolicitudes();
	}
}

function rechazarAmigo(amigo) {
	peticionAjax16 = cargarAjax();
	if (peticionAjax16) {
		//Eliminamos al amigo del array de solicitudes
		for(var i=0;i < SOLICITUDES.length;i++) {
		  if (SOLICITUDES[i] == amigo) SOLICITUDES.splice(i, 1);
	  	}
    	peticionAjax16.onreadystatechange = rechazarAmigoRespuesta;
    	peticionAjax16.open("POST", "consultasAjax/amigos.php", true);
		peticionAjax16.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax16.send("rechazo=si&usuario="+usuarioActual+"&amigo="+amigo+"&nocache=" + Math.random());
  	}	
}

function rechazarAmigoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax16)) {
		if (esPaginaSolicitudVacia())
			paginaActualSolicitudes--;		
		mostrarSolicitudes();
	}
}

function rechazarSiempreAmigo(amigo) {
	peticionAjax17 = cargarAjax();
	if (peticionAjax17) {
		//Eliminamos al amigo del array de solicitudes
		for(var i=0;i < SOLICITUDES.length;i++) {
		  if (SOLICITUDES[i] == amigo) SOLICITUDES.splice(i, 1);
	  	}
    	peticionAjax17.onreadystatechange = rechazarSiempreAmigoRespuesta;
    	peticionAjax17.open("POST", "consultasAjax/amigos.php", true);
		peticionAjax17.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax17.send("rechazosiempre=si&usuario="+usuarioActual+"&amigo="+amigo+"&nocache=" + Math.random());
  	}	
}

function rechazarSiempreAmigoRespuesta() {
	if(peticionAjaxCompleta(peticionAjax17)) {
		if (esPaginaSolicitudVacia())
			paginaActualSolicitudes--;
		mostrarSolicitudes();
	}
}

function cargarTiempo() {
	peticionAjax18 = cargarAjax();
	if (peticionAjax18) {
    	peticionAjax18.onreadystatechange = cargarTiempoRespuesta;
		peticionAjax18.open("POST", "consultasAjax/tiempo.php", false);
		peticionAjax18.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjax18.send("nocache=" + Math.random());
  	}
}

function cargarTiempoRespuesta() {	
	if(peticionAjaxCompleta(peticionAjax18)) {	
		var documentoXML = peticionAjax18.responseXML;
		var raiz = documentoXML.getElementsByTagName("respuesta")[0];
		var diaSemana = raiz.getElementsByTagName("diaSemana")[0];
		var diasSemana = new Array();
		var i=0;
		var cjtoDiasSemana = diaSemana.getElementsByTagName("dia");
		for (i=0; i<cjtoDiasSemana.length; i++) {
			diasSemana[i] = cjtoDiasSemana[i].firstChild.nodeValue;
		}
		controladorTiempo = new MarkerManager(gmap);
		var cjtoLocalidades = raiz.getElementsByTagName("localidad");
		for (i=0; i<cjtoLocalidades.length; i++) {
			var informacion = "";
			var errorLocalidad = cjtoLocalidades[i].getElementsByTagName("error")[0].firstChild.nodeValue;
			if (errorLocalidad == "no") {
				var id = cjtoLocalidades[i].getElementsByTagName("id")[0].firstChild.nodeValue;
				var nombre = cjtoLocalidades[i].getElementsByTagName("nombre")[0].firstChild.nodeValue;
				var latitud = cjtoLocalidades[i].getElementsByTagName("latitud")[0].firstChild.nodeValue;
				var longitud = cjtoLocalidades[i].getElementsByTagName("longitud")[0].firstChild.nodeValue;
				var zoom = cjtoLocalidades[i].getElementsByTagName("zoom")[0].firstChild.nodeValue;
				var provincia = cjtoLocalidades[i].getElementsByTagName("provincia")[0].firstChild.nodeValue;
				var pais = cjtoLocalidades[i].getElementsByTagName("pais")[0].firstChild.nodeValue;
				var continente = cjtoLocalidades[i].getElementsByTagName("continente")[0].firstChild.nodeValue;
				var cjtoDias = cjtoLocalidades[i].getElementsByTagName("dia");	
				var informacion = "<p>Localidad: " + nombre + "Provincia: " + provincia + "</p><!--<p>País: " + pais + "</p><p>Continente: " + continente + "</p><p>Latitud: " + latitud + "</p><p>Longitud: " + longitud + "</p>-->";
				for (var dia=0; dia<=0/*cjtoDias.length*/; dia++) {
					var minima = cjtoDias[dia].getElementsByTagName("minima")[0].firstChild.nodeValue;
					var maxima = cjtoDias[dia].getElementsByTagName("maxima")[0].firstChild.nodeValue;
					var tiempoA = cjtoDias[dia].getElementsByTagName("tiempo")[0].firstChild.nodeValue;	
					informacion = informacion + "<p>Predicci&oacute;n para hoy " + diasSemana[dia] + ":</p>  <p> minima: " + minima + "\n   maxima: " + maxima + "</p><p>   tiempo: " + tiempoA + "</p>";
					//alert(informacion);
					var urlIcono= dameUrlTiempo(tiempoA);
					var icono = dameIconoTiempo(urlIcono);
					var punto = new GLatLng(latitud,longitud);
					var _marcador = new GMarker(punto, {icon: icono, draggable:true});
					_marcador.disableDragging();
					_marcador.value = crearHtmlTiempo(informacion);
					_marcador.bindInfoWindowHtml(_marcador.value);
					//actualiza los eventos del marcador
					/*GEvent.addListener(_marcador, "click", function() {	    
					});	
					GEvent.addListener(_marcador, "dragstart", function() {                         
					});	
					GEvent.addListener(_marcador, "dragend", function() {                         
					});					
					GEvent.addListener(_marcador, "dblclick", function() { 
					});*/
					//introduce el marcador en el array correspondiente
					TIEMPO[zoom].push(_marcador);
					/*TIEMPO.push(_marcador);*/
				}
			}
		}
		//Habilitamos el boton de carga cuando se termine de cargar todo
		//Cambio la clase del icono a activado
		if (!tiempoActivado){
		  document.getElementById("generales_tiempo").className = "ImgAdTiempo";
		  document.getElementById("generales_tiempo").src="images/generales_plantilla.png"
		}
		cargaTiempoFin = true;
	}
}

/* _______________________________________________________________________________________________________________________ */

/* Cierto si se está en la última página de amigos, sólo queda uno en ella y dicho amigo se elimina */
function esPaginaAmigosVacia() {
	var numAmigos = AMIGOS.length;
	if ((numAmigos % AMIGOS_POR_PAGINA) == 0 && 
			((paginaActualAmigos-1) * AMIGOS_POR_PAGINA) == numAmigos)
		return true;
	return false;
}

/* Cierto si se está en la última página de solicitudes, sólo queda una solicitud en ella y dicha solicitud se acepta o rechaza */
function esPaginaSolicitudVacia() {
	var numSolicitudes = SOLICITUDES.length;
	if ((numSolicitudes % SOLICITUDES_POR_PAGINA) == 0 && 
			((paginaActualSolicitudes-1) * SOLICITUDES_POR_PAGINA) == numSolicitudes)
		return true;
	return false;
}

function dameIcono(url) {
  	var icono = new GIcon(G_DEFAULT_ICON);
  	icono.image = "iconos/" + url;
	icono.iconSize = new GSize(20, 34); 
  	return icono;
}

function dameIconoTiempo(url) {
  	var icono = new GIcon(G_DEFAULT_ICON);
  	icono.image = "iconos/tiempo/" + url;
	icono.iconSize = new GSize(55,55);
  	return icono;
}

function dameUrlTiempo(tiempoA) {
	var url = "vacio.gif";
	switch (tiempoA) {
		case "Despejado": 												url = "sol.png"; break;
		case "Intervalos Nubosos": 										url = "IntervalosNubosos_.png"; break;
		case "Intervalos nubosos con lluvia debil": 					url = "IntervalosNubosos_lluviaDebil.png"; break;
		case "Intervalos nubosos con lluvia moderada": 					url = "IntervalosNubosos_lluviaModerada.png"; break;
		case "Intervalos nubosos con chubascos tormentosos": 			url = "IntervalosNubosos_tormenta.png"; break;
		case "Intervalos nubosos con chubascos tormentosos y granizo": 	url = "IntervalosNubosos_tormentaGranizo.png"; break;
		case "Intervalos nubosos con nevadas": 							url = "IntervalosNubosos_nevadas.png"; break;
		
		case "Cielos Nubosos": 											url = "CielosNubosos_.png"; break;
		case "Cielos nubosos con lluvia debil": 						url = "CielosNubosos_lluviaDebil.png"; break;
		case "Cielos nubosos con lluvia moderada": 						url = "CielosNubosos_lluviaModerada.png"; break;
		case "Cielos nubosos con chubascos tormentosos":	 			url = "CielosNubosos_tormenta.png"; break;
		case "Cielos nubosos con chubascos tormentosos y granizo": 		url = "CielosNubosos_tormentaGranizo.png"; break;
		case "Cielos nubosos con nevadas": 								url = "CielosNubosos_nevadas.png"; break;
		
		case "Cielos Cubiertos": 										url = "CielosCubiertos_.png"; break;
		case "Cielos cubiertos con lluvia debil": 						url = "CielosCubiertos_lluviaDebil.png"; break;
		case "Cielos cubiertos con lluvia moderada": 					url = "CielosCubiertos_lluviaModerada.png"; break;
		case "Cielos cubiertos con chubascos tormentosos": 				url = "CielosCubiertos_tormenta.png"; break;
		case "Cielos cubiertos con chubascos tormentosos y granizo": 	url = "CielosCubiertos_tormentaGranizo.png"; break;
		case "Cielos cubiertos con nevadas": 							url = "CielosCubiertos_nevadas.png"; break;
		
		default: 														url = "vacio.gif";
	}	
  	return url;
}

function ad_tiempo(){
	if (!tiempoActivado) { //si el tiempo está desactivado:		  
		tiempoActivado = true;
		//Cambio la clase del icono a activado
		 document.getElementById("generales_tiempo").className = "ImgAdTiempoActivados";
	/*	controladorTiempo.addMarkers(TIEMPO, 0);*/
	/*	controladorTiempo.addMarkers(TIEMPOAUX[1],6, 14);  //Resto*/
		controladorTiempo.addMarkers(TIEMPO[4],4, 14);  //Capital pais
		controladorTiempo.addMarkers(TIEMPO[5],5, 14);  //Capital autonomia*/
		controladorTiempo.addMarkers(TIEMPO[6],6, 14);  //provincias*/
		/*PRUEBAS controladorTiempo.addMarkers(PUBLICOS,5);*/
		controladorTiempo.refresh();
	}
	else { //si el tiempo está activado:
		tiempoActivado = false;		
		//Cambio la clase del icono a desactivado
		document.getElementById("generales_tiempo").className = "ImgAdTiempo";
		controladorTiempo.clearMarkers();
	}
}

function ad_publicos(){
	if (!publicosActivados)	{ //si estan desactivados:		  
		publicosActivados = true;
		//Cambio la clase del icono a activado
		document.getElementById("generales_publicos").className = "ImgAdPublicosActivados";
		controladorMarcadoresPublicos.addMarkers(PUBLICOS,0);
		controladorMarcadoresPublicos.refresh();
	}
	else { //si estan descactivados:
		publicosActivados = false;		
		//Cambio la clase del icono a desactivado
		document.getElementById("generales_publicos").className = "ImgAdPublicos";
		controladorMarcadoresPublicos.clearMarkers();
	}
}

function ad_protegidos(){
	if (usuarioActual == "invitado") alert("Necesitas estar registrado para tener huellas protegidas");
	if (!protegidosActivados)	{		  
		protegidosActivados = true;
		document.getElementById("generales_protegidos").className = "ImgAdProtegidosActivados";
		controladorMarcadoresProtegidos.addMarkers(PROTEGIDOS,0);
   		controladorMarcadoresProtegidos.refresh();
	}
	else { 
		protegidosActivados = false;
		document.getElementById("generales_protegidos").className = "ImgAdProtegidos";
		controladorMarcadoresProtegidos.clearMarkers();
	}
}

function ad_privados(){
	if (usuarioActual == "invitado") alert("Necesitas estar registrado para tener huellas privadas");
	if (!privadosActivados)	{		
		privadosActivados = true;
		document.getElementById("generales_privados").className = "ImgAdPrivadosActivados";
		controladorMarcadoresPrivados.addMarkers(PRIVADOS,0);
   		controladorMarcadoresPrivados.refresh();
	}
	else { 
		privadosActivados = false;
		document.getElementById("generales_privados").className = "ImgAdPrivados";
		controladorMarcadoresPrivados.clearMarkers();
	}
}

function ad_fronteras(){
	if (!fronterasActivados)	{		  
		fronterasActivados = true;
		document.getElementById("generales_fronteras").className = "ImgAdFronterasActivados";
        gmap.addOverlay(geoXmlEspana);
	}
	else { //si estan descactivados:
		fronterasActivados = false;	
		document.getElementById("generales_fronteras").className = "ImgAdFronteras";
		gmap.removeOverlay(geoXmlEspana);
	}
}

/*************************** CREAR, MODIFICAR Y ELIMINAR HUELLAS ***********************************/

function activarMarcar(usuario){	

    GEvent.clearListeners(gmap, "click");
	GEvent.addListener(gmap, "click", function(marcador, punto) {			
		//Si no se ha pulsado sobre ningún marcador
		if (marcador == null) {
			/* OBTENCIÓN DE LA INFORMACIÓN */
			var caracter = "privado";
			var elementos = document.getElementsByName("CaracterHuella");
			for(var i=0; i<elementos.length; i++) {
				if (elementos[i].checked) caracter = elementos[i].value; 
			}					
			var urlIcono = "1.png";
			var elementos = document.getElementsByName("nuevoIcono");
			for(var i=0; i<elementos.length; i++) {
				if (elementos[i].checked) urlIcono = elementos[i].value; 
			}
			var icono = dameIcono(urlIcono);	                        
			var posx = punto.y;
			var posy = punto.x;
			
			/* INSERCIÓN EN LA BASE DE DATOS */        
			insertarPOI(posx,posy,usuario,null,null,urlIcono,1,caracter);
			
			/* INSERCIÓN EN EL MAPA */
			var nuevoMarcador = new GMarker(punto, {icon: icono, draggable:true, bouncy:true });
			var idpoi = ultimoIdPOI;						
			nuevoMarcador.value = crearHtmlHuellaEditar(idpoi,usuario,caracter,urlIcono);
			nuevoMarcador.bindInfoWindowHtml(nuevoMarcador.value);			
			if (caracter == "publico") {
				controladorMarcadoresPublicos.addMarker(nuevoMarcador,0);			
				/* INSERCIÓN EN LOS ARRAYS CORRESPONDIENTES */
				PUBLICOSINF.push( { marcador: nuevoMarcador, 
						  			id: idpoi} );
				PUBLICOS.push(nuevoMarcador);				
			}
			else if (caracter == "protegido") {
				controladorMarcadoresProtegidos.addMarker(nuevoMarcador,0);			
				/* INSERCIÓN EN LOS ARRAYS CORRESPONDIENTES */
				PROTEGIDOSINF.push( { 	marcador: nuevoMarcador, 
						  				id: idpoi} );
				PROTEGIDOS.push(nuevoMarcador);				
			}
			else if (caracter = "privado") {
				controladorMarcadoresPrivados.addMarker(nuevoMarcador,0);			
				/* INSERCIÓN EN LOS ARRAYS CORRESPONDIENTES */
				PRIVADOSINF.push( { marcador: nuevoMarcador, 
						  			id: idpoi} );
				PRIVADOS.push(nuevoMarcador);				
			}
									                   
			/* EVENTOS DEL NUEVO MARCADOR */
  			GEvent.addListener(nuevoMarcador, "click", function() {	    
				this.openInfoWindowHtml(this.value);
			});	               
			GEvent.addListener(nuevoMarcador, "dragend", function() {                         
				var posx = this.getPoint().lat();
				var posy = this.getPoint().lng();
				actualizarPosMarcador(idpoi,posx,posy,caracter);
			});					
			GEvent.addListener(nuevoMarcador, "dblclick", function() { 
				//this.openInfoWindowHtml("Lat: " + this.getPoint().lat() + "<br/>Lon: " + this.getPoint().lng());
			});                     
		}
	});
}

function desactivarMarcar() {
	GEvent.clearListeners(gmap, "click");
	GEvent.addListener(gmap, "click", null);
}

function obtenerIdHuella(html) {
//esta función devuelve el id de una huella o marcador dada su información en html 
	var informacionMarcador = document.createElement('div'); 
	informacionMarcador.innerHTML = html; 
	var elementosDiv = informacionMarcador.getElementsByTagName("div");
	var idHuella = elementosDiv[1].getAttribute("title");
	return idHuella;
}

function crearHtmlHuellaEditar(idpoi,usuario,caracter,urlIcono,nombre,contenido) {
//esta función crea el html de información de una determinada huella o marcador
	var nombreHtml = "";
	var contenidoHtml = "";
	var ncontenido = "";
	var nnombre = "";
	if (contenido == "null") ncontenido = ""; 
	else ncontenido = contenido;
	if (nombre == "null") nnombre = "";
	else nnombre = nombre;
	//comprueba si se ha pasado el nombre y el contenido como parámetro, lo que significa que se han modificado
	if (arguments.length == 6)  {
		nombreHtml = 'value="'+nnombre+'"';
		contenidoHtml = ncontenido;
	}
	var htmlHuella = '<div id="huella" style="max-width:250px;"> <div id="idHuella" title='+ idpoi +'> </div> <div id="usuarioHuella" title='+ usuario +'></div> <div id="caracterHuella" title='+ caracter +'></div> <div id="iconoHuella" title='+ urlIcono +'></div> <form id="formHuella" name="formHuella" method="post" action=""> <p><label for="nombreHuella">Nombre: </label> <input name="nombreHuella" type="text" id="nombreHuella"'+ nombreHtml +'/> </p> <p><label for="contenidoHuella">Contenido: </label> <textarea name="contenidoHuella" id="contenidoHuella" cols="45" rows="5">'+ contenidoHtml +'</textarea></p></form> <a href="javascript: void actualizarInfoMarcador()"> Guardar informaci&oacute;n </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript: void eliminarMarcador()"> Borrar huella </a> </p>';	
	var extraInfo = '<p> <strong>Car&aacute;cter:</strong> '+ caracter +'&nbsp;&nbsp;&nbsp; <strong>Usuario:</strong> '+usuario+'</p></div>';
	return htmlHuella + extraInfo;
}

function crearHtmlHuellaFija(idpoi,usuario,caracter,urlIcono,nombre,contenido, privilegios) {
//esta función crea el html de información de una determinada huella o marcador
	var nombreHtml = "";
	var contenidoHtml = "";
	var htmlPrivilegios = "";
	var ncontenido = "";
	var nnombre = "";
	if (contenido == "null") ncontenido = ""; 
	else ncontenido = contenido;
	if (nombre == "null") nnombre = "";
	else nnombre = nombre;
	//comprueba si se ha pasado el nombre y el contenido como parámetro, lo que significa que se han modificado
	if (arguments.length == 7)  {
		nombreHtml = 'title="'+nnombre+'"';
		contenidoHtml = ncontenido;
	}

	var htmlHuella = '<div id="huella" style="max-width:250px;"> <div id="idHuella" title='+ idpoi +'> </div> <div id="usuarioHuella" title='+ usuario +'></div> <div id="caracterHuella" title='+ caracter +'></div> <div id="iconoHuella" title='+ urlIcono +'></div><div name="nombreHuella" title="'+ nnombre +'" id="nombreHuella"'+ nombreHtml +'/><h3>'+ nnombre +'</h3></div><div name="contenidoHuella" id="contenidoHuella" title="'+ ncontenido +'">'+ contenidoHtml + /*crearHtmlFotos(usuario,idpoi) +*/ '</div>';	
	
	if (privilegios) htmlPrivilegios= '<p><a href="javascript: void editarInfoMarcador()"> Editar </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript: void eliminarMarcador()"> Borrar huella </a> </p>';
	
	var extraInfo = '<p> <strong>Car&aacute;cter:</strong> '+ caracter +'&nbsp;&nbsp;&nbsp; <strong>Usuario:</strong> '+usuario+'</p></div>';
	
	return htmlHuella + htmlPrivilegios +extraInfo;
}

function crearHtmlFotos(usuario,idpoi) {
	var htmlFotos = "";
	var carpeta = "usuarios/" + usuario + "/fotos/";
	
	htmlFotos = '<div id="galeria" style="width: auto"><a href="javascript: void anteriorFoto()"><</a>';
	if (fotosUsuario != null) {
		for (var i=0; i<fotosUsuario.length; i++) {	
			var foto = new RegExp("^" + idpoi);
			if (foto.test(fotosUsuario[i]) == true) {
				alert("HOLA");
			}
		}
	}
	htmlFotos = htmlFotos + '<img src="' + carpeta + 'Photo_00007.jpg" alt="#" title="#" WIDTH=200 HEIGHT=100/>'; 
	htmlFotos = htmlFotos + '<a href="javascript: void siguienteFoto()">></a> </div>'; 
	
	return htmlFotos;
}

function crearHtmlTiempo(informacion) {
//esta función crea el html de información de un marcador de tiempo
	var htmlTiempo = '<div id="tiempo" style="max-width:250px;">'+ informacion +'</div>';	
	
	return htmlTiempo;
}

function buscarMarcadorArray(idMarcador,tipo) {
//esta función devuelve el marcador del array al que pertenece dado su identificador
	/*  publico -> tipo=0
		protegido -> tipo=1
		privado -> tipo=2 	*/
	var encontrado = false;
	var posMarcador = -1;
	var marcadorBuscado = null;	
	if (tipo==0) {
		for (var i=0; i<PUBLICOSINF.length && !encontrado; i++) {
			if (idMarcador == PUBLICOSINF[i].id) {
				encontrado = true;
				marcadorBuscado = PUBLICOSINF[i].marcador;
				posMarcador = i;
			}		
		}
	}
	else if (tipo==1) {
		for (var i=0; i<PROTEGIDOSINF.length && !encontrado; i++) {
			if (idMarcador == PROTEGIDOSINF[i].id) {
				encontrado = true;
				marcadorBuscado = PROTEGIDOSINF[i].marcador;
				posMarcador = i;
			}		
		}
	}
	else if (tipo==2) {
		for (var i=0; i<PRIVADOSINF.length && !encontrado; i++) {
			if (idMarcador == PRIVADOSINF[i].id) {
				encontrado = true;
				marcadorBuscado = PRIVADOSINF[i].marcador;
				posMarcador = i;
			}		
		}
	}
	return {marcador: marcadorBuscado, pos: posMarcador};	
}

function eliminarMarcador() {
	//obtiene el caracter y el identificador del marcador que se quiere eliminar
	var caracterMarca = document.getElementById("caracterHuella").title;	
	var idMarca = document.getElementById("idHuella").title;
	
	var marcadorEliminado = null;
	//obtiene el marcador que se quiere eliminar
	if (caracterMarca == "publico") {
		marcadorEliminado = buscarMarcadorArray(idMarca,0);		
		/* ELIMINACIÓN EN LOS ARRAYS CORRESPONDIENTES Y DEL CONTROLADOR */
		PUBLICOSINF.splice(marcadorEliminado.pos, 1);
		PUBLICOS.splice(marcadorEliminado.pos, 1);
		controladorMarcadoresPublicos.removeMarker(marcadorEliminado.marcador);
	}
	else if (caracterMarca == "protegido") {
		marcadorEliminado = buscarMarcadorArray(idMarca,1);
		/* ELIMINACIÓN EN LOS ARRAYS CORRESPONDIENTES Y DEL CONTROLADOR */
		PROTEGIDOSINF.splice(marcadorEliminado.pos, 1);
		PROTEGIDOS.splice(marcadorEliminado.pos, 1);
		controladorMarcadoresProtegidos.removeMarker(marcadorEliminado.marcador);
	}
	else if (caracterMarca = "privado") {
		marcadorEliminado = buscarMarcadorArray(idMarca,2);
		/* ELIMINACIÓN EN LOS ARRAYS CORRESPONDIENTES Y DEL CONTROLADOR*/
		PRIVADOSINF.splice(marcadorEliminado.pos, 1);
		PRIVADOS.splice(marcadorEliminado.pos, 1);
		controladorMarcadoresPrivados.removeMarker(marcadorEliminado.marcador);
	}
	
	/* ELIMINACIÓN EN LA BASE DE DATOS */
	eliminarPOI(idMarca);
}

function actualizarPosMarcador(id,posx,posy,caracterMarca) {
	//obtiene el marcador sobre el que se quiere modificar la posición
	var marcadorModificado = null;	
	if (caracterMarca == "publico") {
		marcadorModificado = buscarMarcadorArray(id,0);			
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PUBLICOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PUBLICOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	else if (caracterMarca == "protegido") {
		marcadorModificado = buscarMarcadorArray(id,1);				
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PROTEGIDOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PROTEGIDOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	else if (caracterMarca = "privado") {
		marcadorModificado = buscarMarcadorArray(id,2);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PRIVADOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PRIVADOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	
	/* MODIFICACIÓN EN LA BASE DE DATOS */
	actualizarSitioPOI(id,posx,posy);
}

function actualizarInfoMarcador() {
	var idMarca = document.getElementById("idHuella").title;
	var usuarioMarca = document.getElementById("usuarioHuella").title;
	var iconoMarca = document.getElementById("iconoHuella").title;
	var caracterMarca = document.getElementById("caracterHuella").title;
	var nombreMarca = document.getElementById("nombreHuella").value;
	var contenidoMarca = document.getElementById("contenidoHuella").value;

		
	//cierra la ventana de información editable
	gmap.closeInfoWindow();
	
	//obtiene el marcador sobre el que se quiere modificar la información
	var marcadorModificado = null;	
	if (caracterMarca == "publico") {
		marcadorModificado = buscarMarcadorArray(idMarca,0);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PUBLICOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PUBLICOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	else if (caracterMarca == "protegido") {
		marcadorModificado = buscarMarcadorArray(idMarca,1);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PROTEGIDOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PROTEGIDOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	else if (caracterMarca = "privado") {
		marcadorModificado = buscarMarcadorArray(idMarca,2);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PRIVADOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PRIVADOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	
	/* MODIFICACIÓN EN LA BASE DE DATOS */
	actualizarInformacionPOI(idMarca,nombreMarca,contenidoMarca);

	/* MODIFICACIÓN EN EL MAPA */
	marcadorModificado.marcador.value = crearHtmlHuellaFija(idMarca,usuarioMarca,caracterMarca,iconoMarca,nombreMarca,contenidoMarca, true);
	marcadorModificado.marcador.bindInfoWindowHtml(marcadorModificado.marcador.value);
	marcadorModificado.marcador.disableDragging();
}

function editarInfoMarcador() {
	var idMarca = document.getElementById("idHuella").title;
	var usuarioMarca = document.getElementById("usuarioHuella").title;
	var iconoMarca = document.getElementById("iconoHuella").title;
	var caracterMarca = document.getElementById("caracterHuella").title;
	var nombreMarca = document.getElementById("nombreHuella").title;
	var contenidoMarca = document.getElementById("contenidoHuella").title;

	//obtiene el marcador sobre el que se quiere modificar la información
	var marcadorModificado = null;	
	if (caracterMarca == "publico") {
		marcadorModificado = buscarMarcadorArray(idMarca,0);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PUBLICOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PUBLICOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	else if (caracterMarca == "protegido") {
		marcadorModificado = buscarMarcadorArray(idMarca,1);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PROTEGIDOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PROTEGIDOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	else if (caracterMarca = "privado") {
		marcadorModificado = buscarMarcadorArray(idMarca,2);		
		/* MODIFICACIÓN EN LOS ARRAYS CORRESPONDIENTES */
		PRIVADOSINF[marcadorModificado.pos].marcador = marcadorModificado.marcador;
		PRIVADOS[marcadorModificado.pos].marcador = marcadorModificado.marcador;
	}
	
	/* MODIFICACIÓN EN EL MAPA */
	marcadorModificado.marcador.value = crearHtmlHuellaEditar(idMarca,usuarioMarca,caracterMarca,iconoMarca,nombreMarca,contenidoMarca);
	marcadorModificado.marcador.bindInfoWindowHtml(marcadorModificado.marcador.value);
	marcadorModificado.marcador.enableDragging();
	marcadorModificado.marcador.openInfoWindowHtml(marcadorModificado.marcador.value);
}

function iconoSeleccionado(miIcono,usuario) {
	if (miIcono.className == "ImgSeleccionada") {
		miIcono.className = "ImgNuevoIcono";
		desactivarMarcar();
	}
	else {	
		//Ponemos todas a la clase estandar por si ya teniamos alguna seleccionadas
		for (var i=1; i<=8; i++)
			document.getElementById("ico"+i).className = "ImgNuevoIcono";
		miIcono.className = "ImgSeleccionada";
		activarMarcar(usuario);	
	}
}

/************************* FIN CREAR, MODIFICAR Y ELIMINAR HUELLAS ***********************************/

/*************************** ACTUALIZACION AUTOMATICA DE CONTENIDO ***********************************/
function refrescar() {
	/*----- PONER AQUÍ LO QUE QUEREMOS QUE SE HAGA AL INICIO Y CADA "tiempoRefresco" SEGUNDOS ----*/
	
	/* --------------------------- HUELLAS -------------------------- */
	// Las públicas se actualizan cada vez que se refresca, las demás sólo la 1ª vez
	if (reinicio == null) {
		cargarPOIprivados(usuarioActual);
		cargarPOIprotegidos(usuarioActual);
	}
	delete PUBLICOS;
	delete PUBLICOSINF;
	controladorMarcadoresPublicos.clearMarkers();
	controladorMarcadoresPublicos = null;
	cargarPOIpublicos();
	/* --------------------------------------------------------------- */
	
	/* --------------------------- AMIGOS ---------------------------- */
	//Eliminamos las marcas que se esten mostrando actualmente
	controladorMarcadoresAmigos.clearMarkers();
	delete AMIGOS;
	delete SOLICITUDES;
	delete MARCADORESAMIGOS;
	delete MARCADORESAMIGOSINF;
	AMIGOS = new Array();
	SOLICITUDES = new Array();
	MARCADORESAMIGOS = new Array();
	MARCADORESAMIGOSINF = new Array();
		
	cargarAmigos();
	
	//Carga todas las huellas de todos los amigos y de los posibles en un futuro
	amigosActivados = new Object();
	for (var i=0; i<AMIGOS.length; i++) {
		cargarPOIAmigo(AMIGOS[i]);
		amigosActivados["'"+AMIGOS[i]+"'"] = false;
	}
	for (var i=0; i<SOLICITUDES.length; i++) {
		cargarPOIAmigo(SOLICITUDES[i]);
		amigosActivados["'"+SOLICITUDES[i]+"'"] = false;
	}
	
	mostrarAmigos();
	/* --------------------------------------------------------------- */
		
	/* -------------------------- EL TIEMPO -------------------------- */	
	//Borramos y volvemos a crear los arrays
	delete TIEMPO[1];
	delete TIEMPO[4];
	delete TIEMPO[5];
	delete TIEMPO[6];
	delete TIEMPO;	
		
	TIEMPO = new Array(10);
	TIEMPO[1] = new Array(); //Resto
	TIEMPO[4] = new Array(); //Capital Pais
	TIEMPO[5] = new Array(); //Capital de autonomia
	TIEMPO[6] = new Array(); //provincias

	cargaTiempoFin = false;		
	cargarTiempo();
		
	//Si las huellas se están mostrando por pantalla actualiza el mapa
	if (tiempoActivado)  {
	//activamos el boton de cargando	
		document.getElementById("generales_tiempo").className = "ImgAdTiempoActivados";
		document.getElementById("generales_tiempo").src="images/cargando.gif"
		controladorTiempo.clearMarkers(); 
		window.setTimeout("ActualizarMapaTiempo();", 4000);
	}
	/* --------------------------------------------------------------- */
		
	/*-------------------------------------------------------------------------------------------*/
	ahora = new Date();
	reinicio = new Date();
	reinicio.setSeconds(ahora.getSeconds()+tiempoRefresco);
	nuevoTiempo = window.setTimeout(cuenta, tiempoFuncionRefrescar*1000);
}

function cuenta() {
	ahora = new Date();
	
	dias = (reinicio - ahora) / 1000 / 60 / 60 / 24;
	contadorDias = Math.floor(dias);
	horas = (reinicio - ahora) / 1000 / 60 / 60 - (24 * contadorDias);
	contadorHoras = Math.floor(horas);
	minutos = (reinicio - ahora) / 1000 /60 - (24 * 60 * contadorDias) - (60 * contadorHoras);
	contadorMinutos = Math.floor(minutos);
	segundos = (reinicio - ahora) / 1000 - (24 * 60 * 60 * contadorDias) - (60 * 60 * contadorHoras) - (60 * contadorMinutos);
	contadorSegundos = Math.floor(segundos);
	segString = (contadorSegundos == 1) ? "segundo" : "segundos";
	minString = (contadorMinutos == 1) ? "minuto," : "minutos,";
	
	if (contadorMinutos==0 && contadorSegundos==0) {
		// Cuenta finalizada
		window.clearTimeout(tiempoReinicio);
		refrescar();
	}
	else {
		htmlRefresco = "La p&aacute;gina se actualizar&aacute; dentro de " + contadorMinutos + " " + minString + " " + contadorSegundos + " " + segString + "<br>";
		htmlRefresco = htmlRefresco + "<a href='javascript: void refrescar()'>Actualizar ahora</a>";
		document.getElementById("refresco").innerHTML = htmlRefresco;
		tiempoReinicio = window.setTimeout(cuenta, tiempoFuncionRefrescar*1000);
	}
}

function ActualizarMapaTiempo()
{
	if (cargaTiempoFin){	  
	  controladorTiempo.addMarkers(TIEMPO[4],4, 14);  //Capital pais
	  controladorTiempo.addMarkers(TIEMPO[5],5, 14);  //Capital autonomia*/
	  controladorTiempo.addMarkers(TIEMPO[6],6, 14);  //provincias*/
	  controladorTiempo.refresh();
	  document.getElementById("generales_tiempo").src="images/generales_plantilla.png"
	}
	else window.setTimeout("ActualizarMapaTiempo();", 1000);
}

/*************************** FIN ACTUALIZACION AUTOMATICA DE CONTENIDO ***********************************/


/*************************** GESTION DE AMIGOS ***********************************/

function calcularNumPaginas(numElementos,elementosPorPagina) {
	var numPaginas = 0;
	if (numElementos % elementosPorPagina == 0)
		numPaginas = parseInt(numElementos / elementosPorPagina);
	else
		numPaginas = parseInt(numElementos / elementosPorPagina) + 1;
	return numPaginas;
}

function mostrarAmigos(){
	//Muestra la página pasada como parámetro de los amigos del usuario actual en el menú de la página principal
	var htmlAmigos = "";
	var amigoActual= "";
	var claseImg = "ImgverHuellasAmigo";
	var numAmigos = AMIGOS.length;
	var primero = (paginaActualAmigos - 1)*AMIGOS_POR_PAGINA;
	
	var htmlPaginas = "";
	var numPaginas = calcularNumPaginas(numAmigos,AMIGOS_POR_PAGINA);

	if (numAmigos == 0 && usuarioActual != "invitado") {
		htmlAmigos = "<p>Aún no tienes ningún amigo</p>";
	}
	else {
		var j=1;
		for (var i=primero; (i < numAmigos) && (j <= AMIGOS_POR_PAGINA); i++) {	 
			j++;
	       	amigoActual = AMIGOS[i];
		   	htmlAmigoActual= "'"+amigoActual+"'";
		   
		   	//Añadimos la clase de la imagen dependiendo de si las huellas del usuario estan activas o no
		   	if ((amigosActivados == null) || (!amigosActivados[amigoActual])) claseImg = "ImgverHuellasAmigo";
		   	else claseImg = "ImgverHuellasAmigoActiva";

		   	htmlAmigos = htmlAmigos + '<div id="eliminarAmigo_'+amigoActual+'"><p>'+amigoActual+'<img class="'+ claseImg +'" src="images/huellasAmigo_plantilla.png" onclick="javascript: void ad_amigos('+ htmlAmigoActual +');"/><img class="ImgEliminarAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void dialogoEliminarAmigo('+ htmlAmigoActual +');"/></p></div>'; 
	    }
		/* PAGINACIÓN EXTENSA */
		if (numPaginas > MAX_PAGINAS_AMIGOS) {
			var paginaMenor = true;
			for (var i=1; i <= numPaginas; i++) {
				/*
				// link página anterior
				if (i == 0) {
					var a = paginaActualAmigos;
					if (paginaActualAmigos != 1) a=a-1;
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaAmigos('+a+')"> < </a>';
				}
				// link página siguiente
				else if (i == numPaginas+1) {
					var s = paginaActualAmigos;
					if (paginaActualAmigos != numPaginas) s=s+1;
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaAmigos('+s+')"> > </a>';
				}
				*/
				// página actual
				if (i == paginaActualAmigos) {
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaAmigos('+i+')" style="color:blue"> '+i+' </a>';
					paginaMenor = false;
				}
				// página 1, última, anterior o siguiente
				else if ((i == 1) || (i == numPaginas) || (i == paginaActualAmigos-1) || (i == paginaActualAmigos+1)) {
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaAmigos('+i+')"> '+i+' </a>';
				}
				// página perdida anterior	
				else if (paginaMenor) {
					htmlPaginas = htmlPaginas + ' ... ';
					i = paginaActualAmigos - 2;
				}
				// página perdida siguiente
				else {
					htmlPaginas = htmlPaginas + ' ... ';
					i = numPaginas - 1;
				}
			}
		}
		/* PAGINACIÓN CORTA */
		else if (numPaginas > 1) {
			for (var i=1; i <= numPaginas; i++) {
				if (i != paginaActualAmigos)
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaAmigos('+i+')"> '+i+' </a>';
				else
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaAmigos('+i+')" style="color:blue"> '+i+' </a>';
			}
		}
	}
	document.getElementById("MisAmigos").innerHTML = htmlAmigos;
	document.getElementById("paginacionAmigos").innerHTML = htmlPaginas;
	
}

function cambiarPaginaAmigos(pagina) {
	paginaActualAmigos = pagina;
	mostrarAmigos();
}

function mostrarSolicitudes(){
	//Muestra los amigos del usuario actual en el menu de la pagina principal
	var htmlSolicitudes = "";
	var amigoActual= "";
	var numSolicitudes = SOLICITUDES.length;
	var primero = (paginaActualSolicitudes - 1)*SOLICITUDES_POR_PAGINA;
	
	var htmlPaginas = "";
	var numPaginas = calcularNumPaginas(numSolicitudes,SOLICITUDES_POR_PAGINA);
	
	if (numSolicitudes > 0) {
		if (numSolicitudes == 1) htmlSolicitudes = "<h4>Tienes 1 solicitud de amistad</h4>";
		else htmlSolicitudes = "<h4>Tienes "+numSolicitudes+" solicitudes de amistad</h4>";
		var j=1;
		for (var i=primero; (i < numSolicitudes) && (j <= SOLICITUDES_POR_PAGINA); i++) {	 
			j++;
	       	amigoActual = SOLICITUDES[i];
		   	htmlAmigoActual= "'"+amigoActual+"'"; 

		   	htmlSolicitudes = htmlSolicitudes + '<div id="solicitudAmigo_'+amigoActual+'"><p>'+amigoActual+'<img class="ImgAceparAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void aceptarAmigo('+ htmlAmigoActual +');"/><img class="ImgRechazarAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void dialogoRechazarSolicitudAmigo('+ htmlAmigoActual +');"/><img class="ImgNoMostarAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void dialogoRechazarSiempreSolicitudAmigo('+ htmlAmigoActual +');"/></p></div>'; 
		}
		
		/* PAGINACIÓN EXTENSA */
		if (numPaginas > MAX_PAGINAS_SOLICITUDES) {
			var paginaMenor = true;
			for (var i=1; i <= numPaginas; i++) {
				// página actual
				if (i == paginaActualSolicitudes) {
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaSolicitudes('+i+')" style="color:blue"> '+i+' </a>';
					paginaMenor = false;
				}
				// página 1, última, anterior o siguiente
				else if ((i == 1) || (i == numPaginas) || (i == paginaActualSolicitudes-1) || (i == paginaActualSolicitudes+1)) {
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaSolicitudes('+i+')"> '+i+' </a>';
				}
				// página perdida anterior	
				else if (paginaMenor) {
					htmlPaginas = htmlPaginas + ' ... ';
					i = paginaActualSolicitudes - 2;
				}
				// página perdida siguiente
				else {
					htmlPaginas = htmlPaginas + ' ... ';
					i = numPaginas - 1;
				}
			}
		}
		/* PAGINACIÓN CORTA */
		else if (numPaginas > 1) {
			for (var i=1; i <= numPaginas; i++) {
				if (i != paginaActualSolicitudes)
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaSolicitudes('+i+')"> '+i+' </a>';
				else
					htmlPaginas = htmlPaginas + '<a href="javascript: void cambiarPaginaSolicitudes('+i+')" style="color:blue"> '+i+' </a>';
			}
		}
	}	
	document.getElementById("solicitudes").innerHTML = htmlSolicitudes;
	document.getElementById("paginacionSolicitudes").innerHTML = htmlPaginas;
}

function cambiarPaginaSolicitudes(pagina) {
	paginaActualSolicitudes = pagina;
	mostrarSolicitudes();
}

function dialogoEliminarAmigo(usuario){
	var aux= 'eliminarAmigo_'+usuario;
	var usuarioHtml = "'"+usuario+"'";
	var claseImg = "ImgverHuellasAmigo";
	
	//Añadimos la clase de la imagen dependiendo de si las huellas del usuario estan activas o no
	if ((amigosActivados == null) || (!amigosActivados[usuario])) claseImg = "ImgverHuellasAmigo";
	else claseImg = "ImgverHuellasAmigoActiva";
	
	var htmlEliminar ='<p>'+ usuario +'<img class="'+ claseImg +'" src="images/huellasAmigo_plantilla.png" onclick="javascript: void ad_amigos('+ usuarioHtml +');"/><img class="ImgSi" src="images/botonSi.png" onclick="javascript: void quitarAmigo('+ usuarioHtml +');"/><img class="ImgNo" src="images/botonNo.png" onclick="javascript: void mostrarAmigos();"/></p></div>'; 
	
	document.getElementById(aux).innerHTML =htmlEliminar;
}

function dialogoRechazarSolicitudAmigo(usuario){
	var aux= 'solicitudAmigo_'+usuario;
	var usuarioHtml = "'"+usuario+"'";
	
	htmlEliminar = '<p>'+ usuario +'<img class="ImgAceparAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void aceptarAmigo('+ usuarioHtml +');"/><img class="ImgSi" src="images/botonSi.png" onclick="javascript: void rechazarAmigo('+ usuarioHtml +');"/><img class="ImgNo" src="images/botonNo.png" onclick="javascript: void mostrarSolicitudes();"/><img class="ImgNoMostarAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void dialogoRechazarSiempreSolicitudAmigo('+ usuarioHtml +');"/></p>'; 
	
	document.getElementById(aux).innerHTML =htmlEliminar;
}

function dialogoRechazarSiempreSolicitudAmigo(usuario){
	var aux= 'solicitudAmigo_'+usuario;
	var usuarioHtml = "'"+usuario+"'";
	
	 htmlEliminar = '<p>'+usuario+'<img class="ImgAceparAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void aceptarAmigo('+ usuarioHtml +');"/><img class="ImgRechazarAmigo" src="images/huellasAmigo_plantilla.png" onclick="javascript: void dialogoRechazarSolicitudAmigo('+ usuarioHtml +');"/><img class="ImgSi" src="images/botonSi.png" onclick="javascript: void rechazarSiempreAmigo('+ usuarioHtml +');"/><img class="ImgNo" src="images/botonNo.png" onclick="javascript: void mostrarSolicitudes();"/></p>'; 
	
	document.getElementById(aux).innerHTML =htmlEliminar;
}


function ad_amigos(usuario){	
	//Conseguimos un array auxiliar que contenga todos los marcadores que tenemos que mostrar u ocultar
	var MARCADORESAUX = new Array();
	for(var i=0;i < MARCADORESAMIGOSINF.length;i++)
	  {
		  if (MARCADORESAMIGOSINF[i].idAmigo == usuario) MARCADORESAUX.push(MARCADORESAMIGOSINF[i].marcador);
	  }

	if (!amigosActivados[usuario])	{	
		amigosActivados[usuario] = true;
		//Añadimos los marcadores de este amigo a los otros de amigos que ya mostrabamos
		mostrarAmigos();
		controladorMarcadoresAmigos.addMarkers(MARCADORESAUX,0);
   		controladorMarcadoresAmigos.refresh();
	}
	else { 
		amigosActivados[usuario] = false;
		//Eliminamos solo los marcadores del amigo actual
		mostrarAmigos();
		for(var i=0;i < MARCADORESAUX.length;i++)
			   controladorMarcadoresAmigos.removeMarker(MARCADORESAUX[i]);
	}
	MARCADORESAUX = null;
	delete MARCADORESAUX;
}

function quitarAmigo(usuario) {
	//Si estamos mostrando pois de ese amigo los borramos del mapa
	if (amigosActivados[usuario]) ad_amigos(usuario);
	
	//Eliminamos los pois del array de MARCADORESAMIGOSINF
	for(var i=0;i < MARCADORESAMIGOSINF.length;i++) {
		  if (MARCADORESAMIGOSINF[i].idAmigo == usuario) {MARCADORESAMIGOSINF.splice(i, 1);i=0;}//vuelvo a empezar por si acaso
	}
	  
	//Eliminamos al amigo del array de amigos
	for(var i=0;i < AMIGOS.length;i++) {
		  if (AMIGOS[i] == usuario) AMIGOS.splice(i, 1);
	}
	
	eliminarAmigo(usuario);
}

function errorAmigo(texto) {
	document.getElementById("textoErrorAmigos").innerHTML =texto;
}
/*************************** FIN GESTION DE AMIGOS ***********************************/

/*************************** MANTENIMIENTO ***********************************/

var peticionAjaxBBDD = null;
function mantenimientoBBDD() {
peticionAjaxBBDD = cargarAjax();
	if (peticionAjaxBBDD) {
    	peticionAjaxBBDD.onreadystatechange = mantenimientoBBDDRespuesta;
		peticionAjaxBBDD.open("POST", "consultasAjax/mantenimiento.php", true);
		peticionAjaxBBDD.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		peticionAjaxBBDD.send("nocache=" + Math.random());
  	}
}

function mantenimientoBBDDRespuesta() {
	if(peticionAjaxCompleta(peticionAjaxBBDD)) {
		documentoXML = peticionAjaxBBDD.responseXML;
		if (documentoXML == null) {
			alert("ERROR AL REALIZAR MANTENIMIENTO");
		}
		else {
			alert("MANTENIMIENTO REALIZADO SATISFACTORIAMENTE");
		}
	}
}

/*************************** FIN MANTENIMIENTO ***********************************/


//]]>

</script>


