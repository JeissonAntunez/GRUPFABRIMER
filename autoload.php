<?php

# ⭐ IMPORTANTE: Cargar librerías de Composer PRIMERO
require_once __DIR__ . "/vendor/autoload.php";

# Autoload de clases del proyecto
spl_autoload_register(function ($clase) {
    $archivo = __DIR__ . "/" . $clase . ".php";
    $archivo = str_replace("\\", "/", $archivo);

    if (is_file($archivo)) {
        require_once $archivo;
    }
});
