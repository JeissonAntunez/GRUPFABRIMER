$(document).ready(function() {

    // ========== INICIALIZAR DATATABLES  ==========
    const table = $('#tablaListas').DataTable({
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [
            { targets: [5], orderable: false }
        ]
    });


    // ========== REGISTRAR LISTA ==========
    $('#btnGuardarRegistrar').on('click', function(e) {
        e.preventDefault();

        const form = $('#formRegistrar')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La lista se registró correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#modalRegistrar').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg || 'No se pudo registrar la lista'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud'
                });
            }
        });
    });


    // ========== ABRIR MODAL EDITAR (CON LOGS) ==========
    $(document).on('click', '.btn-editar', function() {
        const btn = $(this);
        
        const datos = {
            id: btn.data('id'),
            tienda: btn.data('tienda'),
            juego: btn.data('juego'),
            codigo: btn.data('codigo'),
            descripcion: btn.data('descripcion'),
            estado: btn.data('estado')
        };
        
        console.log('📝 Datos originales:', datos);
        console.log('🔤 Código (tipo):', typeof datos.codigo, '| Valor:', datos.codigo);
        console.log('🔤 Código (length):', datos.codigo ? datos.codigo.length : 0);
        
        // Limpiar el código de caracteres especiales si es necesario
        let codigoLimpio = String(datos.codigo).trim();
        console.log('✨ Código limpio:', codigoLimpio);
        
        $('#updateId').val(datos.id);
        $('#updateTienda').val(datos.tienda);
        $('#updateJuego').val(datos.juego);
        $('#updateCodigo').val(codigoLimpio); // Usar el código limpio
        $('#updateDescripcion').val(datos.descripcion);
        $('#updateEstado').val(datos.estado);
        
        // Verificar que se llenó correctamente
        console.log('✅ Valores en inputs:');
        console.log('  - ID:', $('#updateId').val());
        console.log('  - Tienda:', $('#updateTienda').val());
        console.log('  - Juego:', $('#updateJuego').val());
        console.log('  - Código:', $('#updateCodigo').val());
        console.log('  - Descripción:', $('#updateDescripcion').val());
        console.log('  - Estado:', $('#updateEstado').val());
        
        // Forzar revalidación
        $('#updateCodigo')[0].setCustomValidity('');
        
        $('#modalActualizar').modal('show');
    });


    // ========== ACTUALIZAR LISTA (CON MEJOR VALIDACIÓN) ==========
    $('#btnGuardarActualizar').on('click', function(e) {
        e.preventDefault();

        const form = $('#formActualizar')[0];
        
        // Log de validación
        console.log('🔍 Validando formulario...');
        console.log('  - Formulario válido?', form.checkValidity());
        
        // Validar manualmente cada campo
        const tienda = $('#updateTienda').val();
        const juego = $('#updateJuego').val();
        const codigo = $('#updateCodigo').val();
        
        console.log('📋 Valores a enviar:');
        console.log('  - Tienda:', tienda);
        console.log('  - Juego:', juego);
        console.log('  - Código:', codigo);
        
        if (!tienda || tienda === '') {
            Swal.fire('Error', 'Debe seleccionar una tienda', 'error');
            return;
        }
        
        if (!juego || juego.trim() === '') {
            Swal.fire('Error', 'Debe ingresar un juego', 'error');
            return;
        }
        
        if (!codigo || codigo.trim() === '') {
            Swal.fire('Error', 'Debe ingresar un código', 'error');
            return;
        }
        
        // Si todo está OK, enviar
        const formData = new FormData(form);
        
        // Log de lo que se envía
        console.log('📤 Enviando datos:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }

        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('📥 Respuesta:', response);
                
                if (response.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La lista se actualizó correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#modalActualizar').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg || 'No se pudo actualizar la lista'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error:', error);
                console.error('❌ Respuesta:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud'
                });
            }
        });
    });


    // ========== CAMBIAR ESTADO (TOGGLE SWITCH) ==========
    $(document).on('change', '.toggle-estado', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const estadoActual = checkbox.data('estado');
        const nuevoEstado = estadoActual == 1 ? 0 : 1;
        const row = checkbox.closest('tr');
        const label = row.find('.estado-label');

        Swal.fire({
            title: '¿Cambiar estado?',
            text: `¿Desea ${nuevoEstado == 1 ? 'activar' : 'desactivar'} esta lista?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('modulo_lista', 'actualizar_estado');
                formData.append('id', id);
                formData.append('estado', nuevoEstado);

                $.ajax({
                    url: APP_URL + 'app/ajax/listaAjax.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'ok') {
                            checkbox.data('estado', nuevoEstado);
                            label.removeClass('activo inactivo');
                            label.addClass(nuevoEstado == 1 ? 'activo' : 'inactivo');
                            label.text(nuevoEstado == 1 ? 'Activo' : 'Inactivo');
                            
                            Swal.fire({
                                icon: 'success',
                                title: '¡Actualizado!',
                                text: 'El estado se cambió correctamente',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            checkbox.prop('checked', estadoActual == 1);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg || 'No se pudo cambiar el estado'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        checkbox.prop('checked', estadoActual == 1);
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al procesar la solicitud'
                        });
                    }
                });
            } else {
                checkbox.prop('checked', estadoActual == 1);
            }
        });
    });


    // ========== ELIMINAR LISTA ==========
    $(document).on('click', '.btn-eliminar', function() {
        const btn = $(this);
        const id = btn.data('id');
        const row = btn.closest('tr');

        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción no se puede revertir',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('modulo_lista', 'eliminar');
                formData.append('id', id);

                $.ajax({
                    url: APP_URL + 'app/ajax/listaAjax.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'ok') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: 'La lista se eliminó correctamente',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                table.row(row).remove().draw();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg || 'No se pudo eliminar la lista'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al procesar la solicitud'
                        });
                    }
                });
            }
        });
    });


    // ========== LIMPIAR FORMULARIO AL CERRAR MODAL ==========
    $('#modalRegistrar').on('hidden.bs.modal', function() {
        $('#formRegistrar')[0].reset();
    });

    $('#modalActualizar').on('hidden.bs.modal', function() {
        $('#formActualizar')[0].reset();
    });

});