<div class="container is-fluid mb-6">
    <h1 class="title">Gestión de Imágenes de Productos</h1>
    <h2 class="subtitle">Cargar imágenes por SKU padre</h2>
</div>


<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/imagen.css">
<div class="container pb-6 pt-6">

    <!-- TABS -->
    <div class="tabs is-centered is-boxed mb-6">
        <ul>
            <li class="is-active" data-tab="individual">
                <a>
                    <span class="icon"><i class="fas fa-image"></i></span>
                    <span>Carga Individual</span>
                </a>
            </li>
            <li data-tab="excel">
                <a>
                    <span class="icon"><i class="fas fa-file-excel"></i></span>
                    <span>Carga por Excel</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- PESTAÑA 1: CARGA INDIVIDUAL -->
    <div id="tab-individual" class="tab-content">
        <div class="columns">

            <!-- COLUMNA IZQUIERDA: FORMULARIO -->
            <div class="column is-6">
                <div class="box">
                    <h3 class="title is-4">Cargar Imagen Individual</h3>

                    <form id="formCargarImagen" enctype="multipart/form-data">

                        <!-- SKU Padre -->
                        <div class="field">
                            <label class="label">
                                SKU Padre
                                <span class="tag is-danger is-light ml-2">Obligatorio</span>
                            </label>
                            <div class="control has-icons-left">
                                <input
                                    type="text"
                                    class="input"
                                    id="skuPadre"
                                    name="VCH_SKU_PADRE"
                                    placeholder="Ej: SKU000044"
                                    required
                                    autocomplete="off">
                                <span class="icon is-left">
                                    <i class="fas fa-barcode"></i>
                                </span>
                            </div>
                            <p class="help">El SKU debe existir en la tabla de productos</p>
                        </div>

                        <!-- Info Producto -->
                        <div id="infoProducto" style="display:none;" class="notification is-info is-light mb-4">
                            <div class="columns is-mobile is-multiline">
                                <div class="column is-6">
                                    <p class="is-size-7"><strong>Nombre:</strong></p>
                                    <p id="nombreProducto" class="has-text-weight-semibold">-</p>
                                </div>
                                <div class="column is-6">
                                    <p class="is-size-7"><strong>Marca:</strong></p>
                                    <p id="marcaProducto" class="has-text-weight-semibold">-</p>
                                </div>
                                <div class="column is-6">
                                    <p class="is-size-7"><strong>Variantes:</strong></p>
                                    <span id="numVariantes" class="tag is-info">0</span>
                                </div>
                                <div class="column is-6">
                                    <p class="is-size-7"><strong>Imágenes:</strong></p>
                                    <span id="numImagenes" class="tag is-success">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Campo de Imagen -->
                        <div class="field">
                            <label class="label">
                                Campo de Imagen
                                <span class="tag is-danger is-light ml-2">Obligatorio</span>
                            </label>
                            <div class="control has-icons-left">
                                <div class="select is-fullwidth">
                                    <select id="campoImagen" name="CAMPO_IMAGEN" required>
                                        <option value="">Seleccionar campo...</option>
                                        <option value="VCH_IMAGEN_PRINCIPAL">Imagen Principal *</option>
                                        <option value="VCH_IMAGEN2">Imagen 2 *</option>
                                        <option value="VCH_IMAGEN3">Imagen 3</option>
                                        <option value="VCH_IMAGEN4">Imagen 4</option>
                                        <option value="VCH_IMAGEN5">Imagen 5</option>
                                        <option value="VCH_IMAGEN6">Imagen 6</option>
                                        <option value="VCH_IMAGEN7">Imagen 7</option>
                                        <option value="VCH_IMAGEN8">Imagen 8</option>
                                    </select>
                                </div>
                                <span class="icon is-left">
                                    <i class="fas fa-layer-group"></i>
                                </span>
                            </div>
                            <p class="help">
                                <span class="has-text-danger">*</span> Los campos Principal e Imagen 2 son obligatorios
                            </p>
                        </div>

                        <!-- Seleccionar Imagen -->
                        <div class="field">
                            <label class="label">
                                Seleccionar Imagen
                                <span class="tag is-danger is-light ml-2">Obligatorio</span>
                            </label>
                            <div class="control">
                                <div class="file has-name is-fullwidth is-info">
                                    <label class="file-label">
                                        <input
                                            class="file-input"
                                            type="file"
                                            id="archivoImagen"
                                            name="VCH_IMAGEN"
                                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                            required>
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="fas fa-upload"></i>
                                            </span>
                                            <span class="file-label">Seleccionar archivo</span>
                                        </span>
                                        <span class="file-name" id="nombreArchivo">
                                            Ningún archivo seleccionado
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <p class="help">
                                <i class="fas fa-info-circle"></i>
                                Formatos: JPG, PNG, GIF, WEBP | Máximo: 5MB
                            </p>
                        </div>

                        <!-- Preview -->
                        <div id="previewImagen" style="display:none;" class="mb-4">
                            <p class="has-text-weight-bold mb-2">
                                <i class="fas fa-eye"></i> Vista previa:
                            </p>
                            <figure class="image is-3by2">
                                <img id="imgPreview"
                                    src=""
                                    alt="Preview"
                                    style="object-fit: contain; border: 2px dashed #dbdbdb; border-radius: 6px; padding: 10px; background: #fafafa;">
                            </figure>
                        </div>

                        <!-- Botones -->
                        <div class="field is-grouped">
                            <div class="control">
                                <button type="submit" class="button is-success" id="btnCargarImagen">
                                    <span class="icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </span>
                                    <span>Cargar Imagen</span>
                                </button>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-light" id="btnLimpiar">
                                    <span class="icon">
                                        <i class="fas fa-eraser"></i>
                                    </span>
                                    <span>Limpiar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- COLUMNA DERECHA: GALERÍA -->
            <div class="column is-6">
                <div class="box">
                    <h4 class="title is-5">
                        <i class="fas fa-images"></i> Imágenes del Producto
                    </h4>

                    <div id="galeriaImagenes" style="display:none;">
                        <!-- Se carga dinámicamente -->
                    </div>

                    <div id="sinImagenes" class="has-text-centered py-6">
                        <p class="has-text-grey mb-4">
                            <i class="fas fa-image fa-4x"></i>
                        </p>
                        <p class="has-text-grey-dark has-text-weight-semibold">
                            Ingresa un SKU padre para ver sus imágenes
                        </p>
                        <p class="has-text-grey is-size-7 mt-2">
                            Las imágenes se mostrarán aquí una vez cargadas
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PESTAÑA 2: CARGA EXCEL -->
    <div id="tab-excel" class="tab-content" style="display:none;">
        <div class="box">
            <h3 class="title is-4">Cargar Imágenes desde Excel</h3>

            <div class="notification is-info is-light">
                <p><strong><i class="fas fa-info-circle"></i> Formato esperado:</strong></p>
                <p>La primera columna debe contener el SKU padre y las demás columnas los nombres de imágenes.</p>
            </div>

            <div class="notification is-warning is-light">
                <p><strong><i class="fas fa-exclamation-triangle"></i> Funcionalidad en desarrollo</strong></p>
                <p>Esta función estará disponible próximamente.</p>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script>
    const APP_URL = "<?php echo APP_URL; ?>";
    console.log('APP_URL:', APP_URL); // Para debugging
</script>

<!-- jQuery (asegúrate de que esté cargado) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script personalizado -->
<script src="<?php echo APP_URL; ?>app/views/js/imagen.js"></script>

<script>
    // Verificar que jQuery esté cargado
    $(document).ready(function() {
        console.log('jQuery versión:', $.fn.jquery);
        console.log('Formulario encontrado:', $('#formCargarImagen').length);
        console.log('Input file encontrado:', $('#archivoImagen').length);
    });
</script>