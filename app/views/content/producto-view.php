<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Productos</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container-main {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--primary);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px 10px;
            font-size: 0.85rem;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .table td {
            padding: 12px 10px;
            font-size: 0.85rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        .adding-row,
        .editing-row {
            background-color: #fff3cd !important;
            border: 2px solid var(--warning);
        }

        .adding-row td,
        .editing-row td {
            padding: 2px;
        }


        .form-select-sm {
            font-size: 0.8rem;
            padding: 4px 8px;

        }

        .form-control-sm {
            width: 200px;

        }

        .filter-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-icon {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        @media (max-width: 768px) {
            .stats-number {
                font-size: 1.8rem;
            }

            .table thead th,
            .table td {
                font-size: 0.75rem;
                padding: 8px 6px;
            }
        }
    </style>
</head>

<body>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1 class="mb-0">
                <i class="fas fa-box"></i> Gestión de Productos
            </h1>
            <p class="mb-0">Sistema completo de administración de productos con plantillas dinámicas</p>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Estadísticas -->
        <div class="row mb-4" id="statsRow">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-number" id="totalProductos">0</div>
                    <div class="stats-label"><i class="fas fa-box"></i> Total Productos</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-number" id="conStock">0</div>
                    <div class="stats-label"><i class="fas fa-check-circle"></i> Con Stock</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-number" id="conOferta">0</div>
                    <div class="stats-label"><i class="fas fa-tag"></i> Con Oferta</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-number" id="precioPromedio">$0</div>
                    <div class="stats-label"><i class="fas fa-dollar-sign"></i> Precio Promedio</div>
                </div>
            </div>
        </div>

        <!-- Filtros y Acciones -->
        <div class="filter-section">
            <div class="row g-3 align-items-end">
                <div class="col-lg-2 col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-tags"></i> Clase
                    </label>
                    <select id="f_clase" class="form-select">
                        <option value="0">-- Todas --</option>
                        <?php
                        require_once "./app/controllers/claseController.php";
                        $insClase = new app\controllers\claseController();
                        $clases = $insClase->listarClasesControlador();
                        while ($clase = $clases->fetch()) {
                            echo '<option value="' . $clase['NUM_ID_CLASE'] . '">' . $clase['VCH_NOMBRE'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-store"></i> Tienda
                    </label>
                    <select id="f_tienda" class="form-select">
                        <option value="0">-- Todas --</option>
                        <?php
                        require_once "./app/controllers/tiendaController.php";
                        $insTienda = new app\controllers\tiendaController();
                        $tiendas = $insTienda->listarTiendasControlador();
                        while ($tienda = $tiendas->fetch()) {
                            echo '<option value="' . $tienda['NUM_ID_TIENDA'] . '">' . $tienda['VCH_TIENDA'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-search"></i> Buscar
                    </label>
                    <input type="text" id="f_busqueda" class="form-control" placeholder="Nombre, SKU, Marca...">
                </div>
                <div class="col-lg-5 col-md-12">
                    <div class="d-flex flex-wrap gap-2">
                        <button id="btnFiltrar" class="btn btn-gradient">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <button id="btnLimpiar" class="btn btn-secondary">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                        <button id="btnDescargarPlantillaVacia" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Plantilla Vacía
                        </button>
                        <button id="btnDescargarPlantilla" class="btn btn-info">
                            <i class="fas fa-file-download"></i> Plantilla Específica
                        </button>
                        <button id="btnImportarCSV" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalImportar">
                            <i class="fas fa-file-upload"></i> Importar CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="container-main">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-list"></i> Listado de Productos
                    <small class="text-muted" id="totalMostrado">(0)</small>
                </h4>
                <button id="btnAgregar" class="btn btn-gradient">
                    <i class="fas fa-plus-circle"></i> Nuevo Producto
                </button>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table id="tablaProductos" class="table table-hover table-sm table-bordered">
                        <!-- La tabla se construirá dinámicamente -->
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Importar CSV -->
    <div class="modal fade" id="modalImportar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-upload"></i> Importar Productos desde CSV
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formImportar" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Seleccionar archivo CSV</label>
                            <input type="file" class="form-control" id="archivoCSV" accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> El archivo puede ser CSV o Excel (.xlsx, .xls)
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb"></i> Tipos de Plantillas Disponibles:</h6>

                            <div class="mb-3">
                                <strong>1️⃣ Plantilla Vacía (Recomendada para nuevos productos)</strong>
                                <ul class="mt-2 mb-2">
                                    <li>Contiene <strong>todos los campos</strong> de la tabla producto</li>
                                    <li>Sin datos, lista para completar</li>
                                    <li>No requiere filtros de clase o tienda</li>
                                    <li>Ideal para carga masiva de productos nuevos</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <strong>2️⃣ Plantilla Específica (Para productos con configuración)</strong>
                                <ul class="mt-2 mb-2">
                                    <li>Requiere <strong>seleccionar Clase</strong> en los filtros</li>
                                    <li>Genera campos según configuración de plantilla</li>
                                    <li>Útil para productos con estructura específica</li>
                                    <li>Puede variar según clase y tienda seleccionada</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle"></i> Campos Obligatorios:</h6>
                            <ul class="mb-0">
                                <li><strong>NUM_ID_CLASE</strong> * - ID numérico de la clase</li>
                                <li><strong>VCH_NOMBRE</strong> * - Nombre del producto</li>
                                <li><strong>NUM_PRICE_FALABELLA</strong> * - Precio del producto</li>
                            </ul>
                        </div>

                        <div class="alert alert-success">
                            <h6 class="fw-bold"><i class="fas fa-check-circle"></i> Notas Importantes:</h6>
                            <ul class="mb-0">
                                <li>Los encabezados pueden usar nombres de BD o nombres legibles</li>
                                <li>Ejemplo: "VCH_NOMBRE" o "NOMBRE" funcionan igual</li>
                                <li><strong>NUM_ID_PRODUCTO</strong> se genera automáticamente (no incluir)</li>
                                <li>Fechas y usuarios de creación se generan automáticamente</li>
                                <li>Los valores vacíos se guardan como NULL</li>
                                <li>Formato: CSV UTF-8 con comas como separadores</li>
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-gradient" id="btnProcesarCSV">
                        <i class="fas fa-upload"></i> Importar
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

    <script src="<?php echo APP_URL; ?>app/views/js/producto2.js"></script>

    <!-- Datos PHP para JavaScript -->
    <script id="dataClases" type="application/json">
        <?php
        $clasesArray = [];
        $clasesResult = $insClase->listarClasesControlador();
        while ($clase = $clasesResult->fetch()) {
            if ($clase['VCH_ESTADO'] == 1) {
                $clasesArray[] = [
                    'id' => $clase['NUM_ID_CLASE'],
                    'nombre' => $clase['VCH_NOMBRE']
                ];
            }
        }
        echo json_encode($clasesArray);
        ?>
    </script>

    <script id="dataTiendas" type="application/json">
        <?php
        $tiendasArray = [];
        $tiendasResult = $insTienda->listarTiendasControlador();
        while ($tienda = $tiendasResult->fetch()) {
            if ($tienda['VCH_ESTADO'] == 1) {
                $tiendasArray[] = [
                    'id' => $tienda['NUM_ID_TIENDA'],
                    'nombre' => $tienda['VCH_TIENDA']
                ];
            }
        }
        echo json_encode($tiendasArray);
        ?>
    </script>
</body>

</html>