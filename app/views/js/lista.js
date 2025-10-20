$(document).ready(function() {

    // ========== INICIALIZAR DATATABLES ==========
    const table = $('#tablaListas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [
            { targets: [5], orderable: false } // Columna de acciones no ordenable
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


    // ========== ABRIR MODAL EDITAR ==========
    $(document).on('click', '.btn-editar', function() {
        const btn = $(this);
        
        $('#updateId').val(btn.data('id'));
        $('#updateTienda').val(btn.data('tienda'));
        $('#updateJuego').val(btn.data('juego'));
        $('#updateCodigo').val(btn.data('codigo'));
        $('#updateDescripcion').val(btn.data('descripcion'));
        $('#updateEstado').val(btn.data('estado'));
        
        $('#modalActualizar').modal('show');
    });


    // ========== ACTUALIZAR LISTA ==========
    $('#btnGuardarActualizar').on('click', function(e) {
        e.preventDefault();

        const form = $('#formActualizar')[0];
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
                console.error('Error:', error);
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
                            // Actualizar data attribute
                            checkbox.data('estado', nuevoEstado);
                            
                            // Actualizar label
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
                            // Revertir el checkbox
                            checkbox.prop('checked', estadoActual == 1);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg || 'No se pudo cambiar el estado'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Revertir el checkbox
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
                // Revertir el checkbox si canceló
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
                                // Eliminar fila de DataTable
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