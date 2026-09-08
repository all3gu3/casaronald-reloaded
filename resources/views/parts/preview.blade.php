{{-- Vista previa de descargas, compartida entre la página del niño y el
     concentrado de expedientes. Muestra el carnet vertical (o un PDF en
     iframe) y, abajo, las descargas disponibles: imagen y/o PDF. --}}
<div class="modal" id="modalPreview" tabindex="-1" role="dialog" aria-label="Vista previa del documento">
    <div class="modal-dialog modal-lg preview-dialog" role="document">
        <div class="modal-content">
            {{-- Cierre rápido arriba a la derecha, igual que el tache del
                 encabezado del expediente: con el carnet a pantalla completa
                 el botón del pie queda fuera de vista. --}}
            <button type="button" class="preview-cerrar-x" data-dismiss="modal" aria-label="Cerrar vista previa">&times;</button>

            <div class="preview-cuerpo">
                <p id="preview-cargando" class="preview-cargando">
                    <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Generando el documento…
                </p>
                <p id="preview-aviso" class="preview-cargando" style="display: none;">
                    Este navegador no puede mostrar el PDF aquí: usa el botón <strong>Descargar PDF</strong>.
                </p>
                <iframe id="preview-marco" title="Vista previa del documento" style="display: none;"></iframe>
                <img id="preview-imagen" alt="Vista previa del carnet" style="display: none;">
            </div>
            <div class="preview-pie">
                <a id="preview-descarga-imagen" href="#" class="bitacora-ver perfil-boton-descarga" style="display: none;">
                    <i class="fas fa-image" aria-hidden="true"></i> Descargar imagen
                </a>
                <a id="preview-descarga-pdf" href="#" class="bitacora-ver perfil-boton-descarga" style="display: none;">
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> Descargar PDF
                </a>
                <button type="button" class="bitacora-ver preview-cerrar" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Vista previa de descargas. Las URLs de vista (sin ?download=1) sirven el
    // documento en línea y no quedan anotadas como descarga en la bitácora;
    // la anotación sale solo de los botones azules del pie.
    var previewPendiente = null;

    function abrirPreview(config) {
        previewPendiente = config;
        $('#preview-descarga-imagen').toggle(Boolean(config.descargaImagen)).attr('href', config.descargaImagen || '#');
        $('#preview-descarga-pdf').toggle(Boolean(config.descargaPdf)).attr('href', config.descargaPdf || '#');
        $('#preview-marco, #preview-imagen, #preview-aviso').hide();
        $('#preview-cargando').show();
        // backdrop true: oscurece el fondo y deja que Bootstrap cierre la
        // ventana al tocar fuera del documento.
        $('#modalPreview').modal({backdrop: true});
    }

    // Carnet: la vista previa es el mismo carnet vertical en imagen. El
    // parámetro t evita que el navegador reuse el carnet de otro momento.
    function abrirPreviewCarnet(ninoId) {
        abrirPreview({
            vista: '/fichas/' + ninoId + '?t=' + Date.now(),
            imagen: true,
            descargaImagen: '/fichas/' + ninoId + '?download=1',
            descargaPdf: '/fichas/' + ninoId + '?formato=pdf&download=1',
        });
    }

    // El visor de PDF del navegador no se inicializa dentro de un contenedor
    // con display:none: el src se asigna hasta que el modal ya está en
    // pantalla (shown.bs.modal). Mientras tanto, el aviso de carga.
    $('#modalPreview').on('shown.bs.modal', function () {
        if (previewPendiente === null) { return; }

        // Sin visor de PDF (p. ej. Chrome en Android) el iframe se queda en
        // blanco: mejor mandar directo al botón de descarga.
        if (! previewPendiente.imagen && navigator.pdfViewerEnabled === false) {
            $('#preview-cargando').hide();
            $('#preview-aviso').show();
            previewPendiente = null;
            return;
        }

        var visor = previewPendiente.imagen ? $('#preview-imagen') : $('#preview-marco');
        visor.attr('src', previewPendiente.vista);
        previewPendiente = null;
    });

    $('#preview-marco, #preview-imagen').on('load', function () {
        if ($('#modalPreview').is(':visible')) {
            $('#preview-cargando').hide();
            $(this).show();
        }
    });

    // El src vacío del cierre también dispara error en algunos navegadores:
    // solo cuenta si el modal sigue abierto con una URL real.
    $('#preview-imagen').on('error', function () {
        if ($('#modalPreview').is(':visible') && $(this).attr('src')) {
            $('#preview-cargando').hide();
            toastr.error('No fue posible generar el documento.', 'Error');
        }
    });

    // Al cerrar se vacía el visor para que el documento no siga cargando.
    $('#modalPreview').on('hidden.bs.modal', function () {
        previewPendiente = null;
        $('#preview-marco').attr('src', 'about:blank');
        $('#preview-imagen').attr('src', '');
    });
</script>
