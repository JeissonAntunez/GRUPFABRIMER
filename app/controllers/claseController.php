<?php

namespace app\controllers;

use app\models\claseModel;

class claseController extends mainController
{

	private $claseModel;

	public function __construct()
	{
		$this->claseModel = new claseModel();
	}

	/*---------- Controlador obtener siguiente ID ----------*/
	public function obtenerSiguienteIdControlador()
	{
		$nextId = $this->claseModel->obtenerSiguienteIdModelo();
		$alerta = [
			"status" => "ok",
			"nextId" => $nextId
		];
		return json_encode($alerta);
	}


	/*---------- Controlador registrar clase ----------*/
	public function registrarClaseControlador()
	{

		# Almacenando datos
		$nombre = $this->limpiarCadena($_POST['VCH_NOMBRE']);
		$estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

		# Verificando campos obligatorios
		if ($nombre == "") {
			$alerta = [
				"status" => "error",
				"msg" => "El nombre de la clase es obligatorio"
			];
			return json_encode($alerta);
		}

		# Verificando integridad de los datos
		if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}", $nombre)) {
			$alerta = [
				"status" => "error",
				"msg" => "El NOMBRE no coincide con el formato solicitado"
			];
			return json_encode($alerta);
		}

		# Verificando nombre de clase
		$check_nombre = $this->claseModel->verificarNombreExiste($nombre);
		if ($check_nombre->rowCount() > 0) {
			$alerta = [
				"status" => "error",
				"msg" => "El NOMBRE de la clase ya está registrado"
			];
			return json_encode($alerta);
		}

		# Obtener siguiente ID disponible
		$nextId = $this->claseModel->obtenerSiguienteIdModelo();

		$clase_datos_reg = [
			[
				"campo_nombre" => "NUM_ID_CLASE",
				"campo_marcador" => ":ID",
				"campo_valor" => $nextId
			],
			[
				"campo_nombre" => "VCH_NOMBRE",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "VCH_ESTADO",
				"campo_marcador" => ":Estado",
				"campo_valor" => $estado
			],
			[
				"campo_nombre" => "FEC_FECHA_CREACION",
				"campo_marcador" => ":FechaCreacion",
				"campo_valor" => date("Y-m-d H:i:s")
			],
			[
				"campo_nombre" => "VCH_USER_CREACION",
				"campo_marcador" => ":UserCreacion",
				"campo_valor" => $_SESSION['usuario']
			]
		];

		$registrar_clase = $this->claseModel->registrarClaseModelo($clase_datos_reg);

		if ($registrar_clase->rowCount() == 1) {
			$alerta = [
				"status" => "ok",
				"id" => $nextId,
				"nombre" => $nombre,
				"estado" => $estado,
				"usuario" => $_SESSION['usuario'],
				"fecha" => date("Y-m-d H:i:s")
			];
		} else {
			$alerta = [
				"status" => "error",
				"msg" => "No se pudo registrar la clase"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador actualizar clase ----------*/
	public function actualizarClaseControlador()
	{

		$id = $this->limpiarCadena($_POST['NUM_ID_CLASE']);

		# Verificando clase
		$datos = $this->claseModel->buscarClasePorId($id);
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"status" => "error",
				"msg" => "No se encontró la clase en el sistema"
			];
			return json_encode($alerta);
		} else {
			$datos = $datos->fetch();
		}

		# Almacenando datos
		$nombre = $this->limpiarCadena($_POST['VCH_NOMBRE']);
		$estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

		# Verificando campos obligatorios
		if ($nombre == "") {
			$alerta = [
				"status" => "error",
				"msg" => "El nombre de la clase es obligatorio"
			];
			return json_encode($alerta);
		}

		# Verificando integridad de los datos
		if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}", $nombre)) {
			$alerta = [
				"status" => "error",
				"msg" => "El NOMBRE no coincide con el formato solicitado"
			];
			return json_encode($alerta);
		}

		# Verificando nombre de clase
		if ($datos['VCH_NOMBRE'] != $nombre) {
			$check_nombre = $this->claseModel->verificarNombreExiste($nombre, $id);
			if ($check_nombre->rowCount() > 0) {
				$alerta = [
					"status" => "error",
					"msg" => "El NOMBRE de la clase ya está registrado"
				];
				return json_encode($alerta);
			}
		}

		$clase_datos_up = [
			[
				"campo_nombre" => "VCH_NOMBRE",
				"campo_marcador" => ":Nombre",
				"campo_valor" => $nombre
			],
			[
				"campo_nombre" => "VCH_ESTADO",
				"campo_marcador" => ":Estado",
				"campo_valor" => $estado
			],
			[
				"campo_nombre" => "FEC_FECHA_MODIFICACION",
				"campo_marcador" => ":FechaModificacion",
				"campo_valor" => date("Y-m-d H:i:s")
			],
			[
				"campo_nombre" => "VCH_USER_MODIFICACION",
				"campo_marcador" => ":UserModificacion",
				"campo_valor" => $_SESSION['usuario']
			]
		];

		$condicion = [
			"condicion_campo" => "NUM_ID_CLASE",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->claseModel->actualizarClaseModelo($clase_datos_up, $condicion)) {
			$alerta = [
				"status" => "ok",
				"id" => $id,
				"nombre" => $nombre,
				"estado" => $estado,
				"userM" => $_SESSION['usuario'],
				"fecha" => date("Y-m-d H:i:s")
			];
		} else {
			$alerta = [
				"status" => "error",
				"msg" => "No se pudo actualizar la clase"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador actualizar solo estado ----------*/
	public function actualizarEstadoControlador()
	{
		$id = $this->limpiarCadena($_POST['id']);
		$estado = $this->limpiarCadena($_POST['estado']);

		# Verificando clase
		$datos = $this->claseModel->buscarClasePorId($id);
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"status" => "error",
				"msg" => "No se encontró la clase"
			];
			return json_encode($alerta);
		}

		$actualizar = $this->claseModel->actualizarEstadoClaseModelo($id, $estado, $_SESSION['usuario']);

		if ($actualizar->rowCount() >= 0) {
			$alerta = [
				"status" => "ok",
				"estado" => $estado,
				"fecha" => date("Y-m-d H:i:s")
			];
		} else {
			$alerta = [
				"status" => "error",
				"msg" => "No se pudo actualizar el estado"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador eliminar clase ----------*/
	public function eliminarClaseControlador()
	{

		$id = $this->limpiarCadena($_POST['id']);

		# Verificando clase
		$datos = $this->claseModel->buscarClasePorId($id);
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"status" => "error",
				"msg" => "No se encontró la clase en el sistema"
			];
			return json_encode($alerta);
		}

		$eliminarClase = $this->claseModel->eliminarClaseModelo($id);

		if ($eliminarClase->rowCount() == 1) {
			$alerta = [
				"status" => "ok"
			];
		} else {
			$alerta = [
				"status" => "error",
				"msg" => "No se pudo eliminar la clase"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador listar clases ----------*/
	public function listarClasesControlador()
	{
		return $this->claseModel->listarTodasClasesModelo();
	}
}
