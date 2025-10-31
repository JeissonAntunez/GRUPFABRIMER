<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Plantillas</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }

        body {
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .page-header {
            background: var(--primary-gradient);
            padding: 2rem 0;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

       
        .card-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 16px 16px 0 0 !important;
            padding: 1.25rem 1.5rem;
            border: none;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
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
            background: #cbd5e1;
            transition: .3s;
            border-radius: 26px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        input:checked+.slider {
            background: var(--success-color);
        }

        input:checked+.slider:before {
            transform: translateX(24px);
        }

        .estado-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.813rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .estado-badge.activo {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-badge.inactivo {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            border: none;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.001);
        }

        .editing-row,
        .adding-row {
            background: #fef3c7 !important;
            box-shadow: inset 0 0 0 2px var(--warning-color);
        }

        .editing-row td,
        .adding-row td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .form-control-sm,
        .form-select-sm {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .form-control-sm:focus,
        .form-select-sm:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
        }

        .btn-success {
            background: var(--success-color);
            border: none;
            border-radius: 8px;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
        }

        .btn-warning {
            background: var(--warning-color);
            border: none;
            border-radius: 8px;
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            border: none;
            border-radius: 8px;
            color: white;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: white !important;
            border-radius: 8px;
        }

        .alert {
            border-radius: 12px;
            border: none;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .adding-row,
        .editing-row {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>

<body>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1 class="mb-2 fw-bold">
                <i class="mdi mdi-file-document-multiple"></i> Gestión de Plantillas
            </h1>
            <p class="mb-0 opacity-90">Administra las plantillas y sus detalles de forma eficiente</p>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Filtros -->
        <div class="filter-card">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="mdi mdi-filter"></i> Filtrar por Clase
                    </label>
                    <select id="f_clase" class="form-select">
                        <option value="0">Todas las clases</option>
                        <?php
                        require_once "./app/controllers/claseController.php";
                        $insClase = new app\controllers\claseController();
                        $clases = $insClase->listarClasesControlador();

                        if ($clases->rowCount() > 0) {
                            while ($clase = $clases->fetch()) {
                                echo '<option value="' . $clase['NUM_ID_CLASE'] . '">' . $clase['VCH_NOMBRE'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="mdi mdi-store"></i> Filtrar por Tienda
                    </label>
                    <select id="f_tienda" class="form-select">
                        <option value="0">Todas las tiendas</option>
                        <?php
                        require_once "./app/controllers/tiendaController.php";
                        $insTienda = new app\controllers\tiendaController();
                        $tiendas = $insTienda->listarTiendasControlador();

                        if ($tiendas->rowCount() > 0) {
                            while ($tienda = $tiendas->fetch()) {
                                echo '<option value="' . $tienda['NUM_ID_TIENDA'] . '">' . $tienda['VCH_TIENDA'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="btnFilter" class="btn btn-primary">
                        <i class="mdi mdi-magnify"></i> Aplicar Filtros
                    </button>
                    <button id="btnClear" class="btn btn-secondary">
                        <i class="mdi mdi-filter-remove"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla Plantillas -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="mdi mdi-table"></i> Plantillas
                </h5>
                <button id="btnAddPlantilla" class="btn btn-success btn-sm">
                    <i class="mdi mdi-plus-circle"></i> Nueva Plantilla
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPlantillas" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tienda</th>
                                <th>Clase</th>
                                <th>Categoría N1</th>
                                <th>Categoría N2</th>
                                <th>Categoría N3</th>
                                <th>Categoría N4</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabla Detalles -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="mdi mdi-table-large"></i> Detalles de Plantilla
                </h5>
                <button id="btnAddDetalle" class="btn btn-success btn-sm">
                    <i class="mdi mdi-plus-circle"></i> Nuevo Detalle
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaDetalles" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Plantilla</th>
                                <th>Tienda</th>
                                <th>Grupo</th>
                                <th>Campo</th>
                                <th>Nombre</th>
                                <th>Juego</th>
                                <th>Código</th>
                                <th class="text-center">Estado</th>
                                <th>Orden</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Datos PHP para JS -->
    <script id="dataTiendas" type="application/json">
        <?php
        $tiendasArray = [];
        $tiendasResult = $insTienda->listarTiendasControlador();
        if ($tiendasResult->rowCount() > 0) {
            while ($tienda = $tiendasResult->fetch()) {
                if ($tienda['VCH_ESTADO'] == 1) {
                    $tiendasArray[] = [
                        'id' => $tienda['NUM_ID_TIENDA'],
                        'nombre' => $tienda['VCH_TIENDA']
                    ];
                }
            }
        }
        echo json_encode($tiendasArray);
        ?>
    </script>

    <script id="dataClases" type="application/json">
        <?php
        $clasesArray = [];
        $clasesResult = $insClase->listarClasesControlador();
        if ($clasesResult->rowCount() > 0) {
            while ($clase = $clasesResult->fetch()) {
                if ($clase['VCH_ESTADO'] == 1) {
                    $clasesArray[] = [
                        'id' => $clase['NUM_ID_CLASE'],
                        'nombre' => $clase['VCH_NOMBRE']
                    ];
                }
            }
        }
        echo json_encode($clasesArray);
        ?>
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const APP_URL = "<?php echo APP_URL; ?>";
    </script>

    <script src="<?php echo APP_URL; ?>app/views/js/plantilla.js"></script>

</body>

</html>