<?php

namespace app\models;

class tiendaModel extends mainModel
{

	/*---------- Modelo obtener siguiente ID disponible ----------*/
	protected function obtenerSiguienteIdModelo()
	{
		$sql = "SELECT NUM_ID_TIENDA FROM tienda ORDER BY NUM_ID_TIENDA ASC";
		$result = $this->ejecutarConsulta($sql);

		$ids = [];
		while ($row = $result->fetch()) {
			$ids[] = (int)$row['NUM_ID_TIENDA'];
		}

		// Si no hay registros, empezar desde 1
		if (empty($ids)) {
			return 1;
		}

		// Buscar el primer hueco en la secuencia
		$expectedId = 1;
		foreach ($ids as $existingId) {
			if ($expectedId < $existingId) {
				return $expectedId;
			}
			$expectedId = $existingId + 1;
		}

		return $expectedId;
	}


	/*---------- Modelo registrar tienda ----------*/
	protected function registrarTiendaModelo($datos)
	{
		$sql = $this->guardarDatos("tienda", $datos);
		return $sql;
	}


	/*---------- Modelo actualizar tienda ----------*/
	protected function actualizarTiendaModelo($datos, $condicion)
	{
		$sql = $this->actualizarDatos("tienda", $datos, $condicion);
		return $sql;
	}


	/*---------- Modelo actualizar solo estado ----------*/
	protected function actualizarEstadoTiendaModelo($id, $estado, $usuario)
	{
		$fecha = date("Y-m-d H:i:s");
		$sql = $this->conectar()->prepare("UPDATE tienda 
				SET VCH_ESTADO=:Estado, 
					VCH_USER_MODIFICACION=:Usuario, 
					FEC_FECHA_MODIFICACION=:Fecha 
				WHERE NUM_ID_TIENDA=:ID");

		$sql->bindParam(":Estado", $estado);
		$sql->bindParam(":Usuario", $usuario);
		$sql->bindParam(":Fecha", $fecha);
		$sql->bindParam(":ID", $id);
		$sql->execute();

		return $sql;
	}


	/*---------- Modelo eliminar tienda ----------*/
	protected function eliminarTiendaModelo($id)
	{
		$sql = $this->eliminarRegistro("tienda", "NUM_ID_TIENDA", $id);
		return $sql;
	}


	/*---------- Modelo listar todas las tiendas ----------*/
	protected function listarTodasTiendasModelo()
	{
		$sql = "SELECT * FROM tienda ORDER BY NUM_ID_TIENDA ASC";
		$datos = $this->ejecutarConsulta($sql);
		return $datos;
	}

	/*---------- Buscar tienda por ID ----------*/
	public function buscarTiendaPorId($id)
	{
		$sql = $this->conectar()->prepare("SELECT * FROM tienda WHERE NUM_ID_TIENDA = :ID");
		$sql->bindParam(":ID", $id);
		$sql->execute();
		return $sql;
	}
}
