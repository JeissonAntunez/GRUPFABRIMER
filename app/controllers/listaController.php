<?php

namespace app\controllers;

use app\models\listaModel;
use app\models\tiendaModel;

class listaController extends mainController
{

    private $listaModel;
    private $tiendaModel;

    public function __construct()
    {
        $this->listaModel = new listaModel();
        $this->tiendaModel = new tiendaModel();
    }


    public function obtenerSiguienteIdControlador()
    {
        $nextId = $this->listaModel->obtenerSiguienteIdLista();
        $alerta = [
            "status" => "ok",
            "nextId" => $nextId
        ];
        return json_encode($alerta);
    }




    public function registrarListaControlador()
    {
        $tienda_id = $this->limpiarCadena($_POST['NUM_ID_TIENDA']);
        $juego = $this->limpiarCadena($_POST['VCH_JUEGO']);
        $codigo = $this->limpiarCadena($_POST['VCH_CODIGO']);
        $descripcion = $this->limpiarCadena($_POST['VCH_DESCRIPCION']);
        $estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

        if ($tienda_id == "" || $juego == "" || $codigo == "") {
            $alerta = [
                "status" => "error",
                "msg" => "Todos los campos son obligatorios"
            ];
            return json_encode($alerta);
        }

        # Verificar que la tienda existe
        $check_tienda = $this->tiendaModel->buscarTiendaPorId($tienda_id);
        if ($check_tienda->rowCount() == 0) {
            $alerta = [
                "status" => "error",
                "msg" => "La tienda seleccionada no existe"
            ];
            return json_encode($alerta);
        }

        # ⭐ VALIDACIÓN FLEXIBLE: Acepta guiones bajos
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s_-]{1,100}", $juego)) {
            $alerta = [
                "status" => "error",
                "msg" => "El JUEGO contiene caracteres no permitidos"
            ];
            return json_encode($alerta);
        }

        if ($this->verificarDatos("[a-zA-Z0-9\s_-]{1,50}", $codigo)) {
            $alerta = [
                "status" => "error",
                "msg" => "El CÓDIGO contiene caracteres no permitidos"
            ];
            return json_encode($alerta);
        }

        # Verificando código de lista
        $check_codigo = $this->listaModel->verificarCodigoExiste($codigo);
        if ($check_codigo->rowCount() > 0) {
            $alerta = [
                "status" => "error",
                "msg" => "El CÓDIGO ya está registrado"
            ];
            return json_encode($alerta);
        }

        # Obtener siguiente ID disponible
        $nextId = $this->listaModel->obtenerSiguienteIdLista();

        $lista_datos_reg = [
            [
                "campo_nombre" => "NUM_ID_LISTA",
                "campo_marcador" => ":ID",
                "campo_valor" => $nextId
            ],
            [
                "campo_nombre" => "NUM_ID_TIENDA",
                "campo_marcador" => ":Tienda",
                "campo_valor" => $tienda_id
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
                "campo_nombre" => "VCH_DESCRIPCION",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
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

        $registrar_lista = $this->listaModel->registrarListaModelo($lista_datos_reg);

        if ($registrar_lista->rowCount() == 1) {
            $tienda_data = $check_tienda->fetch();

            $alerta = [
                "status" => "ok",
                "id" => $nextId,
                "tienda" => $tienda_data['VCH_TIENDA'],
                "juego" => $juego,
                "codigo" => $codigo,
                "descripcion" => $descripcion,
                "estado" => $estado,
                "usuario" => $_SESSION['usuario'],
                "fecha" => date("Y-m-d H:i:s"),
                "msg" => "Lista registrada correctamente"
            ];
        } else {
            $alerta = [
                "status" => "error",
                "msg" => "No se pudo registrar la lista"
            ];
        }

        return json_encode($alerta);
    }



    /*---------- Controlador actualizar lista ----------*/
    public function actualizarListaControlador()
    {
        $id = $this->limpiarCadena($_POST['NUM_ID_LISTA']);

        # Verificando lista
        $datos = $this->listaModel->buscarListaPorId($id);
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "status" => "error",
                "msg" => "No se encontró la lista en el sistema"
            ];
            return json_encode($alerta);
        } else {
            $datos = $datos->fetch();
        }

        # Almacenando datos
        $tienda_id = $this->limpiarCadena($_POST['NUM_ID_TIENDA']);
        $juego = $this->limpiarCadena($_POST['VCH_JUEGO']);
        $codigo = $this->limpiarCadena($_POST['VCH_CODIGO']);
        $descripcion = $this->limpiarCadena($_POST['VCH_DESCRIPCION']);
        $estado = $this->limpiarCadena($_POST['VCH_ESTADO']);

        # Verificando campos obligatorios
        if ($tienda_id == "" || $juego == "" || $codigo == "") {
            $alerta = [
                "status" => "error",
                "msg" => "Todos los campos son obligatorios"
            ];
            return json_encode($alerta);
        }

        # Verificar que la tienda existe
        $check_tienda = $this->tiendaModel->buscarTiendaPorId($tienda_id);
        if ($check_tienda->rowCount() == 0) {
            $alerta = [
                "status" => "error",
                "msg" => "La tienda seleccionada no existe"
            ];
            return json_encode($alerta);
        }

        # VALIDACIÓN FLEXIBLE PARA JUEGO: Acepta letras, números, espacios, guiones y guiones bajos
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s_-]{1,100}", $juego)) {
            $alerta = [
                "status" => "error",
                "msg" => "El JUEGO contiene caracteres no permitidos"
            ];
            return json_encode($alerta);
        }

        #VALIDACIÓN FLEXIBLE PARA CÓDIGO: Acepta letras, números, espacios, guiones y guiones bajos
        if ($this->verificarDatos("[a-zA-Z0-9\s_-]{1,50}", $codigo)) {
            $alerta = [
                "status" => "error",
                "msg" => "El CÓDIGO contiene caracteres no permitidos"
            ];
            return json_encode($alerta);
        }

        # Verificando código de lista
        if ($datos['VCH_CODIGO'] != $codigo) {
            $check_codigo = $this->listaModel->verificarCodigoExiste($codigo, $id);
            if ($check_codigo->rowCount() > 0) {
                $alerta = [
                    "status" => "error",
                    "msg" => "El CÓDIGO ya está registrado"
                ];
                return json_encode($alerta);
            }
        }

        $lista_datos_up = [
            [
                "campo_nombre" => "NUM_ID_TIENDA",
                "campo_marcador" => ":Tienda",
                "campo_valor" => $tienda_id
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
                "campo_nombre" => "VCH_DESCRIPCION",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
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
            "condicion_campo" => "NUM_ID_LISTA",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if ($this->listaModel->actualizarListaModelo($lista_datos_up, $condicion)) {
            // Obtener nombre de la tienda para la respuesta
            $tienda_data = $check_tienda->fetch();

            $alerta = [
                "status" => "ok",
                "id" => $id,
                "tienda" => $tienda_data['VCH_TIENDA'],
                "juego" => $juego,
                "codigo" => $codigo,
                "descripcion" => $descripcion,
                "estado" => $estado,
                "userM" => $_SESSION['usuario'],
                "fecha" => date("Y-m-d H:i:s"),
                "msg" => "Lista actualizada correctamente"
            ];
        } else {
            $alerta = [
                "status" => "error",
                "msg" => "No se pudo actualizar la lista"
            ];
        }

        return json_encode($alerta);
    }

    /*---------- Controlador actualizar solo estado ----------*/
    public function actualizarEstadoControlador()
    {
        $id = $this->limpiarCadena($_POST['id']);
        $estado = $this->limpiarCadena($_POST['estado']);

        # Verificando lista
        $datos = $this->listaModel->buscarListaPorId($id);
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "status" => "error",
                "msg" => "No se encontró la lista"
            ];
            return json_encode($alerta);
        }

        $actualizar = $this->listaModel->actualizarEstadoListaModelo($id, $estado, $_SESSION['usuario']);

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


    /*---------- Controlador eliminar lista ----------*/
    public function eliminarListaControlador()
    {

        $id = $this->limpiarCadena($_POST['id']);

        # Verificando lista
        $datos = $this->listaModel->buscarListaPorId($id);
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "status" => "error",
                "msg" => "No se encontró la lista en el sistema"
            ];
            return json_encode($alerta);
        }

        $eliminarLista = $this->listaModel->eliminarListaModelo($id);

        if ($eliminarLista->rowCount() == 1) {
            $alerta = [
                "status" => "ok"
            ];
        } else {
            $alerta = [
                "status" => "error",
                "msg" => "No se pudo eliminar la lista"
            ];
        }

        return json_encode($alerta);
    }


    /*---------- Controlador listar listas ----------*/
    public function listarListasControlador()
    {
        return $this->listaModel->listarTodasListasModelo();
    }
}
