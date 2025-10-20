<?php

namespace app\models;

class listaModel extends mainModel
{

    /*---------- Obtener siguiente ID ----------*/
    public function obtenerSiguienteIdLista()
    {
        try {
            $sql = $this->conectar()->prepare("SELECT IFNULL(MAX(NUM_ID_LISTA), 0) + 1 as next_id FROM listas");
            $sql->execute();
            $resultado = $sql->fetch();
            return $resultado['next_id'];
        } catch (\PDOException $e) {
            error_log("Error en obtenerSiguienteIdLista: " . $e->getMessage());
            return 1;
        }
    }

    /*---------- Buscar lista por ID ----------*/
    public function buscarListaPorId($id)
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT 
                    l.*,
                    t.VCH_TIENDA as NOMBRE_TIENDA
                FROM listas l
                LEFT JOIN tienda t ON l.NUM_ID_TIENDA = t.NUM_ID_TIENDA
                WHERE l.NUM_ID_LISTA = :ID
            ");
            $sql->bindParam(":ID", $id);
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en buscarListaPorId: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Verificar si código existe ----------*/
    public function verificarCodigoExiste($codigo, $excluirId = null)
    {
        try {
            if ($excluirId) {
                $sql = $this->conectar()->prepare("SELECT VCH_CODIGO FROM listas WHERE VCH_CODIGO = :Codigo AND NUM_ID_LISTA != :ID");
                $sql->bindParam(":Codigo", $codigo);
                $sql->bindParam(":ID", $excluirId);
            } else {
                $sql = $this->conectar()->prepare("SELECT VCH_CODIGO FROM listas WHERE VCH_CODIGO = :Codigo");
                $sql->bindParam(":Codigo", $codigo);
            }
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en verificarCodigoExiste: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Registrar lista ----------*/
    public function registrarListaModelo($datos)
    {
        try {
            return $this->guardarDatos("listas", $datos);
        } catch (\Exception $e) {
            error_log("Error en registrarListaModelo: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Actualizar lista ----------*/
    public function actualizarListaModelo($datos, $condicion)
    {
        try {
            return $this->actualizarDatos("listas", $datos, $condicion);
        } catch (\Exception $e) {
            error_log("Error en actualizarListaModelo: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Actualizar estado ----------*/
    public function actualizarEstadoListaModelo($id, $estado, $usuario)
    {
        try {
            $sql = $this->conectar()->prepare("
                UPDATE listas
                SET VCH_ESTADO = :Estado,
                    FEC_FECHA_MODIFICACION = :Fecha,
                    VCH_USER_MODIFICACION = :Usuario
                WHERE NUM_ID_LISTA = :ID
            ");
            $fecha = date("Y-m-d H:i:s");
            $sql->bindParam(":Estado", $estado);
            $sql->bindParam(":Fecha", $fecha);
            $sql->bindParam(":Usuario", $usuario);
            $sql->bindParam(":ID", $id);
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en actualizarEstadoListaModelo: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Eliminar lista ----------*/
    public function eliminarListaModelo($id)
    {
        try {
            return $this->eliminarRegistro("listas", "NUM_ID_LISTA", $id);
        } catch (\Exception $e) {
            error_log("Error en eliminarListaModelo: " . $e->getMessage());
            return false;
        }
    }

    /*---------- Listar todas las listas con nombre de tienda (JOIN) ----------*/
    public function listarTodasListasModelo()
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT 
                    l.NUM_ID_LISTA,
                    l.NUM_ID_TIENDA,
                    t.VCH_TIENDA as NOMBRE_TIENDA,
                    l.VCH_JUEGO,
                    l.VCH_CODIGO,
                    l.VCH_DESCRIPCION,
                    l.VCH_ESTADO,
                    l.FEC_FECHA_CREACION,
                    l.FEC_FECHA_MODIFICACION,
                    l.VCH_USER_CREACION,
                    l.VCH_USER_MODIFICACION
                FROM listas l
                INNER JOIN tienda t ON l.NUM_ID_TIENDA = t.NUM_ID_TIENDA
                ORDER BY l.NUM_ID_LISTA ASC
            ");
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en listarTodasListasModelo: " . $e->getMessage());
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                error_log("VERIFICAR: La tabla 'listas' no existe en la base de datos");
            }
            return false;
        }
    }

    /*---------- Listar listas por tienda ----------*/
    public function listarListasPorTienda($tienda_id)
    {
        try {
            $sql = $this->conectar()->prepare("
                SELECT 
                    l.*,
                    t.VCH_TIENDA as NOMBRE_TIENDA
                FROM listas l
                INNER JOIN tienda t ON l.NUM_ID_TIENDA = t.NUM_ID_TIENDA
                WHERE l.NUM_ID_TIENDA = :TiendaId
                ORDER BY l.NUM_ID_LISTA ASC
            ");
            $sql->bindParam(":TiendaId", $tienda_id);
            $sql->execute();
            return $sql;
        } catch (\PDOException $e) {
            error_log("Error en listarListasPorTienda: " . $e->getMessage());
            return false;
        }
    }

    /*---------- MÉTODO DE DEPURACIÓN - Verificar tabla ----------*/
    public function verificarTablaExiste()
    {
        try {
            $sql = $this->conectar()->query("SHOW TABLES LIKE 'listas'");
            $existe = $sql->rowCount() > 0;
            error_log("Tabla 'listas' existe: " . ($existe ? "SÍ" : "NO"));
            return $existe;
        } catch (\PDOException $e) {
            error_log("Error verificando tabla: " . $e->getMessage());
            return false;
        }
    }
}