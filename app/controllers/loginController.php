<?php

namespace app\controllers;
use app\models\usuarioModel;

class loginController extends mainController {

    private $usuarioModel;

    public function __construct(){
        $this->usuarioModel = new usuarioModel();
    }

    /*---------- Controlador iniciar sesion ----------*/
    public function iniciarSesionControlador(){

        $usuario = $this->limpiarCadena($_POST['login_usuario']);
        $clave = $this->limpiarCadena($_POST['login_clave']);

        # Verificando campos obligatorios #
        if($usuario == "" || $clave == ""){
            echo $this->generarAlerta(
                "error",
                "Ocurrió un error inesperado",
                "No has llenado todos los campos que son obligatorios"
            );
            return;
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)){
            echo $this->generarAlerta(
                "error",
                "Ocurrió un error inesperado",
                "El USUARIO no coincide con el formato solicitado"
            );
            return;
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)){
            echo $this->generarAlerta(
                "error",
                "Ocurrió un error inesperado",
                "La CLAVE no coincide con el formato solicitado"
            );
            return;
        }

        # Verificando usuario #
        $check_usuario = $this->usuarioModel->buscarPorUsuario($usuario);

        if($check_usuario->rowCount() == 1){

            $check_usuario = $check_usuario->fetch();

            if($check_usuario['usuario_usuario'] == $usuario && 
               password_verify($clave, $check_usuario['usuario_clave'])){

                $_SESSION['id'] = $check_usuario['usuario_id'];
                $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                $_SESSION['foto'] = $check_usuario['usuario_foto'];

                if(headers_sent()){
                    echo "<script> window.location.href='" . APP_URL . "dashboard/'; </script>";
                } else {
                    header("Location: " . APP_URL . "dashboard/");
                }

            } else {
                echo $this->generarAlerta(
                    "error",
                    "Ocurrió un error inesperado",
                    "Usuario o clave incorrectos"
                );
            }

        } else {
            echo $this->generarAlerta(
                "error",
                "Ocurrió un error inesperado",
                "Usuario o clave incorrectos"
            );
        }
    }

    /*---------- Controlador cerrar sesion ----------*/
    public function cerrarSesionControlador(){
        session_destroy();

        if(headers_sent()){
            echo "<script> window.location.href='" . APP_URL . "login/'; </script>";
        } else {
            header("Location: " . APP_URL . "login/");
        }
    }

}