<div class="container is-fluid mb-6">
    <h1 class="title">Tiendas</h1>
    <h2 class="subtitle">Gestión de Tiendas</h2>
</div>

<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/tienda.css">

<div class="container pb-6 pt-6">

    <div class="columns">
        <div class="column has-text-centered">
            <button type="button" class="button is-success is-rounded" id="btnAddTienda">
                <i class="fas fa-plus"></i> &nbsp; Agregar Tienda
            </button>
        </div>
    </div>

    <?php

    use app\controllers\tiendaController;

    $insTienda = new tiendaController();
    $tiendas = $insTienda->listarTiendasControlador();
    $totalTiendas = $tiendas->rowCount();
    ?>


    <p class="has-text-centered mb-4">
        Total de Tiendas: <strong id="totalTiendas"><?php echo $totalTiendas; ?></strong>
    </p>


    <div class="table-container">
        <table class="table is-bordered is-striped is-hoverable is-fullwidth" id="tablaTiendas">
            <thead>
                <tr class="has-background-dark has-text-white">
                    <th class="has-text-centered ">Nombre Tienda</th>
                    <th class="has-text-centered ">Estado</th>
                    <th class="has-text-centered ">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($totalTiendas > 0) {
                    while ($tienda = $tiendas->fetch()) {
                ?>
                        <tr data-id="<?php echo $tienda['NUM_ID_TIENDA']; ?>">
                            <td><?php echo htmlspecialchars($tienda['VCH_TIENDA']); ?></td>
                            <td class="has-text-centered">
                                <label class="switch">
                                    <input type="checkbox" class="toggle-estado"
                                        data-id="<?php echo $tienda['NUM_ID_TIENDA']; ?>"
                                        <?php echo $tienda['VCH_ESTADO'] ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="estado-label <?php echo $tienda['VCH_ESTADO'] ? 'activo' : 'inactivo'; ?>">
                                    <?php echo $tienda['VCH_ESTADO'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="has-text-centered">
                                <button type="button" class="button is-warning is-small is-rounded edit-tienda"
                                    data-id="<?php echo $tienda['NUM_ID_TIENDA']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($tienda['VCH_TIENDA']); ?>"
                                    data-estado="<?php echo $tienda['VCH_ESTADO']; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button type="button" class="button is-danger is-small is-rounded delete-tienda"
                                    data-id="<?php echo $tienda['NUM_ID_TIENDA']; ?>">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="3" class="has-text-centered">No hay tiendas registradas</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        const APP_URL = "<?php echo APP_URL; ?>";
    </script>
    <script src="<?php echo APP_URL; ?>app/views/js/tienda.js"></script>
</div>