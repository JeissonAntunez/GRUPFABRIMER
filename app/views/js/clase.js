document.addEventListener("DOMContentLoaded", () => {


		// Agregar nueva fila para crear clase
		document.getElementById("btnAddClase").addEventListener("click", () => {
			// Verificar si ya hay una fila en modo edición
			if (document.querySelector('.editing-row, .adding-row')) {
				alert('Complete la edición actual antes de agregar una nueva clase');
				return;
			}

			const tbody = document.querySelector('#tablaClases tbody');
			const newRow = document.createElement('tr');
			newRow.className = 'adding-row';
			newRow.innerHTML = `
			<td>
				<input type="text" class="input is-small" name="VCH_NOMBRE" 
					placeholder="Nombre de la clase *" required>
			</td>
			<td class="has-text-centered">
				<div class="select is-small">
					<select name="VCH_ESTADO">
						<option value="1">Activo</option>
						<option value="0">Inactivo</option>
					</select>
				</div>
			</td>
			<td class="has-text-centered">
				<button type="button" class="button is-success is-small is-rounded save-new-clase">
					<i class="fas fa-check"></i> Guardar
				</button>
				<button type="button" class="button is-light is-small is-rounded cancel-add">
					<i class="fas fa-times"></i> Cancelar
				</button>
			</td>
		`;
			tbody.insertBefore(newRow, tbody.firstChild);
		});

		// Guardar nueva clase
		document.addEventListener('click', (e) => {
			if (e.target.closest('.save-new-clase')) {
				const row = e.target.closest('tr');
				const formData = new FormData();
				let isValid = true;

				const inputNombre = row.querySelector('input[name="VCH_NOMBRE"]');
				const selectEstado = row.querySelector('select[name="VCH_ESTADO"]');

				const nombre = inputNombre.value.trim();
				const estado = selectEstado.value;

				if (!nombre) {
					inputNombre.classList.add('is-invalid');
					isValid = false;
				} else {
					inputNombre.classList.remove('is-invalid');
				}

				if (!isValid) {
					alert('Complete todos los campos obligatorios');
					return;
				}

				formData.append('modulo_clase', 'registrar');
				formData.append('VCH_NOMBRE', nombre);
				formData.append('VCH_ESTADO', estado);

				fetch(APP_URL + 'app/ajax/claseAjax.php', {
						method: 'POST',
						body: formData
					})
					.then(r => r.json())
					.then(d => {
						if (d.status === 'ok') {
							location.reload();
						} else {
							alert('Error: ' + d.msg);
						}
					});
			}
		});

		// Cancelar agregar
		document.addEventListener('click', (e) => {
			if (e.target.closest('.cancel-add')) {
				if (confirm('¿Desea cancelar la creación de la nueva clase?')) {
					e.target.closest('tr').remove();
				}
			}
		});

		// Editar clase
		document.addEventListener('click', (e) => {
			if (e.target.closest('.edit-clase')) {
				if (document.querySelector('.editing-row, .adding-row')) {
					alert('Complete la edición actual antes de editar otra clase');
					return;
				}

				const btn = e.target.closest('.edit-clase');
				const row = btn.closest('tr');
				const id = btn.dataset.id;
				const nombre = btn.dataset.nombre;
				const estado = btn.dataset.estado;

				row.className = 'editing-row';
				row.innerHTML = `
				<td>
					<input type="text" class="input is-small" name="VCH_NOMBRE" 
						value="${nombre}" required>
				</td>
				<td class="has-text-centered">
					<div class="select is-small">
						<select name="VCH_ESTADO">
							<option value="1" ${estado == 1 ? 'selected' : ''}>Activo</option>
							<option value="0" ${estado == 0 ? 'selected' : ''}>Inactivo</option>
						</select>
					</div>
				</td>
				<td class="has-text-centered">
					<button type="button" class="button is-success is-small is-rounded save-edit-clase" 
						data-id="${id}">
						<i class="fas fa-check"></i> Guardar
					</button>
					<button type="button" class="button is-light is-small is-rounded cancel-edit"
						data-id="${id}" data-nombre="${nombre}" data-estado="${estado}">
						<i class="fas fa-times"></i> Cancelar
					</button>
				</td>
			`;
			}
		});

		// Guardar edición
		document.addEventListener('click', (e) => {
			if (e.target.closest('.save-edit-clase')) {
				const btn = e.target.closest('.save-edit-clase');
				const row = btn.closest('tr');
				const id = btn.dataset.id;
				const formData = new FormData();
				let isValid = true;

				const inputNombre = row.querySelector('input[name="VCH_NOMBRE"]');
				const selectEstado = row.querySelector('select[name="VCH_ESTADO"]');

				const nombre = inputNombre.value.trim();
				const estado = selectEstado.value;

				if (!nombre) {
					inputNombre.classList.add('is-invalid');
					isValid = false;
				} else {
					inputNombre.classList.remove('is-invalid');
				}

				if (!isValid) {
					alert('Complete todos los campos obligatorios');
					return;
				}

				formData.append('modulo_clase', 'actualizar');
				formData.append('NUM_ID_CLASE', id);
				formData.append('VCH_NOMBRE', nombre);
				formData.append('VCH_ESTADO', estado);

				fetch(APP_URL + 'app/ajax/claseAjax.php', {
						method: 'POST',
						body: formData
					})
					.then(r => r.json())
					.then(d => {
						if (d.status === 'ok') {
							location.reload();
						} else {
							alert('Error: ' + d.msg);
						}
					});
			}
		});

		// Cancelar edición
		document.addEventListener('click', (e) => {
			if (e.target.closest('.cancel-edit')) {
				if (confirm('¿Desea cancelar los cambios?')) {
					const btn = e.target.closest('.cancel-edit');
					const row = btn.closest('tr');
					const id = btn.dataset.id;
					const nombre = btn.dataset.nombre;
					const estado = btn.dataset.estado;

					row.className = '';
					row.innerHTML = `
					<td>${nombre}</td>
					<td class="has-text-centered">
						<label class="switch">
							<input type="checkbox" class="toggle-estado" data-id="${id}" 
								${estado == 1 ? 'checked' : ''}>
							<span class="slider"></span>
						</label>
						<span class="estado-label ${estado == 1 ? 'activo' : 'inactivo'}">
							${estado == 1 ? 'Activo' : 'Inactivo'}
						</span>
					</td>
					<td class="has-text-centered">
						<button type="button" class="button is-warning is-small is-rounded edit-clase"
							data-id="${id}" data-nombre="${nombre}" data-estado="${estado}">
							<i class="fas fa-edit"></i> Editar
						</button>
						<button type="button" class="button is-danger is-small is-rounded delete-clase"
							data-id="${id}">
							<i class="fas fa-trash"></i> Eliminar
						</button>
					</td>
				`;
				}
			}
		});

		// Eliminar clase
		document.addEventListener('click', (e) => {
			if (e.target.closest('.delete-clase')) {
				if (confirm('¿Está seguro de eliminar esta clase?')) {
					const btn = e.target.closest('.delete-clase');
					const id = btn.dataset.id;
					const formData = new FormData();

					formData.append('modulo_clase', 'eliminar');
					formData.append('id', id);

					fetch(APP_URL + 'app/ajax/claseAjax.php', {
							method: 'POST',
							body: formData
						})
						.then(r => r.json())
						.then(d => {
							if (d.status === 'ok') {
								btn.closest('tr').remove();
								const totalElement = document.getElementById('totalClases');
								const currentTotal = parseInt(totalElement.textContent);
								totalElement.textContent = currentTotal - 1;
							} else {
								alert('Error: ' + d.msg);
							}
						});
				}
			}
		});

		// Toggle estado
		document.addEventListener('change', (e) => {
			if (e.target.classList.contains('toggle-estado')) {
				const chk = e.target;
				const id = chk.dataset.id;
				const estado = chk.checked ? 1 : 0;
				const formData = new FormData();

				formData.append('modulo_clase', 'actualizar_estado');
				formData.append('id', id);
				formData.append('estado', estado);

				fetch(APP_URL + 'app/ajax/claseAjax.php', {
						method: 'POST',
						body: formData
					})
					.then(r => r.json())
					.then(d => {
						if (d.status === 'ok') {
							const label = chk.closest('td').querySelector('.estado-label');
							label.textContent = estado ? 'Activo' : 'Inactivo';
							label.className = 'estado-label ' + (estado ? 'activo' : 'inactivo');
						} else {
							chk.checked = !estado;
							alert('Error al actualizar estado');
						}
					});
			}
		});
	});
