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
        :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --secondary-color: #64748b;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f8fafc;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #334155;
    line-height: 1.6;
}

/* Container */
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.container-fluid {
    max-width: 100%;
    padding: 0 2rem 3rem;
}

.row {
    margin-bottom: 1.5rem;
}

.col {
    width: 100%;
}

.text-center {
    text-align: center;
}

/* Page Header */
.page-header {
    background: var(--primary-gradient);
    padding: 2rem 0;
    margin-bottom: 2rem;
    color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
    font-weight: 700;
    font-size: 2.25rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-header h1 i {
    font-size: 2rem;
}

.page-header p {
    opacity: 0.9;
    font-size: 1rem;
    margin: 0;
}

/* Botones */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.938rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
    text-decoration: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn:active {
    transform: translateY(0);
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
}

.btn-secondary {
    background: var(--secondary-color);
    color: white;
}

.btn-success {
    background: var(--success-color);
    color: white;
}

.btn-warning {
    background: var(--warning-color);
    color: white;
}

.btn-danger {
    background: var(--danger-color);
    color: white;
}

.btn-sm, .btn-action {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    margin: 0 0.25rem;
}

.btn i {
    font-size: 1rem;
}

.btn-sm i {
    font-size: 0.875rem;
}

/* Card */
.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.card-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.25rem 1.5rem;
    border: none;
}

.card-header h5 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

/* Table */
.table-responsive {
    overflow-x: auto;
    border-radius: 12px;
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.938rem;
}

.table thead {
    background: #1e293b;
}

.table thead th {
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem;
    text-align: left;
    border: none;
    white-space: nowrap;
}

.table tbody td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.2s;
    background: white;
}

.table tbody tr:hover {
    background: #f8fafc;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

/* Badge */
.badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.813rem;
    font-weight: 600;
    font-family: 'Courier New', monospace;
}

.badge-info, .bg-info {
    background: #dbeafe !important;
    color: #1e40af !important;
}

/* Switch Toggle */
.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
    vertical-align: middle;
    margin-right: 0.5rem;
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

input:checked + .slider {
    background: #28a745;
}

input:checked + .slider:before {
    transform: translateX(20px);
}

/* Estado Label */
.estado-label {
    display: inline-block;
    font-weight: 600;
    font-size: 0.875rem;
    vertical-align: middle;
}

.estado-label.activo {
    color: #28a745;
}

.estado-label.inactivo {
    color: #dc3545;
}

/* Columna acciones */
.table td.acciones,
.table th.acciones {
    width: 150px;
    white-space: nowrap;
    text-align: center;
}

/* Fila de edición */
.editing-row {
    border-left: 4px solid var(--warning-color);
    background: rgba(255, 193, 7, 0.1) !important;
}

/* Required field */
.required-field {
    color: var(--danger-color);
    font-weight: bold;
}

/* Modal */
.modal {
    position: fixed;
    z-index: 1055;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    outline: 0;
}

.modal.fade {
    transition: opacity 0.15s linear;
}

.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

.modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 600px;
    pointer-events: none;
}

.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background: white;
    background-clip: padding-box;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    outline: 0;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100vw;
    height: 100vh;
    background-color: #000;
}

.modal-backdrop.fade {
    opacity: 0;
}

.modal-backdrop.show {
    opacity: 0.5;
}

.modal-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.25rem 1.5rem;
    border-radius: 16px 16px 0 0;
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: space-between;
    border-bottom: none;
}

.modal-header.bg-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #1f2937;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.5;
}

.btn-close, .btn-close-white {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em;
    color: white;
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    border: 0;
    border-radius: 0.25rem;
    opacity: 0.8;
    cursor: pointer;
}

.btn-close:hover, .btn-close-white:hover {
    opacity: 1;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    flex-wrap: wrap;
    flex-shrink: 0;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
    border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
    gap: 0.75rem;
}

/* Form */
.form-group, .mb-3 {
    margin-bottom: 1.25rem;
}

.form-group label, .form-label {
    display: block;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    color: #475569;
}

.form-control, .form-select {
    display: block;
    width: 100%;
    padding: 0.625rem 0.875rem;
    font-size: 0.938rem;
    font-weight: 400;
    line-height: 1.5;
    color: #334155;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #e2e8f0;
    appearance: none;
    border-radius: 8px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    color: #334155;
    background-color: #fff;
    border-color: #667eea;
    outline: 0;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:disabled, .form-select:disabled {
    background-color: #f1f5f9;
    opacity: 1;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

.form-select {
    padding-right: 2.5rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23334155' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 12px 12px;
}

/* DataTables personalizado */
.dataTables_wrapper {
    padding: 0;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_length select {
    padding: 0.375rem 2rem 0.375rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin: 0 0.5rem;
}

.dataTables_wrapper .dataTables_filter input {
    padding: 0.5rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.dataTables_wrapper .dataTables_info {
    padding-top: 1rem;
    color: #64748b;
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_paginate {
    padding-top: 1rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9;
    color: #334155;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--primary-gradient);
    color: white;
    font-weight: 600;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Animaciones */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0 1rem 2rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
    }

    .page-header h1 i {
        font-size: 1.5rem;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .btn-sm, .btn-action {
        width: auto;
        padding: 0.25rem 0.5rem;
    }

    .card-body {
        padding: 1rem;
    }

    .table thead th,
    .table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.813rem;
    }

    .modal-dialog {
        margin: 1rem;
    }

    .modal-body {
        padding: 1rem;
    }

    .estado-label {
        display: block;
        margin-top: 0.5rem;
    }
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* SweetAlert personalizado */
.swal2-popup {
    border-radius: 16px;
    font-family: 'Inter', sans-serif;
}

.swal2-title {
    color: #1e293b;
    font-weight: 600;
}

.swal2-confirm {
    background: var(--primary-gradient) !important;
    border-radius: 8px;
    padding: 0.625rem 1.5rem;
}

.swal2-cancel {
    background: var(--secondary-color) !important;
    border-radius: 8px;
    padding: 0.625rem 1.5rem;
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