{{-- Cámara para la foto del expediente. La abre cualquier botón
     [data-foto-tomar] y la maneja public/js/foto-captura.js: enciende la
     cámara del aparato aquí mismo (getUserMedia) en vez de dejar que el
     navegador abra el explorador de archivos. Vive en el esqueleto porque el
     formulario del niño y el del acompañante la comparten. --}}
<div class="camara-velo" id="camara-velo" hidden>
    <div class="camara-ventana" role="dialog" aria-modal="true" aria-labelledby="camara-titulo">
        <div class="camara-cabecera">
            <strong id="camara-titulo"><i class="fas fa-camera" aria-hidden="true"></i> Tomar foto</strong>
            <button type="button" class="camara-cerrar" id="camara-cerrar" aria-label="Cerrar la cámara">&times;</button>
        </div>

        {{-- Marco cuadrado: lo que se ve encuadrado es exactamente lo que se
             guarda (la captura recorta el centro del cuadro de la cámara). --}}
        <div class="camara-marco">
            <video id="camara-video" playsinline autoplay muted></video>
            <img id="camara-previa" alt="Foto recién tomada" hidden>
            <p class="camara-encendiendo" id="camara-encendiendo">
                <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Encendiendo la cámara…
            </p>
        </div>

        <p class="camara-guia" id="camara-guia">Encuadra el rostro y toca «Capturar».</p>

        <div class="camara-acciones" id="camara-acciones-tomar">
            <button type="button" class="camara-btn camara-btn-secundario" id="camara-cambiar" hidden>
                <i class="fas fa-sync-alt" aria-hidden="true"></i> Cambiar cámara
            </button>
            <button type="button" class="camara-btn camara-btn-disparo" id="camara-disparar">
                <i class="fas fa-camera" aria-hidden="true"></i> Capturar
            </button>
        </div>

        <div class="camara-acciones" id="camara-acciones-revisar" hidden>
            <button type="button" class="camara-btn camara-btn-secundario" id="camara-repetir">
                <i class="fas fa-redo" aria-hidden="true"></i> Repetir
            </button>
            <button type="button" class="camara-btn camara-btn-usar" id="camara-usar">
                <i class="fas fa-check" aria-hidden="true"></i> Usar esta foto
            </button>
        </div>
    </div>
</div>
