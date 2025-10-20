<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\claseController;

	if(isset($_POST['modulo_clase'])){

		$insClase = new claseController();

		if($_POST['modulo_clase']=="get-next-id"){
			echo $insClase->obtenerSiguienteIdControlador();
		}

		if($_POST['modulo_clase']=="registrar"){
			echo $insClase->registrarClaseControlador();
		}

		if($_POST['modulo_clase']=="actualizar"){
			echo $insClase->actualizarClaseControlador();
		}

		if($_POST['modulo_clase']=="actualizar_estado"){
			echo $insClase->actualizarEstadoControlador();
		}

		if($_POST['modulo_clase']=="eliminar"){
			echo $insClase->eliminarClaseControlador();
		}

	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}