<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\tiendaController;

	if(isset($_POST['modulo_tienda'])){

		$insTienda = new tiendaController();

		if($_POST['modulo_tienda']=="get-next-id"){
			echo $insTienda->obtenerSiguienteIdControlador();
		}

		if($_POST['modulo_tienda']=="registrar"){
			echo $insTienda->registrarTiendaControlador();
		}

		if($_POST['modulo_tienda']=="actualizar"){
			echo $insTienda->actualizarTiendaControlador();
		}

		if($_POST['modulo_tienda']=="actualizar_estado"){
			echo $insTienda->actualizarEstadoControlador();
		}

		if($_POST['modulo_tienda']=="eliminar"){
			echo $insTienda->eliminarTiendaControlador();
		}

	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}