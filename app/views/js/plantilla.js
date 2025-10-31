$(document).ready(function() {

    console.log('🚀 Sistema de Plantillas Iniciado');

    // Variables globales
    let TIENDAS = [];
    let CLASES = [];
    let plantillaSeleccionadaId = null;
    let tablaPlantillas, tablaDetalles;

    // Cargar datos de referencia
    function cargarReferencias() {
        try {
            TIENDAS = JSON.parse($('#dataTiendas').text() || '[]');
            CLASES = JSON.parse($('#dataClases').text() || '[]');
            console.log('✅ Referencias cargadas:', TIENDAS.length, 'tiendas,', CLASES.length, 'clases');
        } catch(e) {
            console.error('❌ Error cargando referencias:', e);
        }
    }

    cargarReferencias();

    // ========== CONFIGURACIÓN DATATABLES ==========
    const configEspanol = {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible",
        "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
        "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
        "sSearch": "Buscar:",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        }
    };

    // ========== FUNCIONES AUXILIARES ==========
    function generarSelectTiendas(valorSeleccionado = '') {
        let options = '<option value="">-- Seleccione Tienda --</option>';
        TIENDAS.forEach(t => {
            const selected = t.id == valorSeleccionado ? 'selected' : '';
            options += `<option value="${t.id}" ${selected}>${t.nombre}</option>`;
        });
        return `<select class="form-select form-select-sm" name="NUM_ID_TIENDA" required>${options}</select>`;
    }

    function generarSelectClases(valorSeleccionado = '') {
        let options = '<option value="">-- Seleccione Clase --</option>';
        CLASES.forEach(c => {
            const selected = c.id == valorSeleccionado ? 'selected' : '';
            options += `<option value="${c.id}" ${selected}>${c.nombre}</option>`;
        });
        return `<select class="form-select form-select-sm" name="NUM_ID_CLASE" required>${options}</select>`;
    }

    function getNombreTienda(id) {
        const tienda = TIENDAS.find(t => t.id == id);
        return tienda ? tienda.nombre : 'N/A';
    }

    function getNombreClase(id) {
        const clase = CLASES.find(c => c.id == id);
        return clase ? clase.nombre : 'N/A';
    }

    // ========== INICIALIZAR DATATABLES ==========
    tablaPlantillas = $('#tablaPlantillas').DataTable({
        language: configEspanol,
        pageLength: 10,
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [6, 7] } // Estado y Acciones
        ],
        data: []
    });

    tablaDetalles = $('#tablaDetalles').DataTable({
        language: configEspanol,
        pageLength: 10,
        responsive: true,
        order: [[8, 'asc']], // Ordenar por Orden
        columnDefs: [
            { orderable: false, targets: [7, 9] } // Estado y Acciones
        ],
        data: []
    });

    console.log('✅ DataTables inicializados');

    // ========== RENDERIZAR ESTADO ==========
    function renderEstadoToggle(id, estado, tipo) {
        const checked = estado == 1 ? 'checked' : '';
        const estadoClass = estado == 1 ? 'activo' : 'inactivo';
        const estadoText = estado == 1 ? 'Activo' : 'Inactivo';
        const toggleClass = tipo === 'plantilla' ? 'toggle-estado-plantilla' : 'toggle-estado-detalle';

        return `
            <div class="d-flex align-items-center justify-content-center gap-2">
                <label class="switch mb-0">
                    <input type="checkbox" class="${toggleClass}" data-id="${id}" ${checked}>
                    <span class="slider"></span>
                </label>
                <span class="estado-badge ${estadoClass}">
                    <i class="mdi mdi-${estado == 1 ? 'check-circle' : 'close-circle'}"></i>
                    ${estadoText}
                </span>
            </div>
        `;
    }

    // ========== RENDERIZAR ACCIONES ==========
    function renderAccionesPlantilla(row) {
        return `
            <div class="d-flex gap-1 justify-content-center">
                <button type="button" class="btn btn-warning btn-action btn-editar-plantilla"
                    data-id="${row.NUM_ID_PLANTILLA}"
                    data-tienda="${row.NUM_ID_TIENDA}"
                    data-clase="${row.NUM_ID_CLASE}"
                    data-cat1="${row.VCH_CATEGORIA_N1 || ''}"
                    data-cat2="${row.VCH_CATEGORIA_N2 || ''}"
                    data-cat3="${row.VCH_CATEGORIA_N3 || ''}"
                    data-cat4="${row.VCH_CATEGORIA_N4 || ''}"
                    data-estado="${row.VCH_ESTADO}"
                    title="Editar">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-action btn-eliminar-plantilla"
                    data-id="${row.NUM_ID_PLANTILLA}"
                    title="Eliminar">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;
    }

    function renderAccionesDetalle(row) {
        return `
            <div class="d-flex gap-1 justify-content-center">
                <button type="button" class="btn btn-warning btn-action btn-editar-detalle"
                    data-id="${row.NUM_ID_DET_PLANTILLA}"
                    data-idplantilla="${row.NUM_ID_PLANTILLA}"
                    data-grupo="${row.VCH_GRUPO || ''}"
                    data-campo="${row.VCH_CAMPO || ''}"
                    data-nombre="${row.VCH_NOMBRE_PLANTILLA || ''}"
                    data-juego="${row.VCH_JUEGO || ''}"
                    data-codigo="${row.VCH_CODIGO || ''}"
                    data-estado="${row.VCH_ESTADO}"
                    data-orden="${row.NUM_ORDEN}"
                    title="Editar">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-action btn-eliminar-detalle"
                    data-id="${row.NUM_ID_DET_PLANTILLA}"
                    title="Eliminar">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;
    }

    // ========== CARGAR DATOS ==========
    function cargarPlantillas() {
        const f_clase = $('#f_clase').val() || 0;
        const f_tienda = $('#f_tienda').val() || 0;

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: {
                modulo_plantilla: 'listar_plantillas',
                f_clase: f_clase,
                f_tienda: f_tienda
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    tablaPlantillas.clear();
                    response.data.forEach(row => {
                        tablaPlantillas.row.add([
                            getNombreTienda(row.NUM_ID_TIENDA),
                            getNombreClase(row.NUM_ID_CLASE),
                            row.VCH_CATEGORIA_N1 || '-',
                            row.VCH_CATEGORIA_N2 || '-',
                            row.VCH_CATEGORIA_N3 || '-',
                            row.VCH_CATEGORIA_N4 || '-',
                            renderEstadoToggle(row.NUM_ID_PLANTILLA, row.VCH_ESTADO, 'plantilla'),
                            renderAccionesPlantilla(row)
                        ]);
                    });
                    tablaPlantillas.draw();
                    console.log('Plantillas cargadas:', response.data.length);
                }
            },
            error: function(xhr) {
                console.error(' Error cargando plantillas:', xhr);
                Swal.fire('Error', 'Error al cargar las plantillas', 'error');
            }
        });
    }

    function cargarDetalles(idPlantilla = 0) {
        const f_clase = $('#f_clase').val() || 0;
        const f_tienda = $('#f_tienda').val() || 0;

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: {
                modulo_plantilla: 'listar_detalles',
                id_plantilla: idPlantilla,
                f_clase: f_clase,
                f_tienda: f_tienda
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    tablaDetalles.clear();
                    response.data.forEach(row => {
                        const plantillaInfo = `P-${row.NUM_ID_PLANTILLA}`;
                        const nombreTienda = row.NOMBRE_TIENDA || getNombreTienda(row.NUM_ID_TIENDA) || 'N/A';
                        
                        tablaDetalles.row.add([
                            plantillaInfo,
                            nombreTienda,
                            row.VCH_GRUPO || '-',
                            row.VCH_CAMPO || '-',
                            row.VCH_NOMBRE_PLANTILLA || '-',
                            row.VCH_JUEGO || '-',
                            row.VCH_CODIGO || '-',
                            renderEstadoToggle(row.NUM_ID_DET_PLANTILLA, row.VCH_ESTADO, 'detalle'),
                            row.NUM_ORDEN || '1',
                            renderAccionesDetalle(row)
                        ]);
                    });
                    tablaDetalles.draw();
                    console.log(' Detalles cargados:', response.data.length);
                }
            },
            error: function(xhr) {
                console.error(' Error cargando detalles:', xhr);
                Swal.fire('Error', 'Error al cargar los detalles', 'error');
            }
        });
    }

    // ========== AGREGAR PLANTILLA ==========
    $('#btnAddPlantilla').on('click', function() {
        if ($('.adding-row, .editing-row').length > 0) {
            Swal.fire('Atención', 'Complete o cancele la edición actual primero', 'warning');
            return;
        }

        const newRow = `
            <tr class="adding-row">
                <td>${generarSelectTiendas()}</td>
                <td>${generarSelectClases()}</td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N1" placeholder="Categoría N1"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N2" placeholder="Categoría N2"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N3" placeholder="Categoría N3"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N4" placeholder="Categoría N4"></td>
                <td class="text-center">
                    <select class="form-select form-select-sm" name="VCH_ESTADO">
                        <option value="1" selected>Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-success btn-sm guardar-plantilla">
                        <i class="mdi mdi-check"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancelar-edicion">
                        <i class="mdi mdi-close"></i> Cancelar
                    </button>
                </td>
            </tr>
        `;

        $('#tablaPlantillas tbody').prepend(newRow);
        $('html, body').animate({ scrollTop: $('#tablaPlantillas').offset().top - 100 }, 300);
    });

    // ========== GUARDAR PLANTILLA ==========
    $(document).on('click', '.guardar-plantilla', function() {
        const $row = $(this).closest('tr');

        const tiendaId = $row.find('select[name="NUM_ID_TIENDA"]').val();
        const claseId = $row.find('select[name="NUM_ID_CLASE"]').val();

        console.log('📝 Datos a guardar:', {
            tienda: tiendaId,
            clase: claseId
        });

        if (!tiendaId || !claseId || tiendaId === '' || claseId === '') {
            Swal.fire('Error', 'Complete los campos obligatorios (Tienda y Clase)', 'error');
            return;
        }

        const formData = {
            modulo_plantilla: 'registrar_plantilla',
            NUM_ID_TIENDA: parseInt(tiendaId),
            NUM_ID_CLASE: parseInt(claseId),
            VCH_CATEGORIA_N1: $row.find('input[name="VCH_CATEGORIA_N1"]').val() || '',
            VCH_CATEGORIA_N2: $row.find('input[name="VCH_CATEGORIA_N2"]').val() || '',
            VCH_CATEGORIA_N3: $row.find('input[name="VCH_CATEGORIA_N3"]').val() || '',
            VCH_CATEGORIA_N4: $row.find('input[name="VCH_CATEGORIA_N4"]').val() || '',
            VCH_CATEGORIA_N5: '',
            VCH_ESTADO: $row.find('select[name="VCH_ESTADO"]').val() || '1'
        };

        console.log('📤 Enviando formData:', formData);

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('📥 Respuesta del servidor:', response);
                if (response.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Plantilla registrada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $row.remove();
                    cargarPlantillas();
                } else {
                    Swal.fire('Error', response.msg || 'No se pudo registrar', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error guardando plantilla:', {xhr, status, error});
                console.error(' Respuesta del servidor:', xhr.responseText);
                Swal.fire('Error', 'Error de conexión al guardar: ' + error, 'error');
            }
        });
    });

    // ========== EDITAR PLANTILLA ==========
    $(document).on('click', '.btn-editar-plantilla', function() {
        if ($('.editing-row, .adding-row').length > 0) {
            Swal.fire('Atención', 'Complete o cancele la edición actual primero', 'warning');
            return;
        }

        const btn = $(this);
        const $row = btn.closest('tr');

        $row.addClass('editing-row');
        $row.html(`
            <td>${generarSelectTiendas(btn.data('tienda'))}</td>
            <td>${generarSelectClases(btn.data('clase'))}</td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N1" value="${btn.data('cat1')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N2" value="${btn.data('cat2')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N3" value="${btn.data('cat3')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_CATEGORIA_N4" value="${btn.data('cat4')}"></td>
            <td class="text-center">
                <select class="form-select form-select-sm" name="VCH_ESTADO">
                    <option value="1" ${btn.data('estado') == 1 ? 'selected' : ''}>Activo</option>
                    <option value="0" ${btn.data('estado') == 0 ? 'selected' : ''}>Inactivo</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-success btn-sm actualizar-plantilla" data-id="${btn.data('id')}">
                    <i class="mdi mdi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm cancelar-edicion">
                    <i class="mdi mdi-close"></i> Cancelar
                </button>
            </td>
        `);
    });

    // ========== ACTUALIZAR PLANTILLA ==========
    $(document).on('click', '.actualizar-plantilla', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');

        const formData = {
            modulo_plantilla: 'actualizar_plantilla',
            NUM_ID_PLANTILLA: id,
            NUM_ID_TIENDA: $row.find('select[name="NUM_ID_TIENDA"]').val(),
            NUM_ID_CLASE: $row.find('select[name="NUM_ID_CLASE"]').val(),
            VCH_CATEGORIA_N1: $row.find('input[name="VCH_CATEGORIA_N1"]').val(),
            VCH_CATEGORIA_N2: $row.find('input[name="VCH_CATEGORIA_N2"]').val(),
            VCH_CATEGORIA_N3: $row.find('input[name="VCH_CATEGORIA_N3"]').val(),
            VCH_CATEGORIA_N4: $row.find('input[name="VCH_CATEGORIA_N4"]').val(),
            VCH_CATEGORIA_N5: '',
            VCH_ESTADO: $row.find('select[name="VCH_ESTADO"]').val()
        };

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Plantilla actualizada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    cargarPlantillas();
                } else {
                    Swal.fire('Error', response.msg || 'No se pudo actualizar', 'error');
                }
            },
            error: function(xhr) {
                console.error(' Error actualizando plantilla:', xhr);
                Swal.fire('Error', 'Error de conexión al actualizar', 'error');
            }
        });
    });

    // ========== CANCELAR EDICIÓN ==========
    $(document).on('click', '.cancelar-edicion', function() {
        const $row = $(this).closest('tr');
        if ($row.hasClass('adding-row')) {
            $row.remove();
        } else {
            cargarPlantillas();
        }
    });

    // ========== CAMBIAR ESTADO PLANTILLA ==========
    $(document).on('change', '.toggle-estado-plantilla', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const nuevoEstado = this.checked ? 1 : 0;

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: {
                modulo_plantilla: 'actualizar_estado_plantilla',
                id: id,
                estado: nuevoEstado
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    const badge = checkbox.closest('td').find('.estado-badge');
                    badge.removeClass('activo inactivo').addClass(nuevoEstado == 1 ? 'activo' : 'inactivo');
                    badge.html(`<i class="mdi mdi-${nuevoEstado == 1 ? 'check-circle' : 'close-circle'}"></i> ${nuevoEstado == 1 ? 'Activo' : 'Inactivo'}`);
                } else {
                    checkbox.prop('checked', !nuevoEstado);
                    Swal.fire('Error', response.msg, 'error');
                }
            },
            error: function() {
                checkbox.prop('checked', !nuevoEstado);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });

    // ========== ELIMINAR PLANTILLA ==========
    $(document).on('click', '.btn-eliminar-plantilla', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar plantilla?',
            text: 'También se eliminarán sus detalles asociados',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + 'app/ajax/plantillaAjax.php',
                    method: 'POST',
                    data: {
                        modulo_plantilla: 'eliminar_plantilla',
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'ok') {
                            Swal.fire('Eliminado', 'Plantilla eliminada correctamente', 'success');
                            cargarPlantillas();
                            cargarDetalles();
                        } else {
                            Swal.fire('Error', response.msg, 'error');
                        }
                    }
                });
            }
        });
    });

    // ========== AGREGAR DETALLE ==========
    $('#btnAddDetalle').on('click', function() {
        if ($('.adding-row, .editing-row').length > 0) {
            Swal.fire('Atención', 'Complete o cancele la edición actual primero', 'warning');
            return;
        }

        // Generar select de plantillas
        let selectPlantillas = '<select class="form-select form-select-sm" name="NUM_ID_PLANTILLA" required><option value="">-- Seleccione Plantilla --</option>';
        
        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: { modulo_plantilla: 'listar_plantillas' },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.status === 'ok' && response.data.length > 0) {
                    response.data.forEach(p => {
                        const tienda = getNombreTienda(p.NUM_ID_TIENDA);
                        const clase = getNombreClase(p.NUM_ID_CLASE);
                        selectPlantillas += `<option value="${p.NUM_ID_PLANTILLA}">P-${p.NUM_ID_PLANTILLA} - ${tienda} / ${clase}</option>`;
                    });
                }
            }
        });
        selectPlantillas += '</select>';

        const newRow = `
            <tr class="adding-row">
                <td>${selectPlantillas}</td>
                <td><span class="text-muted">Se asignará automáticamente</span></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_GRUPO" placeholder="Grupo"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_CAMPO" placeholder="Campo"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_NOMBRE_PLANTILLA" placeholder="Nombre"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_JUEGO" placeholder="Juego"></td>
                <td><input type="text" class="form-control form-control-sm" name="VCH_CODIGO" placeholder="Código"></td>
                <td class="text-center">
                    <select class="form-select form-select-sm" name="VCH_ESTADO">
                        <option value="1" selected>Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm" name="NUM_ORDEN" value="1" min="1"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-success btn-sm guardar-detalle">
                        <i class="mdi mdi-check"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancelar-edicion">
                        <i class="mdi mdi-close"></i> Cancelar
                    </button>
                </td>
            </tr>
        `;

        $('#tablaDetalles tbody').prepend(newRow);
        $('html, body').animate({ scrollTop: $('#tablaDetalles').offset().top - 100 }, 300);
    });

    // ========== GUARDAR DETALLE ========== 
    $(document).on('click', '.guardar-detalle', function() {
        const $row = $(this).closest('tr');

        const formData = {
            modulo_plantilla: 'registrar_detalle',
            NUM_ID_PLANTILLA: $row.find('select[name="NUM_ID_PLANTILLA"]').val(),
            VCH_GRUPO: $row.find('input[name="VCH_GRUPO"]').val(),
            VCH_CAMPO: $row.find('input[name="VCH_CAMPO"]').val(),
            VCH_NOMBRE_PLANTILLA: $row.find('input[name="VCH_NOMBRE_PLANTILLA"]').val(),
            VCH_DESCRIPCION: '',
            VCH_JUEGO: $row.find('input[name="VCH_JUEGO"]').val(),
            VCH_CODIGO: $row.find('input[name="VCH_CODIGO"]').val(),
            VCH_ESTADO: $row.find('select[name="VCH_ESTADO"]').val(),
            VCH_OBLIGATORIO: '0',  
            NUM_ORDEN: $row.find('input[name="NUM_ORDEN"]').val()
        };

        if (!formData.NUM_ID_PLANTILLA) {
            Swal.fire('Error', 'Debe seleccionar una plantilla', 'error');
            return;
        }

        console.log('📤 Enviando detalle:', formData);

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('📥 Respuesta:', response);
                if (response.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Detalle registrado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $row.remove();
                    cargarDetalles();
                } else {
                    Swal.fire('Error', response.msg || 'No se pudo registrar', 'error');
                }
            },
            error: function(xhr) {
                console.error(' Error guardando detalle:', xhr);
                console.error(' Respuesta completa:', xhr.responseText);
                Swal.fire('Error', 'Error de conexión al guardar', 'error');
            }
        });
    });

    // ========== EDITAR DETALLE ==========
    $(document).on('click', '.btn-editar-detalle', function() {
        if ($('.editing-row, .adding-row').length > 0) {
            Swal.fire('Atención', 'Complete o cancele la edición actual primero', 'warning');
            return;
        }

        const btn = $(this);
        const $row = btn.closest('tr');

        // Generar select de plantillas
        let selectPlantillas = '<select class="form-select form-select-sm" name="NUM_ID_PLANTILLA" required><option value="">-- Seleccione Plantilla --</option>';
        
        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: { modulo_plantilla: 'listar_plantillas' },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.status === 'ok' && response.data.length > 0) {
                    response.data.forEach(p => {
                        const selected = btn.data('idplantilla') == p.NUM_ID_PLANTILLA ? 'selected' : '';
                        const tienda = getNombreTienda(p.NUM_ID_TIENDA);
                        const clase = getNombreClase(p.NUM_ID_CLASE);
                        selectPlantillas += `<option value="${p.NUM_ID_PLANTILLA}" ${selected}>P-${p.NUM_ID_PLANTILLA} - ${tienda} / ${clase}</option>`;
                    });
                }
            }
        });
        selectPlantillas += '</select>';

        $row.addClass('editing-row');
        $row.html(`
            <td>${selectPlantillas}</td>
            <td><span class="text-muted">Automático</span></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_GRUPO" value="${btn.data('grupo')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_CAMPO" value="${btn.data('campo')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_NOMBRE_PLANTILLA" value="${btn.data('nombre')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_JUEGO" value="${btn.data('juego')}"></td>
            <td><input type="text" class="form-control form-control-sm" name="VCH_CODIGO" value="${btn.data('codigo')}"></td>
            <td class="text-center">
                <select class="form-select form-select-sm" name="VCH_ESTADO">
                    <option value="1" ${btn.data('estado') == 1 ? 'selected' : ''}>Activo</option>
                    <option value="0" ${btn.data('estado') == 0 ? 'selected' : ''}>Inactivo</option>
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm" name="NUM_ORDEN" value="${btn.data('orden')}" min="1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-success btn-sm actualizar-detalle" data-id="${btn.data('id')}">
                    <i class="mdi mdi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm cancelar-edicion">
                    <i class="mdi mdi-close"></i> Cancelar
                </button>
            </td>
        `);
    });

    // ========== ACTUALIZAR DETALLE ========== 
    $(document).on('click', '.actualizar-detalle', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');

        const formData = {
            modulo_plantilla: 'actualizar_detalle',
            NUM_ID_DET_PLANTILLA: id,
            NUM_ID_PLANTILLA: $row.find('select[name="NUM_ID_PLANTILLA"]').val(),
            VCH_GRUPO: $row.find('input[name="VCH_GRUPO"]').val(),
            VCH_CAMPO: $row.find('input[name="VCH_CAMPO"]').val(),
            VCH_NOMBRE_PLANTILLA: $row.find('input[name="VCH_NOMBRE_PLANTILLA"]').val(),
            VCH_DESCRIPCION: '',
            VCH_JUEGO: $row.find('input[name="VCH_JUEGO"]').val(),
            VCH_CODIGO: $row.find('input[name="VCH_CODIGO"]').val(),
            VCH_ESTADO: $row.find('select[name="VCH_ESTADO"]').val(),
            VCH_OBLIGATORIO: '0', 
            NUM_ORDEN: $row.find('input[name="NUM_ORDEN"]').val()
        };

        console.log('📤 Actualizando detalle:', formData);

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(' Respuesta:', response);
                if (response.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Detalle actualizado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    cargarDetalles();
                } else {
                    Swal.fire('Error', response.msg || 'No se pudo actualizar', 'error');
                }
            },
            error: function(xhr) {
                console.error(' Error actualizando detalle:', xhr);
                console.error(' Respuesta completa:', xhr.responseText);
                Swal.fire('Error', 'Error de conexión al actualizar', 'error');
            }
        });
    });

    // ========== CAMBIAR ESTADO DETALLE ==========
    $(document).on('change', '.toggle-estado-detalle', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const nuevoEstado = this.checked ? 1 : 0;

        $.ajax({
            url: APP_URL + 'app/ajax/plantillaAjax.php',
            method: 'POST',
            data: {
                modulo_plantilla: 'actualizar_estado_detalle',
                id: id,
                estado: nuevoEstado
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    const badge = checkbox.closest('td').find('.estado-badge');
                    badge.removeClass('activo inactivo').addClass(nuevoEstado == 1 ? 'activo' : 'inactivo');
                    badge.html(`<i class="mdi mdi-${nuevoEstado == 1 ? 'check-circle' : 'close-circle'}"></i> ${nuevoEstado == 1 ? 'Activo' : 'Inactivo'}`);
                } else {
                    checkbox.prop('checked', !nuevoEstado);
                    Swal.fire('Error', response.msg, 'error');
                }
            },
            error: function() {
                checkbox.prop('checked', !nuevoEstado);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });

    // ========== ELIMINAR DETALLE ==========
    $(document).on('click', '.btn-eliminar-detalle', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar detalle?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + 'app/ajax/plantillaAjax.php',
                    method: 'POST',
                    data: {
                        modulo_plantilla: 'eliminar_detalle',
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'ok') {
                            Swal.fire('Eliminado', 'Detalle eliminado correctamente', 'success');
                            cargarDetalles();
                        } else {
                            Swal.fire('Error', response.msg, 'error');
                        }
                    }
                });
            }
        });
    });

    // ========== FILTROS ==========
    $('#btnFilter').on('click', function() {
        cargarPlantillas();
        cargarDetalles();
    });

    $('#btnClear').on('click', function() {
        $('#f_clase, #f_tienda').val('0');
        cargarPlantillas();
        cargarDetalles();
    });

    // ========== CARGAR DATOS INICIALES ==========
    console.log('🔄 Cargando datos iniciales...');
    cargarPlantillas();
    cargarDetalles();


// ========== SELECCIONAR PLANTILLA Y FILTRAR DETALLES (VERSIÓN ESTABLE) ==========
$(document).on('click', '#tablaPlantillas tbody tr td:first-child', function(e) {
    // Solo responder a clicks en la primera columna (TIENDA)
    e.stopPropagation();
    
    const $row = $(this).closest('tr');
    
    // Ignorar si es una fila en edición
    if ($row.hasClass('editing-row') || $row.hasClass('adding-row')) {
        return;
    }

    // Remover selección previa
    $('#tablaPlantillas tbody tr').removeClass('selected-row');
    
    // Marcar esta fila como seleccionada
    $row.addClass('selected-row');

    // Buscar el botón de editar para obtener los datos
    const btnEditar = $row.find('.btn-editar-plantilla');
    
    if (btnEditar.length === 0) {
        console.warn('⚠️ No se encontró botón de editar en la fila');
        return;
    }

    const idPlantilla = btnEditar.data('id');
    const idTienda = btnEditar.data('tienda');
    const idClase = btnEditar.data('clase');

    console.log('📋 Plantilla seleccionada:', {
        id: idPlantilla,
        tienda: idTienda,
        clase: idClase
    });

    if (!idPlantilla) {
        Swal.fire('Error', 'No se pudo obtener el ID de la plantilla', 'error');
        return;
    }

    // Guardar ID
    plantillaSeleccionadaId = idPlantilla;

    // Toast rápido sin bloquear
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
    
    Toast.fire({
        icon: 'info',
        title: 'Filtrando detalles...'
    });

    // Cargar detalles sin mostrar loading modal
    $.ajax({
        url: APP_URL + 'app/ajax/plantillaAjax.php',
        method: 'POST',
        data: {
            modulo_plantilla: 'listar_detalles',
            id_plantilla: parseInt(idPlantilla),
            f_clase: parseInt(idClase) || 0,
            f_tienda: parseInt(idTienda) || 0
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'ok') {
                // ⭐ CLAVE: clear sin draw para evitar parpadeos
                tablaDetalles.clear();
                
                // Agregar filas
                response.data.forEach(row => {
                    const plantillaInfo = `P-${row.NUM_ID_PLANTILLA}`;
                    const nombreTienda = row.NOMBRE_TIENDA || getNombreTienda(row.NUM_ID_TIENDA) || 'N/A';
                    
                    tablaDetalles.row.add([
                        plantillaInfo,
                        nombreTienda,
                        row.VCH_GRUPO || '-',
                        row.VCH_CAMPO || '-',
                        row.VCH_NOMBRE_PLANTILLA || '-',
                        row.VCH_JUEGO || '-',
                        row.VCH_CODIGO || '-',
                        renderEstadoToggle(row.NUM_ID_DET_PLANTILLA, row.VCH_ESTADO, 'detalle'),
                        row.NUM_ORDEN || '1',
                        renderAccionesDetalle(row)
                    ]);
                });
                
                // ⭐ Un solo draw al final
                tablaDetalles.draw(false); // false = no resetear paginación
                
                // Scroll suave
                $('html, body').animate({
                    scrollTop: $('#tablaDetalles').offset().top - 100
                }, 300);
                
                // Notificación de éxito
                Toast.fire({
                    icon: 'success',
                    title: `${response.data.length} detalle(s)`,
                    text: `Plantilla P-${idPlantilla}`
                });
                
                console.log('✅ Detalles cargados:', response.data.length);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.msg || 'No se pudieron cargar detalles'
                });
            }
        },
        error: function(xhr) {
            console.error('❌ Error:', xhr);
            Toast.fire({
                icon: 'error',
                title: 'Error de conexión'
            });
        }
    });
});

// ========== INDICADOR VISUAL: Hover en primera columna ==========
$(document).on('mouseenter', '#tablaPlantillas tbody tr td:first-child', function() {
    if (!$(this).closest('tr').hasClass('editing-row') && !$(this).closest('tr').hasClass('adding-row')) {
        $(this).css('cursor', 'pointer');
        $(this).css('background', '#e0e7ff');
    }
});

$(document).on('mouseleave', '#tablaPlantillas tbody tr td:first-child', function() {
    if (!$(this).closest('tr').hasClass('selected-row')) {
        $(this).css('background', '');
    }
});

// ========== LIMPIAR SELECCIÓN ==========
$('#btnClear').off('click').on('click', function() {
    $('#tablaPlantillas tbody tr').removeClass('selected-row');
    plantillaSeleccionadaId = null;
    $('#f_clase, #f_tienda').val('0');
    cargarPlantillas();
    cargarDetalles();
});
});