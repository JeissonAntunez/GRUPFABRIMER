
console.log('=== IMAGEN.JS CARGADO ===');

$(document).ready(function() {
    let modoEdicion = false;
    let campoEditando = null;

    console.log('jQuery inicializado');

    // ========== MANEJO DE TABS ==========
    $('.tabs li').on('click', function() {
        const tabName = $(this).data('tab');
        console.log('Tab clickeado:', tabName);
        
        $('.tabs li').removeClass('is-active');
        $(this).addClass('is-active');
        
        $('.tab-content').hide();
        $('#tab-' + tabName).show();
    });

    // ========== CAMBIO DE ARCHIVO ==========
    $('#archivoImagen').on('change', function(e) {
        console.log('=== ARCHIVO SELECCIONADO ===');
        
        const archivo = this.files[0];
        
        if (archivo) {
            console.log('Archivo:', archivo.name, archivo.size);
            
            $('#nombreArchivo').text(archivo.name);
            
            // Validar tamaño
            if (archivo.size > 5242880) { // 5MB
                Swal.fire({
                    icon: 'warning',
                    title: 'Archivo muy grande',
                    text: 'La imagen no debe superar 5MB',
                    confirmButtonColor: '#f14668'
                });
                $(this).val('');
                $('#nombreArchivo').text('Ningún archivo seleccionado');
                $('#previewImagen').hide();
                return;
            }
            
            // Validar tipo
            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!tiposPermitidos.includes(archivo.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato no válido',
                    text: 'Solo se permiten imágenes JPG, PNG, GIF o WEBP',
                    confirmButtonColor: '#f14668'
                });
                $(this).val('');
                $('#nombreArchivo').text('Ningún archivo seleccionado');
                $('#previewImagen').hide();
                return;
            }
            
            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imgPreview').attr('src', e.target.result);
                $('#previewImagen').fadeIn(300);
            };
            reader.readAsDataURL(archivo);
            
        } else {
            $('#nombreArchivo').text('Ningún archivo seleccionado');
            $('#previewImagen').hide();
        }
    });

    // ========== BUSCAR SKU PADRE ==========
    $('#skuPadre').on('blur', function() {
        const skuPadre = $(this).val().trim();
        console.log('SKU blur:', skuPadre);
        
        if (skuPadre === '') {
            $('#infoProducto').hide();
            $('#sinImagenes').show();
            $('#galeriaImagenes').html('').hide();
            return;
        }

        buscarProductoPorSku(skuPadre);
    });

    $('#skuPadre').on('keypress', function(e) {
        if (e.which === 13) { // Enter
            e.preventDefault();
            const skuPadre = $(this).val().trim();
            if (skuPadre !== '') {
                buscarProductoPorSku(skuPadre);
            }
        }
    });

    function buscarProductoPorSku(skuPadre) {
        console.log('Buscando SKU:', skuPadre);
        
        $.ajax({
            url: APP_URL + 'app/ajax/imagenAjax.php',
            method: 'POST',
            data: {
                modulo_imagen: 'buscar_sku',
                sku: skuPadre
            },
            dataType: 'json',
            beforeSend: function() {
                $('#infoProducto').hide();
            },
            success: function(response) {
                console.log('Respuesta SKU:', response);
                
                if (response.status === 'ok') {
                    $('#nombreProducto').text(response.nombre);
                    $('#marcaProducto').text(response.marca);
                    $('#numVariantes').text(response.variantes);
                    $('#numImagenes').text(response.imagenes);
                    $('#infoProducto').fadeIn(300);
                    $('#sinImagenes').hide();
                    
                    cargarGaleria(skuPadre);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'SKU no encontrado',
                        text: response.msg,
                        confirmButtonColor: '#3273dc'
                    });
                    
                    $('#infoProducto').hide();
                    $('#sinImagenes').show();
                    $('#galeriaImagenes').html('').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#f14668'
                });
            }
        });
    }

    // ========== CARGAR GALERÍA ==========
    function cargarGaleria(skuPadre) {
        console.log('Cargando galería para:', skuPadre);
        
        $.ajax({
            url: APP_URL + 'app/ajax/imagenAjax.php',
            method: 'GET',
            data: {
                modulo_imagen: 'listar',
                sku: skuPadre
            },
            dataType: 'json',
            success: function(response) {
                console.log('Galería:', response);
                
                if (response.status === 'ok' && response.imagenes && response.imagenes.length > 0) {
                    let galeriaHTML = '<div class="columns is-multiline">';
                    
                    response.imagenes.forEach(function(imagen) {
                        // Construir URL correcta
                        let urlImagen = imagen.ruta;
                        if (!urlImagen.startsWith('http')) {
                            urlImagen = APP_URL + urlImagen;
                        }
                        
                        galeriaHTML += `
                            <div class="column is-half">
                                <div class="box">
                                    <figure class="image is-3by2 mb-3">
                                        <img src="${urlImagen}" 
                                             alt="${imagen.nombre}" 
                                             style="object-fit: cover; border-radius: 6px;"
                                             onerror="this.style.border='2px dashed red'; this.alt='Error: imagen no encontrada';">
                                    </figure>
                                    <p class="has-text-weight-bold mb-1">
                                        <i class="fas fa-image"></i> ${imagen.nombre}
                                    </p>
                                    <p class="is-size-7 has-text-grey mb-3">
                                        <i class="fas fa-file"></i> ${imagen.archivo}
                                    </p>
                                    <div class="buttons">
                                        <button type="button" 
                                                class="button is-warning is-small btn-editar-imagen"
                                                data-campo="${imagen.campo}"
                                                data-nombre="${imagen.nombre}"
                                                data-sku="${skuPadre}">
                                            <span class="icon"><i class="fas fa-edit"></i></span>
                                            <span>Editar</span>
                                        </button>
                                        <button type="button" 
                                                class="button is-danger is-small btn-eliminar-imagen"
                                                data-campo="${imagen.campo}"
                                                data-nombre="${imagen.nombre}"
                                                data-sku="${skuPadre}">
                                            <span class="icon"><i class="fas fa-trash"></i></span>
                                            <span>Eliminar</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    galeriaHTML += '</div>';
                    $('#galeriaImagenes').html(galeriaHTML).fadeIn(300);
                    $('#sinImagenes').hide();
                    $('#numImagenes').text(response.imagenes.length);
                } else {
                    $('#galeriaImagenes').html('').hide();
                    $('#sinImagenes').show();
                    $('#numImagenes').text('0');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error galería:', error);
                $('#galeriaImagenes').html('').hide();
                $('#sinImagenes').show();
            }
        });
    }

    // ========== EDITAR IMAGEN ==========
    $(document).on('click', '.btn-editar-imagen', function() {
        const campo = $(this).data('campo');
        const nombre = $(this).data('nombre');
        const skuPadre = $(this).data('sku');

        console.log('Editar imagen:', { campo, nombre, skuPadre });

        // Activar modo edición
        modoEdicion = true;
        campoEditando = campo;

        // Precargar el campo
        $('#campoImagen').val(campo);
        $('#skuPadre').val(skuPadre);

        // Cambiar texto del botón
        $('#btnCargarImagen')
            .removeClass('is-success')
            .addClass('is-warning')
            .html('<span class="icon"><i class="fas fa-sync-alt"></i></span><span>Reemplazar Imagen</span>');

        // Mostrar notificación
        Swal.fire({
            icon: 'info',
            title: 'Modo Edición',
            html: `Vas a <strong>reemplazar</strong> la imagen de:<br><strong>${nombre}</strong><br><br>Selecciona una nueva imagen y presiona "Reemplazar Imagen"`,
            confirmButtonColor: '#3273dc',
            confirmButtonText: 'Entendido'
        });

        // Scroll al formulario
        $('html, body').animate({
            scrollTop: $('#formCargarImagen').offset().top - 100
        }, 500);
    });

    // ========== SUBMIT FORMULARIO (CARGAR O EDITAR) ==========
    $('#formCargarImagen').on('submit', function(e) {
        e.preventDefault();
        console.log('=== FORMULARIO ENVIADO ===');

        const skuPadre = $('#skuPadre').val().trim();
        const campoImagen = $('#campoImagen').val();
        const archivo = $('#archivoImagen')[0].files[0];

        console.log('Datos:', { skuPadre, campoImagen, archivo: archivo ? archivo.name : 'NO', modoEdicion });

        // Validaciones
        if (!skuPadre) {
            Swal.fire({
                icon: 'warning',
                title: 'SKU requerido',
                text: 'Ingresa el SKU padre',
                confirmButtonColor: '#f14668'
            });
            $('#skuPadre').focus();
            return;
        }

        if (!campoImagen) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'Selecciona el campo de imagen',
                confirmButtonColor: '#f14668'
            });
            $('#campoImagen').focus();
            return;
        }

        if (!archivo) {
            Swal.fire({
                icon: 'warning',
                title: 'Imagen requerida',
                text: 'Selecciona una imagen para ' + (modoEdicion ? 'reemplazar' : 'cargar'),
                confirmButtonColor: '#f14668'
            });
            $('#archivoImagen').focus();
            return;
        }

        // Crear FormData
        const formData = new FormData(this);
        formData.append('modulo_imagen', 'cargar');
        
        if (modoEdicion) {
            formData.append('modo_edicion', '1');
        }

        // Deshabilitar botón
        const btn = $('#btnCargarImagen');
        btn.addClass('is-loading').prop('disabled', true);

        $.ajax({
            url: APP_URL + 'app/ajax/imagenAjax.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                btn.removeClass('is-loading').prop('disabled', false);

                if (response.status === 'ok') {
                    const accion = modoEdicion ? 'reemplazada' : 'cargada';
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        html: `<p class="has-text-weight-bold">¡Imagen ${accion} exitosamente!</p><p class="is-size-7 mt-2">${response.detalles}</p>`,
                        confirmButtonColor: '#48c774',
                        timer: 4000,
                        showConfirmButton: true
                    }).then(() => {
                        // Limpiar formulario
                        limpiarFormulario();
                        
                        // Recargar galería
                        cargarGaleria(skuPadre);
                        buscarProductoPorSku(skuPadre);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg || 'No se pudo procesar la imagen',
                        confirmButtonColor: '#f14668'
                    });
                }
            },
            error: function(xhr, status, error) {
                btn.removeClass('is-loading').prop('disabled', false);
                console.error('Error AJAX:', error);
                console.error('Response:', xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    html: '<p>No se pudo conectar.</p><p class="is-size-7 mt-2">Revisa la consola (F12)</p>',
                    confirmButtonColor: '#f14668'
                });
            }
        });
    });

    // ========== ELIMINAR IMAGEN ==========
    $(document).on('click', '.btn-eliminar-imagen', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = $(this);
        const campo = btn.data('campo');
        const nombre = btn.data('nombre');
        const skuPadre = btn.data('sku');

        console.log('Eliminar:', { campo, nombre, skuPadre });

        Swal.fire({
            title: '¿Está seguro?',
            html: `Se eliminará la imagen:<br><strong>${nombre}</strong><br><br>Esta acción afectará a todas las variantes del SKU: <strong>${skuPadre}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#f14668',
            cancelButtonColor: '#b5b5b5'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.addClass('is-loading').prop('disabled', true);
                
                console.log('Enviando petición de eliminar...');
                
                $.ajax({
                    url: APP_URL + 'app/ajax/imagenAjax.php',
                    method: 'POST',
                    data: {
                        modulo_imagen: 'eliminar',
                        campo: campo,
                        sku_padre: skuPadre
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta eliminar:', response);
                        btn.removeClass('is-loading').prop('disabled', false);
                        
                        if (response.status === 'ok') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                cargarGaleria(skuPadre);
                                buscarProductoPorSku(skuPadre);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg,
                                confirmButtonColor: '#f14668'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        btn.removeClass('is-loading').prop('disabled', false);
                        console.error('Error al eliminar:', error);
                        console.error('Response:', xhr.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar la imagen',
                            confirmButtonColor: '#f14668'
                        });
                    }
                });
            }
        });
    });

    // ========== BOTÓN LIMPIAR ==========
    $('#btnLimpiar').on('click', function(e) {
        e.preventDefault();
        console.log('Limpiando formulario...');
        limpiarFormulario();
    });

    // ========== FUNCIÓN LIMPIAR FORMULARIO ==========
    function limpiarFormulario() {
        $('#formCargarImagen')[0].reset();
        $('#previewImagen').hide();
        $('#nombreArchivo').text('Ningún archivo seleccionado');
        
        // Resetear modo edición
        modoEdicion = false;
        campoEditando = null;
        
        // Resetear botón
        $('#btnCargarImagen')
            .removeClass('is-warning')
            .addClass('is-success')
            .html('<span class="icon"><i class="fas fa-cloud-upload-alt"></i></span><span>Cargar Imagen</span>');
    }

    console.log('=== IMAGEN.JS INICIALIZADO ===');
});

window.imagenJsLoaded = true;