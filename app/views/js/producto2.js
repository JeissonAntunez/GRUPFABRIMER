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
});