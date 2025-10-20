<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gestión de Clases</title>


	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	
	<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

	
	<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/clase.css">
</head>

<body class="bg-light">

	<!-- Header -->
	<div class="page-header">
		<div class="container">
			<h1 class="display-4 mb-2">Clases</h1>
			<p class="lead mb-0">Gestión de Clases</p>
		</div>
	</div>

	<div class="container pb-5">

		<!-- Botón para agregar clase -->
		<div class="row mb-4">
			<div class="col-12 text-center">
				<button type="button" class="btn btn-success btn-lg rounded-pill" id="btnAddClase">
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
		<div class="alert alert-info text-center" role="alert">
			<strong>Total de Clases:</strong> <span id="totalClases" class="badge bg-primary fs-5"><?php echo $totalClases; ?></span>
		</div>

		<!-- Card con tabla -->
		<div class="card card-custom">
			<div class="card-header card-header-custom">
				<h5 class="mb-0"><i class="fas fa-list"></i> Listado de Clases</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover align-middle" id="tablaClases">
						<thead class="table-dark">
							<tr>
								<th class="text-center">Nombre</th>
								<th class="text-center">Estado</th>
								<th class="text-center acciones">Acciones</th>
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
											<button type="button" class="btn btn-warning btn-sm rounded-pill edit-clase"
												data-id="<?php echo $clase['NUM_ID_CLASE']; ?>"
												data-nombre="<?php echo htmlspecialchars($clase['VCH_NOMBRE']); ?>"
												data-estado="<?php echo $clase['VCH_ESTADO']; ?>">
												<i class="fas fa-edit"></i> Editar
											</button>
											<button type="button" class="btn btn-danger btn-sm rounded-pill delete-clase"
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
									<td colspan="3" class="text-center text-muted py-4">
										<i class="fas fa-inbox fa-2x mb-2 d-block"></i>
										No hay clases registradas
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

	<!-- Bootstrap 5 -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<!-- DataTables -->
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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