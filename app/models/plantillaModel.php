<?php

namespace app\models;

class plantillaModel extends mainModel
{

    /*---------- Registrar Plantilla ----------*/
    public function registrarPlantillaModelo($datos)
    {
        $sql = $this->guardarDatos("plantilla", $datos);
        return $sql;
    }


    /*---------- Registrar Detalle ----------*/
    public function registrarDetalleModelo($datos)
    {
        $sql = $this->guardarDatos("plant_detalle", $datos);
        return $sql;
    }


    /*---------- Actualizar Plantilla ----------*/
    public function actualizarPlantillaModelo($datos, $condicion)
    {
        $sql = $this->actualizarDatos("plantilla", $datos, $condicion);
        return $sql;
    }


    /*---------- Actualizar Detalle ----------*/
    public function actualizarDetalleModelo($datos, $condicion)
    {
        $sql = $this->actualizarDatos("plant_detalle", $datos, $condicion);
        return $sql;
    }


    /*---------- Actualizar solo estado de Plantilla ----------*/
    public function actualizarEstadoPlantillaModelo($id, $estado, $usuario)
    {
        $fecha = date("Y-m-d H:i:s");
        $sql = $this->conectar()->prepare("UPDATE plantilla 
            SET VCH_ESTADO = :Estado,
                FEC_FECHA_MODIFICACION = :Fecha,
                VCH_USER_MODIFICACION = :Usuario
            WHERE NUM_ID_PLANTILLA = :ID");

        $sql->bindParam(":Estado", $estado);
        $sql->bindParam(":Fecha", $fecha);
        $sql->bindParam(":Usuario", $usuario);
        $sql->bindParam(":ID", $id);
        $sql->execute();

        return $sql;
    }


    /*---------- Actualizar solo estado de Detalle ----------*/
    public function actualizarEstadoDetalleModelo($id, $estado, $usuario)
    {
        $fecha = date("Y-m-d H:i:s");
        $sql = $this->conectar()->prepare("UPDATE plant_detalle 
            SET VCH_ESTADO = :Estado,
                FEC_FECHA_MODIFICACION = :Fecha,
                VCH_USER_MODIFICACION = :Usuario
            WHERE NUM_ID_DET_PLANTILLA = :ID");

        $sql->bindParam(":Estado", $estado);
        $sql->bindParam(":Fecha", $fecha);
        $sql->bindParam(":Usuario", $usuario);
        $sql->bindParam(":ID", $id);
        $sql->execute();

        return $sql;
    }


    /*---------- Eliminar Plantilla ----------*/
    public function eliminarPlantillaModelo($id)
    {
        $sql = $this->eliminarRegistro("plantilla", "NUM_ID_PLANTILLA", $id);
        return $sql;
    }


    /*---------- Eliminar Detalle ----------*/
    public function eliminarDetalleModelo($id)
    {
        $sql = $this->eliminarRegistro("plant_detalle", "NUM_ID_DET_PLANTILLA", $id);
        return $sql;
    }


    /*---------- Eliminar Detalles por ID Plantilla ----------*/
    public function eliminarDetallesPorPlantillaModelo($idPlantilla)
    {
        $sql = $this->conectar()->prepare("DELETE FROM plant_detalle WHERE NUM_ID_PLANTILLA = :ID");
        $sql->bindParam(":ID", $idPlantilla);
        $sql->execute();
        return $sql;
    }


    /*---------- Buscar Plantilla por ID ----------*/
    public function buscarPlantillaPorIdModelo($id)
    {
        $sql = $this->seleccionarDatos("Unico", "plantilla", "NUM_ID_PLANTILLA", $id);
        return $sql;
    }


    /*---------- Buscar Detalle por ID ----------*/
    public function buscarDetallePorIdModelo($id)
    {
        $sql = $this->seleccionarDatos("Unico", "plant_detalle", "NUM_ID_DET_PLANTILLA", $id);
        return $sql;
    }


    /*---------- Listar todas las Plantillas ----------*/
    public function listarTodasPlantillasModelo()
    {
        $sql = $this->ejecutarConsulta("SELECT p.*, 
            t.VCH_TIENDA AS NOMBRE_TIENDA,
            c.VCH_NOMBRE AS NOMBRE_CLASE
            FROM plantilla p
            LEFT JOIN tienda t ON p.NUM_ID_TIENDA = t.NUM_ID_TIENDA
            LEFT JOIN clase c ON p.NUM_ID_CLASE = c.NUM_ID_CLASE
            ORDER BY p.NUM_ID_PLANTILLA DESC");
        return $sql;
    }


    /*---------- Listar Plantillas con Filtros ----------*/
    public function listarPlantillasFiltrosModelo($idClase = 0, $idTienda = 0)
    {
        $sql = "SELECT p.*, 
            t.VCH_TIENDA AS NOMBRE_TIENDA,
            c.VCH_NOMBRE AS NOMBRE_CLASE
            FROM plantilla p
            LEFT JOIN tienda t ON p.NUM_ID_TIENDA = t.NUM_ID_TIENDA
            LEFT JOIN clase c ON p.NUM_ID_CLASE = c.NUM_ID_CLASE
            WHERE 1=1";

        if ($idClase > 0) {
            $sql .= " AND p.NUM_ID_CLASE = $idClase";
        }
        if ($idTienda > 0) {
            $sql .= " AND p.NUM_ID_TIENDA = $idTienda";
        }

        $sql .= " ORDER BY p.NUM_ID_PLANTILLA DESC";

        return $this->ejecutarConsulta($sql);
    }


    /*---------- Listar todos los Detalles ----------*/
    public function listarTodosDetallesModelo()
    {
        $sql = $this->ejecutarConsulta("SELECT d.*, 
            p.NUM_ID_TIENDA,
            p.NUM_ID_CLASE,
            t.VCH_TIENDA AS NOMBRE_TIENDA,
            c.VCH_NOMBRE AS NOMBRE_CLASE
            FROM plant_detalle d
            INNER JOIN plantilla p ON d.NUM_ID_PLANTILLA = p.NUM_ID_PLANTILLA
            LEFT JOIN tienda t ON p.NUM_ID_TIENDA = t.NUM_ID_TIENDA
            LEFT JOIN clase c ON p.NUM_ID_CLASE = c.NUM_ID_CLASE
            ORDER BY d.NUM_ORDEN ASC, d.NUM_ID_DET_PLANTILLA DESC");
        return $sql;
    }


    /*---------- Listar Detalles con Filtros ----------*/
    public function listarDetallesFiltrosModelo($idPlantilla = 0, $idClase = 0, $idTienda = 0)
    {
        $sql = "SELECT d.*, 
            p.NUM_ID_TIENDA,
            p.NUM_ID_CLASE,
            t.VCH_TIENDA AS NOMBRE_TIENDA,
            c.VCH_NOMBRE AS NOMBRE_CLASE
            FROM plant_detalle d
            INNER JOIN plantilla p ON d.NUM_ID_PLANTILLA = p.NUM_ID_PLANTILLA
            LEFT JOIN tienda t ON p.NUM_ID_TIENDA = t.NUM_ID_TIENDA
            LEFT JOIN clase c ON p.NUM_ID_CLASE = c.NUM_ID_CLASE
            WHERE 1=1";

        if ($idPlantilla > 0) {
            $sql .= " AND d.NUM_ID_PLANTILLA = $idPlantilla";
        }
        if ($idClase > 0) {
            $sql .= " AND p.NUM_ID_CLASE = $idClase";
        }
        if ($idTienda > 0) {
            $sql .= " AND p.NUM_ID_TIENDA = $idTienda";
        }

        $sql .= " ORDER BY d.NUM_ORDEN ASC, d.NUM_ID_DET_PLANTILLA DESC";

        return $this->ejecutarConsulta($sql);
    }


    /*---------- Verificar si ID Plantilla existe ----------*/
    public function verificarIdPlantillaExiste($id)
    {
        $sql = $this->conectar()->prepare("SELECT NUM_ID_PLANTILLA FROM plantilla WHERE NUM_ID_PLANTILLA = :ID");
        $sql->bindParam(":ID", $id);
        $sql->execute();
        return $sql;
    }


    /*---------- Verificar si ID Detalle existe ----------*/
    public function verificarIdDetalleExiste($id)
    {
        $sql = $this->conectar()->prepare("SELECT NUM_ID_DET_PLANTILLA FROM plant_detalle WHERE NUM_ID_DET_PLANTILLA = :ID");
        $sql->bindParam(":ID", $id);
        $sql->execute();
        return $sql;
    }


    /*---------- Obtener siguiente ID disponible Plantilla ----------*/
    public function obtenerSiguienteIdPlantilla()
    {
        $sql = $this->ejecutarConsulta("SELECT MAX(NUM_ID_PLANTILLA) as max_id FROM plantilla");
        $result = $sql->fetch();
        return ($result['max_id'] ?? 0) + 1;
    }


    /*---------- Obtener siguiente ID disponible Detalle ----------*/
    public function obtenerSiguienteIdDetalle()
    {
        $sql = $this->ejecutarConsulta("SELECT MAX(NUM_ID_DET_PLANTILLA) as max_id FROM plant_detalle");
        $result = $sql->fetch();
        return ($result['max_id'] ?? 0) + 1;
    }
}
