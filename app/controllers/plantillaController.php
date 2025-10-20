<?php

namespace app\controllers;

use app\models\plantillaModel;
use app\models\tiendaModel;
use app\models\claseModel;

class plantillaController extends mainController
{
    private $plantillaModel;
    private $tiendaModel;
    private $claseModel;

    public function __construct()
    {
        $this->plantillaModel = new plantillaModel();
        $this->tiendaModel = new tiendaModel();
        $this->claseModel = new claseModel();
    }

    /*---------- Registrar Plantilla ----------*/
    public function registrarPlantillaControlador()
    {
        try {
            $tienda_id = $this->limpiarCadena($_POST['NUM_ID_TIENDA'] ?? '');
            $clase_id = $this->limpiarCadena($_POST['NUM_ID_CLASE'] ?? '');
            $cat1 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N1'] ?? '');
            $cat2 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N2'] ?? '');
            $cat3 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N3'] ?? '');
            $cat4 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N4'] ?? '');
            $cat5 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N5'] ?? '');
            $estado = $this->limpiarCadena($_POST['VCH_ESTADO'] ?? '1');

            // Log para depuración
            error_log("Registrando plantilla - Tienda: $tienda_id, Clase: $clase_id");

            // Validaciones
            if (empty($tienda_id) || empty($clase_id)) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'Los campos Tienda y Clase son obligatorios'
                ], JSON_UNESCAPED_UNICODE);
            }

            // Convertir a enteros
            $tienda_id = (int)$tienda_id;
            $clase_id = (int)$clase_id;

            if ($tienda_id <= 0 || $clase_id <= 0) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'Los valores de Tienda y Clase deben ser válidos'
                ], JSON_UNESCAPED_UNICODE);
            }

            // Verificar que la tienda existe
            $check_tienda = $this->tiendaModel->buscarTiendaPorId($tienda_id);
            if ($check_tienda->rowCount() == 0) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'La tienda seleccionada no existe'
                ], JSON_UNESCAPED_UNICODE);
            }

            // Verificar que la clase existe
            $check_clase = $this->claseModel->buscarClasePorId($clase_id);
            if ($check_clase->rowCount() == 0) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'La clase seleccionada no existe'
                ], JSON_UNESCAPED_UNICODE);
            }

            // Obtener siguiente ID
            $id = $this->plantillaModel->obtenerSiguienteIdPlantilla();

            $plantilla_datos_reg = [
                [
                    "campo_nombre" => "NUM_ID_PLANTILLA",
                    "campo_marcador" => ":ID",
                    "campo_valor" => $id
                ],
                [
                    "campo_nombre" => "NUM_ID_TIENDA",
                    "campo_marcador" => ":Tienda",
                    "campo_valor" => $tienda_id
                ],
                [
                    "campo_nombre" => "NUM_ID_CLASE",
                    "campo_marcador" => ":Clase",
                    "campo_valor" => $clase_id
                ],
                [
                    "campo_nombre" => "VCH_CATEGORIA_N1",
                    "campo_marcador" => ":Cat1",
                    "campo_valor" => $cat1
                ],
                [
                    "campo_nombre" => "VCH_CATEGORIA_N2",
                    "campo_marcador" => ":Cat2",
                    "campo_valor" => $cat2
                ],
                [
                    "campo_nombre" => "VCH_CATEGORIA_N3",
                    "campo_marcador" => ":Cat3",
                    "campo_valor" => $cat3
                ],
                [
                    "campo_nombre" => "VCH_CATEGORIA_N4",
                    "campo_marcador" => ":Cat4",
                    "campo_valor" => $cat4
                ],
                [
                    "campo_nombre" => "VCH_CATEGORIA_N5",
                    "campo_marcador" => ":Cat5",
                    "campo_valor" => $cat5
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
                    "campo_valor" => $_SESSION['usuario'] ?? 'sistema'
                ]
            ];

            $registrar = $this->plantillaModel->registrarPlantillaModelo($plantilla_datos_reg);

            if ($registrar->rowCount() == 1) {
                return json_encode([
                    'status' => 'ok',
                    'msg' => 'Plantilla registrada correctamente',
                    'id' => $id
                ], JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'No se pudo registrar la plantilla en la base de datos'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            error_log("Error en registrarPlantillaControlador: " . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'msg' => 'Error al registrar: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /*---------- Registrar Detalle ----------*/
    public function registrarDetalleControlador()
    {
        $id_plantilla = $this->limpiarCadena($_POST['NUM_ID_PLANTILLA']);
        $grupo = $this->limpiarCadena($_POST['VCH_GRUPO'] ?? '');
        $campo = $this->limpiarCadena($_POST['VCH_CAMPO'] ?? '');
        $nombre = $this->limpiarCadena($_POST['VCH_NOMBRE_PLANTILLA'] ?? '');
        $descripcion = $this->limpiarCadena($_POST['VCH_DESCRIPCION'] ?? '');
        $juego = $this->limpiarCadena($_POST['VCH_JUEGO'] ?? '');
        $codigo = $this->limpiarCadena($_POST['VCH_CODIGO'] ?? '');
        $estado = $this->limpiarCadena($_POST['VCH_ESTADO'] ?? '1');
        $obligatorio = $this->limpiarCadena($_POST['VCH_OBLIGATORIO'] ?? 'N');
        $orden = $this->limpiarCadena($_POST['NUM_ORDEN'] ?? '1');

        // Validaciones
        if (empty($id_plantilla)) {
            return json_encode([
                'status' => 'error',
                'msg' => 'El campo ID Plantilla es obligatorio'
            ]);
        }

        // Verificar que la plantilla existe
        $check_plantilla = $this->plantillaModel->buscarPlantillaPorIdModelo($id_plantilla);
        if ($check_plantilla->rowCount() == 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'La plantilla con ID ' . $id_plantilla . ' no existe'
            ]);
        }

        // Obtener siguiente ID
        $id = $this->plantillaModel->obtenerSiguienteIdDetalle();

        $detalle_datos_reg = [
            [
                "campo_nombre" => "NUM_ID_DET_PLANTILLA",
                "campo_marcador" => ":ID",
                "campo_valor" => $id
            ],
            [
                "campo_nombre" => "NUM_ID_PLANTILLA",
                "campo_marcador" => ":IDPlantilla",
                "campo_valor" => $id_plantilla
            ],
            [
                "campo_nombre" => "VCH_GRUPO",
                "campo_marcador" => ":Grupo",
                "campo_valor" => $grupo
            ],
            [
                "campo_nombre" => "VCH_CAMPO",
                "campo_marcador" => ":Campo",
                "campo_valor" => $campo
            ],
            [
                "campo_nombre" => "VCH_NOMBRE_PLANTILLA",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "VCH_DESCRIPCION",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "VCH_JUEGO",
                "campo_marcador" => ":Juego",
                "campo_valor" => $juego
            ],
            [
                "campo_nombre" => "VCH_CODIGO",
                "campo_marcador" => ":Codigo",
                "campo_valor" => $codigo
            ],
            [
                "campo_nombre" => "VCH_ESTADO",
                "campo_marcador" => ":Estado",
                "campo_valor" => $estado
            ],
            [
                "campo_nombre" => "VCH_OBLIGATORIO",
                "campo_marcador" => ":Obligatorio",
                "campo_valor" => $obligatorio
            ],
            [
                "campo_nombre" => "NUM_ORDEN",
                "campo_marcador" => ":Orden",
                "campo_valor" => $orden
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

        $registrar = $this->plantillaModel->registrarDetalleModelo($detalle_datos_reg);

        if ($registrar->rowCount() == 1) {
            return json_encode([
                'status' => 'ok',
                'msg' => 'Detalle registrado correctamente',
                'id' => $id
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo registrar el detalle'
            ]);
        }
    }

    /*---------- Actualizar Plantilla ----------*/
    public function actualizarPlantillaControlador()
    {
        $id = $this->limpiarCadena($_POST['NUM_ID_PLANTILLA']);

        // Verificar que la plantilla existe
        $datos = $this->plantillaModel->buscarPlantillaPorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontró la plantilla'
            ]);
        }

        $tienda_id = $this->limpiarCadena($_POST['NUM_ID_TIENDA']);
        $clase_id = $this->limpiarCadena($_POST['NUM_ID_CLASE']);
        $cat1 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N1'] ?? '');
        $cat2 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N2'] ?? '');
        $cat3 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N3'] ?? '');
        $cat4 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N4'] ?? '');
        $cat5 = $this->limpiarCadena($_POST['VCH_CATEGORIA_N5'] ?? '');
        $estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

        if (empty($tienda_id) || empty($clase_id)) {
            return json_encode([
                'status' => 'error',
                'msg' => 'Los campos Tienda y Clase son obligatorios'
            ]);
        }

        $plantilla_datos_up = [
            [
                "campo_nombre" => "NUM_ID_TIENDA",
                "campo_marcador" => ":Tienda",
                "campo_valor" => $tienda_id
            ],
            [
                "campo_nombre" => "NUM_ID_CLASE",
                "campo_marcador" => ":Clase",
                "campo_valor" => $clase_id
            ],
            [
                "campo_nombre" => "VCH_CATEGORIA_N1",
                "campo_marcador" => ":Cat1",
                "campo_valor" => $cat1
            ],
            [
                "campo_nombre" => "VCH_CATEGORIA_N2",
                "campo_marcador" => ":Cat2",
                "campo_valor" => $cat2
            ],
            [
                "campo_nombre" => "VCH_CATEGORIA_N3",
                "campo_marcador" => ":Cat3",
                "campo_valor" => $cat3
            ],
            [
                "campo_nombre" => "VCH_CATEGORIA_N4",
                "campo_marcador" => ":Cat4",
                "campo_valor" => $cat4
            ],
            [
                "campo_nombre" => "VCH_CATEGORIA_N5",
                "campo_marcador" => ":Cat5",
                "campo_valor" => $cat5
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
            "condicion_campo" => "NUM_ID_PLANTILLA",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if ($this->plantillaModel->actualizarPlantillaModelo($plantilla_datos_up, $condicion)) {
            return json_encode([
                'status' => 'ok',
                'msg' => 'Plantilla actualizada correctamente'
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo actualizar la plantilla'
            ]);
        }
    }

    /*---------- Actualizar Detalle ----------*/
    public function actualizarDetalleControlador()
    {
        $id = $this->limpiarCadena($_POST['NUM_ID_DET_PLANTILLA']);

        // Verificar que el detalle existe
        $datos = $this->plantillaModel->buscarDetallePorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontró el detalle'
            ]);
        }

        $id_plantilla = $this->limpiarCadena($_POST['NUM_ID_PLANTILLA']);
        $grupo = $this->limpiarCadena($_POST['VCH_GRUPO'] ?? '');
        $campo = $this->limpiarCadena($_POST['VCH_CAMPO'] ?? '');
        $nombre = $this->limpiarCadena($_POST['VCH_NOMBRE_PLANTILLA'] ?? '');
        $descripcion = $this->limpiarCadena($_POST['VCH_DESCRIPCION'] ?? '');
        $juego = $this->limpiarCadena($_POST['VCH_JUEGO'] ?? '');
        $codigo = $this->limpiarCadena($_POST['VCH_CODIGO'] ?? '');
        $estado = $this->limpiarCadena($_POST['VCH_ESTADO']);
        $obligatorio = $this->limpiarCadena($_POST['VCH_OBLIGATORIO'] ?? 'N');
        $orden = $this->limpiarCadena($_POST['NUM_ORDEN'] ?? '1');

        if (empty($id_plantilla)) {
            return json_encode([
                'status' => 'error',
                'msg' => 'El campo ID Plantilla es obligatorio'
            ]);
        }

        $detalle_datos_up = [
            [
                "campo_nombre" => "NUM_ID_PLANTILLA",
                "campo_marcador" => ":IDPlantilla",
                "campo_valor" => $id_plantilla
            ],
            [
                "campo_nombre" => "VCH_GRUPO",
                "campo_marcador" => ":Grupo",
                "campo_valor" => $grupo
            ],
            [
                "campo_nombre" => "VCH_CAMPO",
                "campo_marcador" => ":Campo",
                "campo_valor" => $campo
            ],
            [
                "campo_nombre" => "VCH_NOMBRE_PLANTILLA",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "VCH_DESCRIPCION",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "VCH_JUEGO",
                "campo_marcador" => ":Juego",
                "campo_valor" => $juego
            ],
            [
                "campo_nombre" => "VCH_CODIGO",
                "campo_marcador" => ":Codigo",
                "campo_valor" => $codigo
            ],
            [
                "campo_nombre" => "VCH_ESTADO",
                "campo_marcador" => ":Estado",
                "campo_valor" => $estado
            ],
            [
                "campo_nombre" => "VCH_OBLIGATORIO",
                "campo_marcador" => ":Obligatorio",
                "campo_valor" => $obligatorio
            ],
            [
                "campo_nombre" => "NUM_ORDEN",
                "campo_marcador" => ":Orden",
                "campo_valor" => $orden
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
            "condicion_campo" => "NUM_ID_DET_PLANTILLA",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if ($this->plantillaModel->actualizarDetalleModelo($detalle_datos_up, $condicion)) {
            return json_encode([
                'status' => 'ok',
                'msg' => 'Detalle actualizado correctamente'
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo actualizar el detalle'
            ]);
        }
    }

    /*---------- Actualizar Estado Plantilla ----------*/
    public function actualizarEstadoPlantillaControlador()
    {
        $id = $this->limpiarCadena($_POST['id']);
        $estado = $this->limpiarCadena($_POST['estado']);

        $datos = $this->plantillaModel->buscarPlantillaPorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontró la plantilla'
            ]);
        }

        $actualizar = $this->plantillaModel->actualizarEstadoPlantillaModelo($id, $estado, $_SESSION['usuario']);

        if ($actualizar->rowCount() >= 0) {
            return json_encode([
                'status' => 'ok',
                'estado' => $estado,
                'fecha' => date("Y-m-d H:i:s")
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo actualizar el estado'
            ]);
        }
    }

    /*---------- Actualizar Estado Detalle ----------*/
    public function actualizarEstadoDetalleControlador()
    {
        $id = $this->limpiarCadena($_POST['id']);
        $estado = $this->limpiarCadena($_POST['estado']);

        $datos = $this->plantillaModel->buscarDetallePorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontró el detalle'
            ]);
        }

        $actualizar = $this->plantillaModel->actualizarEstadoDetalleModelo($id, $estado, $_SESSION['usuario']);

        if ($actualizar->rowCount() >= 0) {
            return json_encode([
                'status' => 'ok',
                'estado' => $estado,
                'fecha' => date("Y-m-d H:i:s")
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo actualizar el estado'
            ]);
        }
    }

    /*---------- Eliminar Plantilla ----------*/
    public function eliminarPlantillaControlador()
    {
        $id = $this->limpiarCadena($_POST['id']);

        $datos = $this->plantillaModel->buscarPlantillaPorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontró la plantilla'
            ]);
        }

        // Eliminar detalles asociados
        $this->plantillaModel->eliminarDetallesPorPlantillaModelo($id);

        // Eliminar plantilla
        $eliminar = $this->plantillaModel->eliminarPlantillaModelo($id);

        if ($eliminar->rowCount() == 1) {
            return json_encode([
                'status' => 'ok',
                'msg' => 'Plantilla eliminada correctamente'
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo eliminar la plantilla'
            ]);
        }
    }

    /*---------- Eliminar Detalle ----------*/
    public function eliminarDetalleControlador()
    {
        $id = $this->limpiarCadena($_POST['id']);

        $datos = $this->plantillaModel->buscarDetallePorIdModelo($id);
        if ($datos->rowCount() <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontró el detalle'
            ]);
        }

        $eliminar = $this->plantillaModel->eliminarDetalleModelo($id);

        if ($eliminar->rowCount() == 1) {
            return json_encode([
                'status' => 'ok',
                'msg' => 'Detalle eliminado correctamente'
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo eliminar el detalle'
            ]);
        }
    }

    /*---------- Listar Plantillas ----------*/
    public function listarPlantillasControlador($idClase = 0, $idTienda = 0)
    {
        if ($idClase == 0 && $idTienda == 0) {
            return $this->plantillaModel->listarTodasPlantillasModelo();
        } else {
            return $this->plantillaModel->listarPlantillasFiltrosModelo($idClase, $idTienda);
        }
    }

    /*---------- Listar Detalles ----------*/
    public function listarDetallesControlador($idPlantilla = 0, $idClase = 0, $idTienda = 0)
    {
        if ($idPlantilla == 0 && $idClase == 0 && $idTienda == 0) {
            return $this->plantillaModel->listarTodosDetallesModelo();
        } else {
            return $this->plantillaModel->listarDetallesFiltrosModelo($idPlantilla, $idClase, $idTienda);
        }
    }
}
