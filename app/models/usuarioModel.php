<?php

namespace app\models;

class usuarioModel extends mainModel
{

    /*---------- Buscar usuario por nombre de usuario ----------*/
    public function buscarPorUsuario($usuario)
    {
        $sql = $this->conectar()->prepare("SELECT * FROM usuario WHERE usuario_usuario = :Usuario");
        $sql->bindParam(":Usuario", $usuario);
        $sql->execute();
        return $sql;
    }

    /*---------- Buscar usuario por ID ----------*/
    public function buscarPorId($id)
    {
        $sql = $this->conectar()->prepare("SELECT * FROM usuario WHERE usuario_id = :ID");
        $sql->bindParam(":ID", $id);
        $sql->execute();
        return $sql;
    }

    // Agrega más métodos específicos de usuario aquí...

}
