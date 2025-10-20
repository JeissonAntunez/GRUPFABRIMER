$(document).ready(function () {
    console.log('✅ producto.js cargado');
    
    let tablaProductos;
    let clases = [];
    let tiendas = [];

    // Cargar datos iniciales
    cargarClases();
    cargarTiendas();
    cargarEstadisticas();
    inicializarTabla();
    cargarProductos();

    function cargarClases() {
        const dataClases = document.getElementById('dataClases');
        if (dataClases) {
            clases = JSON.parse(dataClases.textContent);
            console.log('📋 Clases cargadas:', clases.length);
        }
    }

    function cargarTiendas() {
        const dataTiendas = document.getElementById('dataTiendas');
        if (dataTiendas) {
            tiendas = JSON.parse(dataTiendas.textContent);
            console.log('🏪 Tiendas cargadas:', tiendas.length);
        }
    }

    function inicializarTabla() {
        tablaProductos = $('#tablaProductos').DataTable({
            language: {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible",
                "infoEmpty": "Mostrando 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros"
            },
            responsive: true,
            order: [[0, 'asc']],
            pageLength: 15,
            scrollX: true
        });
        console.log('✅ Tabla inicializada');
    }

    function cargarProductos(idClase = 0, idTienda = 0, busqueda = '') {
        console.log('🔍 Cargando productos...');
        
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
                    renderizarTabla(response.data);
                    $('#totalMostrado').text('(' + response.data.length + ')');
                } else {
                    console.error('❌ Error:', response);
                    alert('Error al cargar productos');
                }
            },
            error: function (xhr, status, error) {
                console.error('❌ Error AJAX:', xhr.responseText);
                alert('Error de conexión: ' + error);
            }
        });
    }

    function renderizarTabla(productos) {
        console.log('📊 Renderizando', productos.length, 'productos');
        tablaProductos.clear();

        productos.forEach(function (producto) {
            const fila = [
                producto.NUM_STOCK || 0,
                producto.NOMBRE_CLASE || '-',
                producto.VCH_NOMBRE || '-',
                producto.VCH_MARCA || '-',
                producto.VCH_MODELO || '-',
                producto.VCH_DESCRIPCION || '-',
                producto.VCH_CATEGORIA_PRIMARIA || '-',
                producto.VCH_PAIS_PRODUCCION || '-',
                producto.VCH_BASIC_COLOR || '-',
                producto.VCH_COLOR || '-',
                producto.VCH_SIZE || '-',
                producto.VCH_SKU_VENDEDOR || '-',
                producto.VCH_CODIGO_BARRAS || '-',
                producto.VCH_SKU_PADRE || '-',
                producto.NUM_QUANTITY_FALABELLA || 0,
                formatearPrecio(producto.NUM_PRICE_FALABELLA),
                formatearPrecio(producto.NUM_SALE_PRICE_FALABELLA),
                producto.FEC_SALE_START_DATE || '-',
                producto.FEC_SALE_END_DATE || '-',
                producto.VCH_FIT || '-',
                producto.VCH_COSTUME_GENRE || '-',
                producto.VCH_PANTS_TYPE || '-',
                producto.VCH_COMPOSITION || '-',
                producto.VCH_MATERIAL_VESTUARIO || '-',
                producto.VCH_CONDICION_PRODUCTO || '-',
                producto.VCH_GARANTIA_PRODUCTO || '-',
                producto.VCH_GARANTIA_VENDEDOR || '-',
                producto.VCH_CONTENIDO_PAQUETE || '-',
                producto.NUM_ANCHO_PAQUETE || '-',
                producto.NUM_LARGO_PAQUETE || '-',
                producto.NUM_ALTO_PAQUETE || '-',
                producto.NUM_PESO_PAQUETE || '-',
                '-', '-', '-', '-', '-', '-', '-', '-', // Imágenes
                producto.VCH_MONEDA || '-',
                producto.VCH_TIPO_PUBLI || '-',
                producto.VCH_FORM_ENV || '-',
                producto.VCH_COSTO_EN || '-',
                producto.VCH_RETIRO || '-',
                producto.VCH_PESO_PROD || '-',
                producto.VCH_LONG_PROD || '-',
                producto.VCH_ANCHO_PROD || '-',
                producto.VCH_ALTURA_PROD || '-',
                producto.VCH_TIPO_CUE || '-',
                producto.VCH_TIPO_PUN || '-',
                producto.VCH_TIPO_CIER || '-',
                producto.VCH_TIPO_GARANT || '-',
                producto.VCH_TABLA_TALLA || '-',
                producto.VCH_TAMANIO_PROD || '-',
                `<button class="btn btn-sm btn-danger btn-eliminar" data-id="${producto.NUM_ID_PRODUCTO}">
                    <i class="fas fa-trash"></i>
                </button>`
            ];

            tablaProductos.row.add(fila);
        });

        tablaProductos.draw();
        console.log('✅ Tabla renderizada');
    }

    // FUNCIONES AUXILIARES
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
        $.ajax({
            url: APP_URL + 'app/ajax/productoAjax.php',
            type: 'POST',
            data: { modulo_producto: 'obtener_plantilla_vacia' },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'ok') {
                    const csv = response.headers.join(',');
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'plantilla_vacia.csv';
                    a.click();
                }
            }
        });
    });

    $(document).on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');
        
        if (confirm('¿Eliminar este producto?')) {
            $.ajax({
                url: APP_URL + 'app/ajax/productoAjax.php',
                type: 'POST',
                data: {
                    modulo_producto: 'eliminar',
                    id: id
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'ok') {
                        alert('Producto eliminado');
                        cargarProductos();
                        cargarEstadisticas();
                    } else {
                        alert('Error: ' + response.msg);
                    }
                }
            });
        }
    });

    // ⭐ PLANTILLA VACÍA
$('#btnDescargarPlantillaVacia').click(function () {
    console.log('📥 Descargando plantilla vacía...');
    window.location.href = APP_URL + 'app/ajax/productoAjax.php?modulo_producto=obtener_plantilla_vacia';
});

// ⭐ PLANTILLA ESPECÍFICA
$('#btnDescargarPlantilla').click(function () {
    const idClase = $('#f_clase').val() || 0;
    const idTienda = $('#f_tienda').val() || 0;
    
    if (idClase == 0) {
        alert('⚠️ Debe seleccionar una Clase para generar la plantilla específica');
        $('#f_clase').focus();
        return;
    }
    
    console.log('📥 Descargando plantilla específica...', { idClase, idTienda });
    window.location.href = APP_URL + 'app/ajax/productoAjax.php?modulo_producto=obtener_plantilla_excel&id_clase=' + idClase + '&id_tienda=' + idTienda;
});
});
