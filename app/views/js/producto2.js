$(document).ready(function () {
    console.log('✅ producto.js cargado');
    
    let tablaProductos = null;
    let clases = [];
    let tiendas = [];

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
                        crearTablaDinamica(response.headers, response.data);
                    } else {
                        console.log('📋 Usando tabla por defecto');
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
            html += `<td><button class="btn btn-sm btn-danger btn-eliminar" data-id="${prod.NUM_ID_PRODUCTO}">
                <i class="fas fa-trash"></i>
            </button></td></tr>`;
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
            <th>Stock</th><th>Clase</th><th>Nombre</th><th>Marca</th>
            <th>Modelo</th><th>Precio</th><th>Acciones</th>
        </tr></thead><tbody>`;
        
        productos.forEach(prod => {
            html += `<tr>
                <td>${prod.NUM_STOCK || 0}</td>
                <td>${prod.NOMBRE_CLASE || '-'}</td>
                <td>${prod.VCH_NOMBRE || '-'}</td>
                <td>${prod.VCH_MARCA || '-'}</td>
                <td>${prod.VCH_MODELO || '-'}</td>
                <td>${formatearPrecio(prod.NUM_PRICE_FALABELLA)}</td>
                <td><button class="btn btn-sm btn-danger btn-eliminar" data-id="${prod.NUM_ID_PRODUCTO}">
                    <i class="fas fa-trash"></i>
                </button></td>
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