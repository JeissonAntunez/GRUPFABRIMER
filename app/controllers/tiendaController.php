<?php

namespace app\controllers;

use app\models\tiendaModel;

class tiendaController extends tiendaModel
{

	/*---------- Controlador obtener siguiente ID ----------*/
	public function obtenerSiguienteIdControlador()
	{
		$nextId = $this->obtenerSiguienteIdModelo();
		$alerta = [
			"status" => "ok",
			"nextId" => $nextId
		];
		return json_encode($alerta);
	}


	/*---------- Controlador registrar tienda ----------*/
	public function registrarTiendaControlador()
	{

		# Almacenando datos#
		$nombre = $this->limpiarCadena($_POST['VCH_TIENDA']);
		$estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

		# Verificando campos obligatorios #
		if ($nombre == "") {
			$alerta = [
				"status" => "error",
				"msg" => "El nombre de la tienda es obligatorio"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando integridad de los datos #
		if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}", $nombre)) {
			$alerta = [
				"status" => "error",
				"msg" => "El NOMBRE no coincide con el formato solicitado"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando nombre de tienda #
		$check_nombre = $this->ejecutarConsulta("SELECT VCH_TIENDA FROM tienda WHERE VCH_TIENDA='$nombre'");
		if ($check_nombre->rowCount() > 0) {
			$alerta = [
				"status" => "error",
				"msg" => "El NOMBRE de la tienda ya está registrado"
			];
			return json_encode($alerta);
			exit();
		}

		# Obtener siguiente ID disponible
		$nextId = $this->obtenerSiguienteIdModelo();

		$tienda_datos_reg = [
			[
				"campo_nombre" => "NUM_ID_TIENDA",
				"campo_marcador" => ":ID",
				"campo_valor" => $nextId
			],
			[
				"campo_nombre" => "VCH_TIENDA",
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

		$registrar_tienda = $this->registrarTiendaModelo($tienda_datos_reg);

		if ($registrar_tienda->rowCount() == 1) {
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
				"msg" => "No se pudo registrar la tienda"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador actualizar tienda ----------*/
	public function actualizarTiendaControlador()
	{

		$id = $this->limpiarCadena($_POST['NUM_ID_TIENDA']);

		# Verificando tienda #
		$datos = $this->ejecutarConsulta("SELECT * FROM tienda WHERE NUM_ID_TIENDA='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"status" => "error",
				"msg" => "No se encontró la tienda en el sistema"
			];
			return json_encode($alerta);
			exit();
		} else {
			$datos = $datos->fetch();
		}

		# Almacenando datos#
		$nombre = $this->limpiarCadena($_POST['VCH_TIENDA']);
		$estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

		# Verificando campos obligatorios #
		if ($nombre == "") {
			$alerta = [
				"status" => "error",
				"msg" => "El nombre de la tienda es obligatorio"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando integridad de los datos #
		if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}", $nombre)) {
			$alerta = [
				"status" => "error",
				"msg" => "El NOMBRE no coincide con el formato solicitado"
			];
			return json_encode($alerta);
			exit();
		}

		# Verificando nombre de tienda #
		if ($datos['VCH_TIENDA'] != $nombre) {
			$check_nombre = $this->ejecutarConsulta("SELECT VCH_TIENDA FROM tienda WHERE VCH_TIENDA='$nombre'");
			if ($check_nombre->rowCount() > 0) {
				$alerta = [
					"status" => "error",
					"msg" => "El NOMBRE de la tienda ya está registrado"
				];
				return json_encode($alerta);
				exit();
			}
		}

		$tienda_datos_up = [
			[
				"campo_nombre" => "VCH_TIENDA",
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
			"condicion_campo" => "NUM_ID_TIENDA",
			"condicion_marcador" => ":ID",
			"condicion_valor" => $id
		];

		if ($this->actualizarTiendaModelo($tienda_datos_up, $condicion)) {
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
				"msg" => "No se pudo actualizar la tienda"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador actualizar solo estado ----------*/
	public function actualizarEstadoControlador()
	{
		$id = $this->limpiarCadena($_POST['id']);
		$estado = $this->limpiarCadena($_POST['estado']);

		# Verificando tienda #
		$datos = $this->ejecutarConsulta("SELECT * FROM tienda WHERE NUM_ID_TIENDA='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"status" => "error",
				"msg" => "No se encontró la tienda"
			];
			return json_encode($alerta);
			exit();
		}

		$actualizar = $this->actualizarEstadoTiendaModelo($id, $estado, $_SESSION['usuario']);

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


	/*---------- Controlador eliminar tienda ----------*/
	public function eliminarTiendaControlador()
	{

		$id = $this->limpiarCadena($_POST['id']);

		# Verificando tienda #
		$datos = $this->ejecutarConsulta("SELECT * FROM tienda WHERE NUM_ID_TIENDA='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = [
				"status" => "error",
				"msg" => "No se encontró la tienda en el sistema"
			];
			return json_encode($alerta);
			exit();
		}

		$eliminarTienda = $this->eliminarTiendaModelo($id);

		if ($eliminarTienda->rowCount() == 1) {
			$alerta = [
				"status" => "ok"
			];
		} else {
			$alerta = [
				"status" => "error",
				"msg" => "No se pudo eliminar la tienda"
			];
		}

		return json_encode($alerta);
	}


	/*---------- Controlador listar tiendas ----------*/
	public function listarTiendasControlador()
	{
		return $this->listarTodasTiendasModelo();
	}
}
