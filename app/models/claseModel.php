<?php

namespace app\models;

class claseModel extends mainModel
{

	/*---------- Obtener siguiente ID ----------*/
	public function obtenerSiguienteIdModelo()
	{
		$sql = $this->conectar()->prepare("SELECT IFNULL(MAX(NUM_ID_CLASE), 0) + 1 as next_id FROM clase");
		$sql->execute();
		$resultado = $sql->fetch();
		return $resultado['next_id'];
	}

	/*---------- Buscar clase por ID ----------*/
	public function buscarClasePorId($id)
	{
		$sql = $this->conectar()->prepare("SELECT * FROM clase WHERE NUM_ID_CLASE = :ID");
		$sql->bindParam(":ID", $id);
		$sql->execute();
		return $sql;
	}

	/*---------- ⭐ NUEVO: Buscar clase por ID (nomenclatura modelo) ----------*/
	public function buscarClasePorIdModelo($id)
	{
		$sql = $this->conectar()->prepare("SELECT * FROM clase WHERE NUM_ID_CLASE = :ID LIMIT 1");
		$sql->bindParam(":ID", $id, \PDO::PARAM_INT);
		$sql->execute();
		return $sql;
	}

	/*---------- Verificar si nombre existe ----------*/
	public function verificarNombreExiste($nombre, $excluirId = null)
	{
		if ($excluirId) {
			$sql = $this->conectar()->prepare("SELECT VCH_NOMBRE FROM clase WHERE VCH_NOMBRE = :Nombre AND NUM_ID_CLASE != :ID");
			$sql->bindParam(":Nombre", $nombre);
			$sql->bindParam(":ID", $excluirId);
		} else {
			$sql = $this->conectar()->prepare("SELECT VCH_NOMBRE FROM clase WHERE VCH_NOMBRE = :Nombre");
			$sql->bindParam(":Nombre", $nombre);
		}
		$sql->execute();
		return $sql;
	}

	/*---------- Registrar clase ----------*/
	public function registrarClaseModelo($datos)
	{
		return $this->guardarDatos("clase", $datos);
	}

	/*---------- Actualizar clase ----------*/
	public function actualizarClaseModelo($datos, $condicion)
	{
		return $this->actualizarDatos("clase", $datos, $condicion);
	}

	/*---------- Actualizar estado ----------*/
	public function actualizarEstadoClaseModelo($id, $estado, $usuario)
	{
		$sql = $this->conectar()->prepare("
            UPDATE clase 
            SET VCH_ESTADO = :Estado,
                FEC_FECHA_MODIFICACION = :Fecha,
                VCH_USER_MODIFICACION = :Usuario
            WHERE NUM_ID_CLASE = :ID
        ");
		$fecha = date("Y-m-d H:i:s");
		$sql->bindParam(":Estado", $estado);
		$sql->bindParam(":Fecha", $fecha);
		$sql->bindParam(":Usuario", $usuario);
		$sql->bindParam(":ID", $id);
		$sql->execute();
		return $sql;
	}

	/*---------- Eliminar clase ----------*/
	public function eliminarClaseModelo($id)
	{
		return $this->eliminarRegistro("clase", "NUM_ID_CLASE", $id);
	}

	/*---------- Listar todas las clases ----------*/
	public function listarTodasClasesModelo()
	{
		$sql = $this->conectar()->prepare("SELECT * FROM clase ORDER BY NUM_ID_CLASE DESC");
		$sql->execute();
		return $sql;
	}

	/*---------- ⭐ NUEVO: Listar clases activas para controladores ----------*/
	public function listarClasesControlador()
	{
		$sql = $this->conectar()->prepare("SELECT * FROM clase WHERE VCH_ESTADO = 1 ORDER BY VCH_NOMBRE ASC");
		$sql->execute();
		return $sql;
	}
}
