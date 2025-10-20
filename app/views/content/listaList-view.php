<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Listas</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #ccc;
            transition: .4s;
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        .estado-label {
            margin-left: 8px;
            font-weight: 600;
        }

        .estado-label.activo {
            color: #28a745;
        }

        .estado-label.inactivo {
            color: #dc3545;
        }

        .table td.acciones,
        .table th.acciones {
            width: 200px;
            white-space: nowrap;
        }

        .editing-row {
            border-left: 4px solid #ffc107;
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .required-field {
            color: #dc3545;
            font-weight: bold;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            color: white;
        }
    </style>
</head>

<body>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="mdi mdi-format-list-bulleted"></i> Gestión de Listas
            </h1>
            <p class="mb-0">Administra las listas de juegos por tienda</p>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Botón Nueva Lista -->
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="mdi mdi-plus-circle"></i> Nueva Lista
                </button>
            </div>
        </div>

        <!-- Tabla de Listas -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-table"></i> Listado de Listas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaListas" class="table table-striped table-hover table-sm" style="width:100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tienda</th>
                                        <th>Juego</th>
                                        <th class="text-center">Código</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center acciones">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require_once "./app/controllers/listaController.php";
                                    $insLista = new app\controllers\listaController();

                                    $listas = $insLista->listarListasControlador();

                                    if ($listas->rowCount() > 0) {
                                        $listas = $listas->fetchAll();
                                        foreach ($listas as $row) {
                                            $estadoChecked = $row['VCH_ESTADO'] == 1 ? 'checked' : '';
                                            $estadoTexto = $row['VCH_ESTADO'] == 1 ? 'Activo' : 'Inactivo';
                                            $estadoClass = $row['VCH_ESTADO'] == 1 ? 'activo' : 'inactivo';
                                    ?>
                                            <tr>
                                                <td><?php echo $row['NOMBRE_TIENDA']; ?></td>
                                                <td><?php echo $row['VCH_JUEGO']; ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-info"><?php echo $row['VCH_CODIGO']; ?></span>
                                                </td>
                                                <td><?php echo $row['VCH_DESCRIPCION']; ?></td>
                                                <td class="text-center">
                                                    <label class="switch">
                                                        <input type="checkbox"
                                                            class="toggle-estado"
                                                            <?php echo $estadoChecked; ?>
                                                            data-id="<?php echo $row['NUM_ID_LISTA']; ?>"
                                                            data-estado="<?php echo $row['VCH_ESTADO']; ?>">
                                                        <span class="slider"></span>
                                                    </label>
                                                    <span class="estado-label <?php echo $estadoClass; ?>">
                                                        <?php echo $estadoTexto; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center acciones">
                                                    <button class="btn btn-warning btn-action btn-editar"
                                                        data-id="<?php echo $row['NUM_ID_LISTA']; ?>"
                                                        data-tienda="<?php echo $row['NUM_ID_TIENDA']; ?>"
                                                        data-juego="<?php echo $row['VCH_JUEGO']; ?>"
                                                        data-codigo="<?php echo $row['VCH_CODIGO']; ?>"
                                                        data-descripcion="<?php echo $row['VCH_DESCRIPCION']; ?>"
                                                        data-estado="<?php echo $row['VCH_ESTADO']; ?>">
                                                        <i class="mdi mdi-pencil"></i> Editar
                                                    </button>
                                                    <button class="btn btn-danger btn-action btn-eliminar"
                                                        data-id="<?php echo $row['NUM_ID_LISTA']; ?>">
                                                        <i class="mdi mdi-delete"></i> Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="mdi mdi-information"></i> No hay registros disponibles
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Registrar -->
    <div class="modal fade" id="modalRegistrar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="mdi mdi-plus-circle"></i> Nueva Lista
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegistrar" action="<?php echo APP_URL; ?>app/ajax/listaAjax.php" method="POST">
                        <input type="hidden" name="modulo_lista" value="registrar">

                        <div class="mb-3">
                            <label class="form-label">
                                Tienda <span class="required-field">*</span>
                            </label>
                            <select name="NUM_ID_TIENDA" class="form-select" required>
                                <option value="">Seleccione una tienda</option>
                                <?php
                                require_once "./app/controllers/tiendaController.php";
                                $insTienda = new app\controllers\tiendaController();
                                $tiendas = $insTienda->listarTiendasControlador();

                                if ($tiendas->rowCount() > 0) {
                                    $tiendas = $tiendas->fetchAll();
                                    foreach ($tiendas as $tienda) {
                                        if ($tienda['VCH_ESTADO'] == 1) {
                                ?>
                                            <option value="<?php echo $tienda['NUM_ID_TIENDA']; ?>">
                                                <?php echo $tienda['VCH_TIENDA']; ?>
                                            </option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Juego <span class="required-field">*</span>
                            </label>
                            <input type="text" name="VCH_JUEGO" class="form-control" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Código <span class="required-field">*</span>
                            </label>
                            <input type="text" name="VCH_CODIGO" class="form-control" maxlength="50" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="VCH_DESCRIPCION" class="form-control" rows="3" maxlength="255"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Estado <span class="required-field">*</span>
                            </label>
                            <select name="VCH_ESTADO" class="form-select" required>
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnGuardarRegistrar">
                        <i class="mdi mdi-content-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Actualizar -->
    <div class="modal fade" id="modalActualizar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="mdi mdi-pencil"></i> Actualizar Lista
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formActualizar" action="<?php echo APP_URL; ?>app/ajax/listaAjax.php" method="POST">
                        <input type="hidden" name="modulo_lista" value="actualizar">
                        <input type="hidden" name="NUM_ID_LISTA" id="updateId">

                        <div class="mb-3">
                            <label class="form-label">
                                Tienda <span class="required-field">*</span>
                            </label>
                            <select name="NUM_ID_TIENDA" id="updateTienda" class="form-select" required>
                                <option value="">Seleccione una tienda</option>
                                <?php
                                if (isset($tiendas) && is_array($tiendas)) {
                                    foreach ($tiendas as $tienda) {
                                        if ($tienda['VCH_ESTADO'] == 1) {
                                ?>
                                            <option value="<?php echo $tienda['NUM_ID_TIENDA']; ?>">
                                                <?php echo $tienda['VCH_TIENDA']; ?>
                                            </option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Juego <span class="required-field">*</span>
                            </label>
                            <input type="text" name="VCH_JUEGO" id="updateJuego" class="form-control" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Código <span class="required-field">*</span>
                            </label>
                            <input type="text" name="VCH_CODIGO" id="updateCodigo" class="form-control" maxlength="50" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="VCH_DESCRIPCION" id="updateDescripcion" class="form-control" rows="3" maxlength="255"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Estado <span class="required-field">*</span>
                            </label>
                            <select name="VCH_ESTADO" id="updateEstado" class="form-select" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" id="btnGuardarActualizar">
                        <i class="mdi mdi-content-save"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script>
        const APP_URL = "<?php echo APP_URL; ?>";

    </script>

    <script src="<?php echo APP_URL; ?>app/views/js/lista.js"></script>
</body>

</html>