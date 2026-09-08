{{-- Ventana flotante de escaneo: sustituye a la antigua página /escanear.
     Flujo: 1) elegir servicio → 2) cámara o código manual → 3) confirmación.
     Se abre desde cualquier botón con la clase js-abrir-escanear. --}}
<div class="escaneo-velo" id="escaneo-velo" hidden>
    <div class="escaneo-ventana" role="dialog" aria-modal="true" aria-labelledby="escaneo-titulo">
        <div class="escaneo-cabecera">
            <strong id="escaneo-titulo"><i class="fas fa-qrcode"></i>Registrar servicio</strong>
            <button type="button" class="escaneo-cerrar" id="escaneo-cerrar" aria-label="Cerrar ventana">&times;</button>
        </div>

        {{-- Paso 1: elegir el servicio. Cada botón lleva el color de su
             servicio en el gráfico de administración; abajo, entrada en
             verde y salida en rojo. --}}
        <div class="escaneo-paso" id="escaneo-paso-servicio">
            <p class="escaneo-guia">Elige el servicio a registrar:</p>
            <div class="escaneo-servicios">
                @foreach ([
                    'comedor' => ['Comedor', 'fas fa-drumstick-bite'],
                    'lavanderia' => ['Lavandería', 'fas fa-shirt'],
                    'escuela' => ['Escuelita', 'fa fa-graduation-cap'],
                    'transporte' => ['Transporte', 'fas fa-bus'],
                ] as $valor => [$etiqueta, $icono])
                    <button type="button" class="escaneo-servicio" data-accion="{{ $valor }}"
                            data-etiqueta="{{ $etiqueta }}" data-icono="{{ $icono }}">
                        <i class="{{ $icono }}"></i><span>{{ $etiqueta }}</span>
                    </button>
                @endforeach
            </div>
            <div class="escaneo-movimientos">
                @foreach ([
                    'entrada' => ['Entrada', 'fas fa-sign-in-alt'],
                    'salida' => ['Salida', 'fas fa-sign-out-alt'],
                ] as $valor => [$etiqueta, $icono])
                    <button type="button" class="escaneo-servicio" data-accion="{{ $valor }}"
                            data-etiqueta="{{ $etiqueta }}" data-icono="{{ $icono }}">
                        <i class="{{ $icono }}"></i><span>{{ $etiqueta }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Paso 2: escanear con la cámara o teclear el código --}}
        <div class="escaneo-paso" id="escaneo-paso-codigo" hidden>
            <div class="escaneo-seleccion">
                <span class="escaneo-chip"><i id="escaneo-chip-icono"></i><span id="escaneo-chip-texto"></span></span>
                <button type="button" class="escaneo-cambiar" id="escaneo-cambiar">Cambiar servicio</button>
            </div>

            <div class="escaneo-marco" id="escaneo-marco" hidden>
                <div id="escaneo-lector"></div>
                <div class="escaneo-exito" id="escaneo-exito" hidden>
                    <span class="escaneo-exito-anillo"></span>
                    <span class="escaneo-exito-check"><i class="fas fa-check"></i></span>
                    <p>Código capturado</p>
                </div>
            </div>

            <button type="button" class="escaneo-btn-camara" id="escaneo-btn-camara">
                <i class="fa fa-camera"></i> Activar cámara
            </button>
            <small class="escaneo-aviso" id="escaneo-aviso-https" hidden>
                La cámara requiere HTTPS (o localhost). También puedes teclear el código o usar un lector USB.
            </small>

            <form class="escaneo-form" id="escaneo-form" autocomplete="off">
                <input type="text" class="escaneo-codigo" id="escaneo-codigo" maxlength="6"
                       placeholder="A1B2C3" pattern="[A-Za-z0-9]{6}" aria-label="Código de la credencial">
                <button type="submit" class="escaneo-registrar" id="escaneo-registrar">Registrar</button>
            </form>

            <div class="escaneo-error" id="escaneo-error" hidden></div>
        </div>

        {{-- Paso 3: confirmación del registro --}}
        <div class="escaneo-paso escaneo-confirmacion" id="escaneo-paso-confirmacion" hidden>
            <span class="escaneo-confirmacion-icono"><i class="fas fa-check"></i></span>
            <h5 id="escaneo-res-titulo"></h5>
            <p class="escaneo-res-nino" id="escaneo-res-nino"></p>
            <p class="escaneo-res-detalle" id="escaneo-res-detalle"></p>
            <p class="escaneo-res-alergias" id="escaneo-res-alergias"></p>
            <div class="escaneo-confirmacion-acciones">
                <button type="button" class="escaneo-otro" id="escaneo-otro"><i class="fas fa-redo"></i> Registrar otro</button>
                <button type="button" class="escaneo-terminar" id="escaneo-terminar">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.RUTA_REGISTRAR = @json(route('escanear.registrar'));
    // La librería de la cámara se carga bajo demanda desde escanear.js.
    window.RUTA_HTML5QRCODE = @json(asset('vendor/html5-qrcode/html5-qrcode.min.js'));
</script>
<script src="{{ asset('js/escanear.js') }}" defer></script>
