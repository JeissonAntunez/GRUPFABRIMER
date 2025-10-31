$(document).ready(function () {
    console.log('✅ producto.js cargado');
    
    let tablaProductos = null;
    let clases = [];
    let tiendas = [];
    let headersActuales = []; // Guardamos los headers para el modal

    cargarClases();
    cargarTiendas();
    cargarEstadisticas();
    cargarProductos();

    function cargarClases() {
        const dataClases = document.getElementById('dataClases');
        if (dataClases) {
            clases = JSON.parse(dataClases.textContent);
            console.log('📋 Clases:', clases.length);
        }
    }

    function cargarTiendas() {
        const dataTiendas = document.getElementById('dataTiendas');
        if (dataTiendas) {
            tiendas = JSON.parse(dataTiendas.textContent);
            console.log('🏪 Tiendas:', tiendas.length);
        }
    }

    function cargarProductos(idClase = 0, idTienda = 0, busqueda = '') {
        console.log('🔍 Cargando...', { idClase, idTienda, busqueda });
        
        $.ajax({
            url: APP_URL + 'app/ajax/productoAjax.php',
            type: 'POST',
            data: {
                modulo_producto: 'listar',
                id_clase: idClase,
                id_tienda: idTienda,
                busqueda: busqueda
            },
            dataType: 'json',
            success: function (response) {
                console.log('✅ Respuesta:', response);
                
                if (response.status === 'ok') {
                    // Destruir tabla anterior
                    if (tablaProductos) {
                        tablaProductos.destroy();
                    }

                    if (response.tiene_plantilla) {
                        console.log('📋 Usando plantilla con', response.headers.length, 'columnas');
                        headersActuales = response.headers;
                        crearTablaDinamica(response.headers, response.data);
                    } else {
                        console.log('📋 Usando tabla por defecto');
                        headersActuales = [];
                        crearTablaEstandar(response.data);
                    }
                    
                    $('#totalMostrado').text('(' + response.data.length + ')');
                } else {
                    alert('Error: ' + (response.msg || 'Error al cargar'));
                }
            },
            error: function (xhr) {
                console.error('❌ Error:', xhr.responseText);
                alert('Error de conexión');
            }
        });
    }

    function crearTablaDinamica(headers, productos) {
        console.log('🔨 Creando tabla dinámica...');
        
        // Construir HTML completo
        let html = '<thead><tr>';
        
        // Encabezados
        headers.forEach(h => {
            html += `<th>${h.nombre}</th>`;
        });
        html += '<th>Acciones</th></tr></thead><tbody>';
        
        // Filas
        productos.forEach(prod => {
            html += '<tr>';
            headers.forEach(h => {
                let valor = prod[h.campo] || '-';
                
                // Formatear precios
                if (h.campo && h.campo.includes('PRICE')) {
                    valor = formatearPrecio(valor);
                }
                
                html += `<td>${valor}</td>`;
            });
            html += `<td>
                <button class="btn btn-sm btn-warning btn-editar me-1" data-producto='${JSON.stringify(prod).replace(/'/g, "&apos;")}'>
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-eliminar" data-id="${prod.NUM_ID_PRODUCTO}">
                    <i class="fas fa-trash"></i>
                </button>
            </td></tr>`;
        });
        
        html += '</tbody>';
        
        // Insertar
        $('#tablaProductos').html(html);
        
        // Inicializar DataTable
        tablaProductos = $('#tablaProductos').DataTable({
            language: {
                "search": "Buscar:",
                "lengthMenu": "Mostrar _MENU_ registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            pageLength: 15,
            scrollX: true
        });
        
        console.log('✅ Tabla creada');
    }

    function crearTablaEstandar(productos) {
        console.log('🔨 Creando tabla estándar...');
        
        let html = `<thead><tr>
            <th >Stock</th>
            <th>Clase</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Precio</th>
            <th>Sku Vendedor</th>
            <th>Sku Padre</th>
            <th>Acciones</th>
        </tr></thead><tbody>`;
        
        productos.forEach(prod => {
            html += `<tr>
                <td>${prod.NUM_STOCK || 0}</td>
                <td>${prod.NOMBRE_CLASE || '-'}</td>
                <td>${prod.VCH_NOMBRE || '-'}</td>
                <td>${prod.VCH_MARCA || '-'}</td>
                <td>${prod.VCH_MODELO || '-'}</td>
                <td>${formatearPrecio(prod.NUM_PRICE_FALABELLA)}</td>
                <td>${prod.VCH_SKU_VENDEDOR || '-'}</td>
                <td>${prod.VCH_SKU_PADRE || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-warning btn-editar me-1" data-producto='${JSON.stringify(prod).replace(/'/g, "&apos;")}'>
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-eliminar" data-id="${prod.NUM_ID_PRODUCTO}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
        });
        
        html += '</tbody>';
        $('#tablaProductos').html(html);
        
        tablaProductos = $('#tablaProductos').DataTable({
            language: {
                "search": "Buscar:",
                "lengthMenu": "Mostrar _MENU_ registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_"
            },
            pageLength: 15,
            scrollX: true
        });
    }

    function formatearPrecio(precio) {
        if (!precio || precio == 0) return '$0.00';
        return '$' + parseFloat(precio).toFixed(2);
    }

    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + 'app/ajax/productoAjax.php',
            type: 'POST',
            data: { modulo_producto: 'estadisticas' },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'ok') {
                    $('#totalProductos').text(response.data.total_productos || 0);
                    $('#conStock').text(response.data.con_stock || 0);
                    $('#conOferta').text(response.data.con_oferta || 0);
                    $('#precioPromedio').text(formatearPrecio(response.data.precio_promedio));
                }
            }
        });
    }

    // FUNCIÓN PARA ABRIR MODAL DE EDICIÓN
    function abrirModalEditar(producto) {
        console.log('📝 Editando producto:', producto);
        
        let html = '<form id="formEditarProducto">';
        html += `<input type="hidden" name="id_producto" value="${producto.NUM_ID_PRODUCTO}">`;
        
        // Si hay headers dinámicos, usarlos
        if (headersActuales.length > 0) {
            headersActuales.forEach(h => {
                // Omitir el ID principal
                if (h.campo === 'NUM_ID_PRODUCTO') return;
                
                let valor = producto[h.campo] || '';
                
                html += `<div class="mb-3">
                    <label class="form-label">${h.nombre}</label>
                    <input type="text" class="form-control" name="${h.campo}" value="${valor}">
                </div>`;
            });
        } else {
            // Campos estándar
            const campos = [
                { nombre: 'Stock', campo: 'NUM_STOCK', tipo: 'number' },
                { nombre: 'Nombre', campo: 'VCH_NOMBRE', tipo: 'text' },
                { nombre: 'Marca', campo: 'VCH_MARCA', tipo: 'text' },
                { nombre: 'Modelo', campo: 'VCH_MODELO', tipo: 'text' },
                { nombre: 'Precio', campo: 'NUM_PRICE_FALABELLA', tipo: 'number' }
            ];
            
            campos.forEach(c => {
                let valor = producto[c.campo] || '';
                html += `<div class="mb-3">
                    <label class="form-label">${c.nombre}</label>
                    <input type="${c.tipo}" class="form-control" name="${c.campo}" value="${valor}" step="0.01">
                </div>`;
            });
        }
        
        html += '</form>';
        
        $('#modalEditarBody').html(html);
        $('#modalEditar').modal('show');
    }

    // EVENTOS
    $('#btnFiltrar').click(function () {
        const idClase = $('#f_clase').val() || 0;
        const idTienda = $('#f_tienda').val() || 0;
        const busqueda = $('#f_busqueda').val() || '';
        cargarProductos(idClase, idTienda, busqueda);
    });

    $('#btnLimpiar').click(function () {
        $('#f_clase').val('0');
        $('#f_tienda').val('0');
        $('#f_busqueda').val('');
        cargarProductos();
    });

    $('#btnDescargarPlantillaVacia').click(function () {
        window.location.href = APP_URL + 'app/ajax/productoAjax.php?modulo_producto=obtener_plantilla_vacia';
    });

    $('#btnDescargarPlantilla').click(function () {
        const idClase = $('#f_clase').val() || 0;
        
        if (idClase == 0) {
            alert('⚠️ Debe seleccionar una Clase');
            return;
        }
        
        const idTienda = $('#f_tienda').val() || 0;
        window.location.href = APP_URL + 'app/ajax/productoAjax.php?modulo_producto=obtener_plantilla_excel&id_clase=' + idClase + '&id_tienda=' + idTienda;
    });

    $('#btnProcesarCSV').click(function () {
        const archivo = $('#archivoCSV')[0].files[0];
        
        if (!archivo) {
            alert('⚠️ Seleccione un archivo');
            return;
        }
        
        const formData = new FormData();
        formData.append('archivo_csv', archivo);
        formData.append('modulo_producto', 'importar_csv');
        
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: APP_URL + 'app/ajax/productoAjax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'ok') {
                    alert('✅ Importación exitosa!');
                    $('#modalImportar').modal('hide');
                    cargarProductos();
                    cargarEstadisticas();
                } else {
                    alert('❌ Error: ' + response.msg);
                }
            },
            complete: function () {
                $('#btnProcesarCSV').prop('disabled', false).html('<i class="fas fa-upload"></i> Importar');
            }
        });
    });

    // EVENTO BOTÓN EDITAR
    $(document).on('click', '.btn-editar', function () {
        const producto = JSON.parse($(this).attr('data-producto'));
        abrirModalEditar(producto);
    });

    // EVENTO GUARDAR EDICIÓN
    $(document).on('click', '#btnGuardarEdicion', function () {
        const formData = $('#formEditarProducto').serialize();
        
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + 'app/ajax/productoAjax.php',
            type: 'POST',
            data: formData + '&modulo_producto=actualizar',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'ok') {
                    alert('✅ Producto actualizado');
                    $('#modalEditar').modal('hide');
                    const idClase = $('#f_clase').val() || 0;
                    const idTienda = $('#f_tienda').val() || 0;
                    cargarProductos(idClase, idTienda);
                    cargarEstadisticas();
                } else {
                    alert('❌ Error: ' + response.msg);
                }
            },
            error: function () {
                alert('❌ Error de conexión');
            },
            complete: function () {
                $('#btnGuardarEdicion').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    $(document).on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');
        
        if (confirm('¿Eliminar producto?')) {
            $.ajax({
                url: APP_URL + 'app/ajax/productoAjax.php',
                type: 'POST',
                data: { modulo_producto: 'eliminar', id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'ok') {
                        alert('Producto eliminado');
                        const idClase = $('#f_clase').val() || 0;
                        const idTienda = $('#f_tienda').val() || 0;
                        cargarProductos(idClase, idTienda);
                        cargarEstadisticas();
                    }
                }
            });
        }
    });

    // ========== NUEVO PRODUCTO INDIVIDUAL ==========
$('#btnNuevoProducto').click(function() {
    // Resetear modal
    $('#claseProducto').val('');
    $('#tiendaProducto').val('0');
    $('#formNuevoProducto').hide();
    $('#btnGuardarProducto').hide();
    $('#alertaSeleccionClase').show();
    $('#camposProducto').empty();
    
    // Cargar clases en el select
    $('#claseProducto').empty().append('<option value="">-- Seleccione una Clase --</option>');
    clases.forEach(c => {
        $('#claseProducto').append(`<option value="${c.id}">${c.nombre}</option>`);
    });
    
    // Cargar tiendas en el select
    $('#tiendaProducto').empty().append('<option value="0">-- Sin Tienda Específica --</option>');
    tiendas.forEach(t => {
        $('#tiendaProducto').append(`<option value="${t.id}">${t.nombre}</option>`);
    });
    
    // Mostrar modal
    $('#modalNuevoProducto').modal('show');
});

// Cuando se selecciona una clase, cargar campos
$('#claseProducto, #tiendaProducto').change(function() {
    const idClase = $('#claseProducto').val();
    const idTienda = $('#tiendaProducto').val() || 0;
    
    if (!idClase || idClase == '') {
        $('#formNuevoProducto').hide();
        $('#btnGuardarProducto').hide();
        $('#alertaSeleccionClase').show();
        return;
    }
    
    // Mostrar loading
    $('#alertaSeleccionClase').hide();
    $('#formNuevoProducto').hide();
    $('#loadingCampos').show();
    
    $.ajax({
        url: APP_URL + 'app/ajax/productoAjax.php',
        type: 'POST',
        data: {
            modulo_producto: 'obtener_campos_plantilla',
            id_clase: idClase,
            id_tienda: idTienda
        },
        dataType: 'json',
        success: function(response) {
            $('#loadingCampos').hide();
            
            if (response.status === 'ok') {
                generarFormularioDinamico(response.campos, idClase);
                $('#formNuevoProducto').show();
                $('#btnGuardarProducto').show();
            } else {
                alert('❌ ' + response.msg);
                $('#alertaSeleccionClase').text(response.msg).show();
            }
        },
        error: function(xhr) {
            $('#loadingCampos').hide();
            console.error('Error:', xhr);
            alert('Error al cargar campos de plantilla');
            $('#alertaSeleccionClase').text('Error al cargar campos').show();
        }
    });
});

// Generar formulario dinámico

// Generar formulario dinámico con soporte para SELECT
function generarFormularioDinamico(campos, idClase) {
    const $container = $('#camposProducto');
    $container.empty();
    
    // Agregar campo oculto para ID de clase
    $container.append(`<input type="hidden" name="NUM_ID_CLASE" value="${idClase}">`);
    
    console.log(' Generando', campos.length, 'campos únicos');
    
    campos.forEach(campo => {
        const esObligatorio = campo.obligatorio == 1;
        const asterisco = esObligatorio ? '<span class="text-danger">*</span>' : '';
        const required = esObligatorio ? 'required' : '';
        
        let inputHTML = '';
        
        //  Si tiene opciones, crear SELECT
        if (campo.tipo_campo === 'select' && campo.opciones && campo.opciones.length > 0) {
            inputHTML = `<select class="form-select" name="${campo.columna}" ${required}>
                <option value="">-- Seleccione ${campo.encabezado} --</option>`;
            
            campo.opciones.forEach(opcion => {
                inputHTML += `<option value="${opcion.codigo}">${opcion.descripcion}</option>`;
            });
            
            inputHTML += `</select>
                <small class="text-muted">
                    <i class="fas fa-list"></i> ${campo.opciones.length} opciones disponibles
                </small>`;
        }
        // TEXTAREA
        else if (campo.tipo_campo === 'textarea') {
            inputHTML = `<textarea class="form-control" name="${campo.columna}" ${required} rows="3" placeholder="Ingrese ${campo.encabezado}"></textarea>`;
        }
        // NUMBER
        else if (campo.tipo_campo === 'number') {
            const step = campo.columna.includes('PRICE') ? '0.01' : '1';
            const placeholder = campo.columna.includes('PRICE') ? 'Ej: 99.99' : 'Ej: 10';
            inputHTML = `<input type="number" class="form-control" name="${campo.columna}" ${required} step="${step}" min="0" placeholder="${placeholder}">`;
        }
        // DATETIME
        else if (campo.tipo_campo === 'datetime-local') {
            inputHTML = `<input type="datetime-local" class="form-control" name="${campo.columna}" ${required}>`;
        }
        // TEXT (por defecto)
        else {
            inputHTML = `<input type="text" class="form-control" name="${campo.columna}" ${required} placeholder="Ingrese ${campo.encabezado}">`;
        }
        
        const html = `
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">
                    ${campo.encabezado} ${asterisco}
                </label>
                ${inputHTML}
            </div>
        `;
        
        $container.append(html);
    });
    
    console.log(' Formulario generado correctamente');
}

// Guardar producto individual
$('#btnGuardarProducto').click(function() {
    const $form = $('#formNuevoProducto');
    
    // Validar campos obligatorios
    if (!$form[0].checkValidity()) {
        $form[0].reportValidity();
        return;
    }
    
    const formData = $form.serialize() + '&modulo_producto=registrar';
    
    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: APP_URL + 'app/ajax/productoAjax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('📥 Respuesta guardar:', response);
            
            if (response.status === 'ok') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Producto registrado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                $('#modalNuevoProducto').modal('hide');
                
                // Recargar tabla
                const idClase = $('#f_clase').val() || 0;
                const idTienda = $('#f_tienda').val() || 0;
                cargarProductos(idClase, idTienda);
                cargarEstadisticas();
            } else {
                Swal.fire('Error', response.msg, 'error');
            }
        },
        error: function(xhr) {
            console.error(' Error:', xhr);
            console.error(' Respuesta:', xhr.responseText);
            Swal.fire('Error', 'Error de conexión al guardar', 'error');
        },
        complete: function() {
            $('#btnGuardarProducto').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Producto');
        }
    });
});
});