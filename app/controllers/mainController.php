<?php

namespace app\controllers;

abstract class mainController
{

    /*---------- Limpiar cadenas de texto ----------*/
    protected function limpiarCadena($cadena)
    {
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);
        $cadena = str_ireplace("<script>", "", $cadena);
        $cadena = str_ireplace("</script>", "", $cadena);
        $cadena = str_ireplace("<script src", "", $cadena);
        $cadena = str_ireplace("<script type=", "", $cadena);
        $cadena = str_ireplace("SELECT * FROM", "", $cadena);
        $cadena = str_ireplace("DELETE FROM", "", $cadena);
        $cadena = str_ireplace("INSERT INTO", "", $cadena);
        $cadena = str_ireplace("DROP TABLE", "", $cadena);
        $cadena = str_ireplace("DROP DATABASE", "", $cadena);
        $cadena = str_ireplace("TRUNCATE TABLE", "", $cadena);
        $cadena = str_ireplace("SHOW TABLES", "", $cadena);
        $cadena = str_ireplace("SHOW DATABASES", "", $cadena);
        $cadena = str_ireplace("<?php", "", $cadena);
        $cadena = str_ireplace("?>", "", $cadena);
        $cadena = str_ireplace("--", "", $cadena);
        $cadena = str_ireplace("^", "", $cadena);
        $cadena = str_ireplace("<", "", $cadena);
        $cadena = str_ireplace("[", "", $cadena);
        $cadena = str_ireplace("]", "", $cadena);
        $cadena = str_ireplace("==", "", $cadena);
        $cadena = str_ireplace(";", "", $cadena);
        $cadena = str_ireplace("::", "", $cadena);
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);
        return $cadena;
    }


    /*---------- Verificar datos (expresion regular) ----------*/
    protected function verificarDatos($filtro, $cadena)
    {
        if (preg_match("/^" . $filtro . "$/", $cadena)) {
            return false;
        } else {
            return true;
        }
    }


    /*---------- Verificar fechas ----------*/
    protected function verificarFecha($fecha)
    {
        $valores = explode('-', $fecha);
        if (count($valores) == 3 && checkdate($valores[1], $valores[2], $valores[0])) {
            return false;
        } else {
            return true;
        }
    }


    /*---------- Paginador de tablas ----------*/
    protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones)
    {
        $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

        if ($pagina <= 1) {
            $tabla .= '
            <a class="pagination-previous is-disabled" disabled >Anterior</a>
            <ul class="pagination-list">';
        } else {
            $tabla .= '
            <a class="pagination-previous" href="' . $url . ($pagina - 1) . '/">Anterior</a>
            <ul class="pagination-list">
                <li><a class="pagination-link" href="' . $url . '1/">1</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
            ';
        }

        $ci = 0;
        for ($i = $pagina; $i <= $numeroPaginas; $i++) {
            if ($ci >= $botones) {
                break;
            }
            if ($pagina == $i) {
                $tabla .= '<li><a class="pagination-link is-current" href="' . $url . $i . '/">' . $i . '</a></li>';
            } else {
                $tabla .= '<li><a class="pagination-link" href="' . $url . $i . '/">' . $i . '</a></li>';
            }
            $ci++;
        }

        if ($pagina == $numeroPaginas) {
            $tabla .= '
            </ul>
            <a class="pagination-next is-disabled" disabled >Siguiente</a>
            ';
        } else {
            $tabla .= '
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a class="pagination-link" href="' . $url . $numeroPaginas . '/">' . $numeroPaginas . '</a></li>
            </ul>
            <a class="pagination-next" href="' . $url . ($pagina + 1) . '/">Siguiente</a>
            ';
        }

        $tabla .= '</nav>';
        return $tabla;
    }


    /*---------- Generar alertas SweetAlert ----------*/
    protected function generarAlerta($tipo, $titulo, $texto, $redireccion = "")
    {
        if ($tipo == "simple") {
            $alerta = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '" . $titulo . "',
                    text: '" . $texto . "',
                    confirmButtonText: 'Aceptar'
                });
            </script>
            ";
        } elseif ($tipo == "recargar") {
            $alerta = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '" . $titulo . "',
                    text: '" . $texto . "',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            </script>
            ";
        } elseif ($tipo == "limpiar") {
            $alerta = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '" . $titulo . "',
                    text: '" . $texto . "',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector('.FormularioAjax').reset();
                    }
                });
            </script>
            ";
        } elseif ($tipo == "redireccionar") {
            $alerta = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '" . $titulo . "',
                    text: '" . $texto . "',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='" . $redireccion . "';
                    }
                });
            </script>
            ";
        } elseif ($tipo == "error") {
            $alerta = "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: '" . $titulo . "',
                    text: '" . $texto . "',
                    confirmButtonText: 'Aceptar'
                });
            </script>
            ";
        } elseif ($tipo == "warning") {
            $alerta = "
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: '" . $titulo . "',
                    text: '" . $texto . "',
                    confirmButtonText: 'Aceptar'
                });
            </script>
            ";
        }

        return $alerta;
    }


    /*---------- Encriptar cadenas ----------*/
    protected function encryption($string)
    {
        $output = false;
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }


    /*---------- Desencriptar cadenas ----------*/
    protected function decryption($string)
    {
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
        return $output;
    }


    /*---------- Generar códigos aleatorios ----------*/
    protected function generarCodigoAleatorio($letra, $longitud, $numero)
    {
        for ($i = 1; $i <= $longitud; $i++) {
            $aleatorio = rand(0, 9);
            $letra .= $aleatorio;
        }
        return $letra . "-" . $numero;
    }


    /*---------- Limpiar memória ----------*/
    protected function limpiarMemoria($sql)
    {
        $sql = null;
        $pdo = null;
    }


    /*---------- Formatear precio ----------*/
    protected function formatearPrecio($precio)
    {
        return number_format($precio, 2, '.', ',');
    }


    /*---------- Validar email ----------*/
    protected function validarEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }


    /*---------- Subir imagen ----------*/
    protected function subirImagen($imagen, $directorio, $nombreAnterior = "")
    {
        // Si hay imagen anterior, eliminarla
        if ($nombreAnterior != "" && is_file($directorio . $nombreAnterior)) {
            chmod($directorio . $nombreAnterior, 0777);
            unlink($directorio . $nombreAnterior);
        }

        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            if (!mkdir($directorio, 0777)) {
                return ["error" => "Error al crear el directorio"];
            }
        }

        // Validar tipo de imagen
        $permitidos = ["image/jpeg", "image/png", "image/jpg"];
        if (!in_array($imagen['type'], $permitidos)) {
            return ["error" => "Tipo de archivo no permitido"];
        }

        // Validar tamaño (2MB máximo)
        if ($imagen['size'] > 2097152) {
            return ["error" => "La imagen supera el peso permitido (2MB)"];
        }

        // Generar nombre único
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreFinal = bin2hex(random_bytes(10)) . "." . $extension;

        // Mover imagen
        if (move_uploaded_file($imagen['tmp_name'], $directorio . $nombreFinal)) {
            return ["success" => $nombreFinal];
        } else {
            return ["error" => "No se pudo subir la imagen"];
        }
    }


    /*---------- Eliminar imagen ----------*/
    protected function eliminarImagen($directorio, $nombre)
    {
        if (is_file($directorio . $nombre)) {
            chmod($directorio . $nombre, 0777);
            if (unlink($directorio . $nombre)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
