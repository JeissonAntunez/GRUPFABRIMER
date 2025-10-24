<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gestión de Clases</title>

	<!-- DataTables CSS -->
	<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

	<!-- CSS Personalizado -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/clase1.css">
</head>

<body>

	<!-- Header -->
	<div class="page-header">
		<div class="container">
			<h1>Gestión de Clases</h1>
			<p>Sistema completo de administración de clases</p>
		</div>
	</div>

	<div class="container">

		<!-- Botón para agregar clase -->
		<div class="row">
			<div class="col text-center">
				<button type="button" class="btn btn-success" id="btnAddClase">
					<i class="fas fa-plus"></i> Agregar Clase
				</button>
			</div>
		</div>

		<?php

		use app\controllers\claseController;

		$insClase = new claseController();
		$clases = $insClase->listarClasesControlador();
		$totalClases = $clases->rowCount();
		?>

		<!-- Total de clases -->
		<div class="stats-card">
			<p>Total de Clases: <strong id="totalClases"><?php echo $totalClases; ?></strong></p>
		</div>

		<!-- Card con tabla -->
		<div class="card">
			<div class="card-header">
				<h5><i class="fas fa-list"></i> Listado de Clases</h5>
			</div>
			<div class="card-body">
				<table id="tablaClases">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Estado</th>
							<th class="acciones">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ($totalClases > 0) {
							while ($clase = $clases->fetch()) {
						?>
								<tr data-id="<?php echo $clase['NUM_ID_CLASE']; ?>">
									<td><?php echo htmlspecialchars($clase['VCH_NOMBRE']); ?></td>
									<td class="text-center">
										<label class="switch">
											<input type="checkbox" class="toggle-estado"
												data-id="<?php echo $clase['NUM_ID_CLASE']; ?>"
												<?php echo $clase['VCH_ESTADO'] ? 'checked' : ''; ?>>
											<span class="slider"></span>
										</label>
										<span class="estado-label <?php echo $clase['VCH_ESTADO'] ? 'activo' : 'inactivo'; ?>">
											<?php echo $clase['VCH_ESTADO'] ? 'Activo' : 'Inactivo'; ?>
										</span>
									</td>
									<td class="text-center acciones">
										<button type="button" class="btn btn-warning edit-clase"
											data-id="<?php echo $clase['NUM_ID_CLASE']; ?>"
											data-nombre="<?php echo htmlspecialchars($clase['VCH_NOMBRE']); ?>"
											data-estado="<?php echo $clase['VCH_ESTADO']; ?>">
											<i class="fas fa-edit"></i> Editar
										</button>
										<button type="button" class="btn btn-danger delete-clase"
											data-id="<?php echo $clase['NUM_ID_CLASE']; ?>">
											<i class="fas fa-trash"></i> Eliminar
										</button>
									</td>
								</tr>
							<?php
							}
						} else {
							?>
							<tr>
								<td colspan="3" class="empty-state">
									<i class="fas fa-inbox"></i>
									<p>No hay clases registradas</p>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

	<!-- DataTables -->
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

	<!-- SweetAlert2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		const APP_URL = "<?php echo APP_URL; ?>";

		// Inicializar DataTables
		$(document).ready(function() {
			$('#tablaClases').DataTable({
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
				},
				pageLength: 10,
				order: [
					[0, 'asc']
				],
				columnDefs: [{
					orderable: false,
					targets: [2]
				}]
			});
		});
	</script>

	<script src="<?php echo APP_URL; ?>app/views/js/clase.js"></script>

</body>

</html>