// Ventana flotante de escaneo: sustituye a la antigua página /escanear.
// Flujo: 1) elegir servicio → 2) cámara (html5-qrcode) o captura manual /
// lector USB → 3) confirmación del registro con botón para cerrar.
// La cámara solo funciona en contextos seguros (https o localhost); la
// captura manual es un camino de primera clase, no un plan B.
(function () {
    'use strict';

    var accion = null;
    var lector = null;
    var camaraActiva = false;
    var capturado = false;
    var registrando = false;

    function paso(nombre) {
        $('#escaneo-paso-servicio').prop('hidden', nombre !== 'servicio');
        $('#escaneo-paso-codigo').prop('hidden', nombre !== 'codigo');
        $('#escaneo-paso-confirmacion').prop('hidden', nombre !== 'confirmacion');
    }

    function abrir() {
        accion = null;
        $('#escaneo-codigo').val('');
        $('#escaneo-error').prop('hidden', true).text('');
        $('#escaneo-aviso-https').prop('hidden', true);
        ocultarCaptura();
        paso('servicio');
        $('#escaneo-velo').prop('hidden', false);
        $('body').addClass('escaneo-abierto');
    }

    function cerrar() {
        detenerCamara().then(ocultarCaptura);
        $('#escaneo-velo').prop('hidden', true);
        $('body').removeClass('escaneo-abierto');
    }

    // Un registro va a medias si la petición está en vuelo, la cámara sigue
    // encendida o ya hay un código capturado en el paso de captura. En la
    // confirmación el código sigue en el campo, pero el registro ya terminó.
    function registroEnCurso() {
        if (registrando || camaraActiva) { return true; }

        var enCaptura = !$('#escaneo-paso-codigo').prop('hidden');

        return enCaptura && $('#escaneo-codigo').val().trim() !== '';
    }

    function ocultarCaptura() {
        $('#escaneo-exito').prop('hidden', true).removeClass('listo');
        $('#escaneo-marco').prop('hidden', true);
    }

    function elegirServicio(boton) {
        accion = $(boton).data('accion');
        // El chip toma el color del servicio (mismo mapa que en el CSS).
        $('.escaneo-chip').attr('data-accion', accion);
        $('#escaneo-chip-icono').attr('class', $(boton).data('icono'));
        $('#escaneo-chip-texto').text($(boton).data('etiqueta'));
        paso('codigo');
        $('#escaneo-codigo').trigger('focus');
    }

    // La librería de la cámara pesa varios cientos de KB: se carga la primera
    // vez que alguien activa la cámara, no en cada página del sistema.
    function cargarLibreria() {
        return new Promise(function (resolve, reject) {
            if (window.Html5Qrcode) { resolve(); return; }
            var script = document.createElement('script');
            script.src = window.RUTA_HTML5QRCODE;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function detenerCamara() {
        if (!camaraActiva || !lector) { return Promise.resolve(); }
        camaraActiva = false;
        $('#escaneo-btn-camara').html('<i class="fa fa-camera"></i> Activar cámara');
        return lector.stop().catch(function () { /* ya estaba detenida */ });
    }

    function iniciarCamara() {
        if (camaraActiva) {
            detenerCamara().then(ocultarCaptura);
            return;
        }
        if (!window.isSecureContext) {
            $('#escaneo-aviso-https').prop('hidden', false);
            toastr.warning('La cámara requiere HTTPS o localhost. Usa la captura manual.', 'Contexto no seguro');
            return;
        }
        cargarLibreria().then(function () {
            lector = lector || new Html5Qrcode('escaneo-lector');
            capturado = false;
            $('#escaneo-exito').prop('hidden', true).removeClass('listo');
            $('#escaneo-marco').prop('hidden', false);
            return lector.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                alLeerCodigo,
                function () { /* sin lectura en este cuadro: silencio */ }
            ).then(function () {
                camaraActiva = true;
                $('#escaneo-btn-camara').html('<i class="fa fa-stop"></i> Detener cámara');
            });
        }).catch(function () {
            ocultarCaptura();
            $('#escaneo-aviso-https').prop('hidden', false);
            toastr.error('No fue posible acceder a la cámara. Usa la captura manual.', 'Cámara no disponible');
        });
    }

    // Lectura correcta: rellena el código, muestra el spinner verde de
    // confirmación y apaga la cámara sola. El registro sigue siendo manual
    // (botón Registrar), para que quien escanea confirme lo capturado.
    function alLeerCodigo(texto) {
        if (capturado) { return; }
        capturado = true;
        $('#escaneo-codigo').val(texto.trim().toUpperCase());
        $('#escaneo-exito').prop('hidden', false);
        detenerCamara().then(function () {
            $('#escaneo-exito').addClass('listo');
            setTimeout(function () {
                ocultarCaptura();
                $('#escaneo-registrar').trigger('focus');
            }, 1100);
        });
    }

    function registrar(codigo) {
        if (registrando) { return; }
        registrando = true;
        $('#escaneo-error').prop('hidden', true).text('');
        $('#escaneo-registrar').prop('disabled', true).text('Registrando…');
        $.post(window.RUTA_REGISTRAR, { codigo: codigo, accion: accion })
            .done(mostrarConfirmacion)
            .fail(function (xhr) {
                // El toast lo pone el manejador global; aquí el detalle en la ventana.
                var cuerpo = xhr.responseJSON || {};
                var msg = cuerpo.message || 'Error inesperado.';
                if (cuerpo.errors) { msg = Object.values(cuerpo.errors).flat().join(' '); }
                $('#escaneo-error').prop('hidden', false).text(msg);
            })
            .always(function () {
                registrando = false;
                $('#escaneo-registrar').prop('disabled', false).text('Registrar');
            });
    }

    function mostrarConfirmacion(datos) {
        $('#escaneo-res-titulo').text(datos.title || 'Registro completado');
        if (datos.nino) {
            $('#escaneo-res-nino').text(datos.nino.nombre_completo + ' · ' + datos.nino.edad + ' años · Dieta: ' + datos.nino.dieta);
            var alergias = [];
            if (datos.nino.alerg_alimentos) { alergias.push('Alergia alimentos: ' + datos.nino.alerg_alimentos); }
            if (datos.nino.alerg_medicamentos) { alergias.push('Alergia medicamentos: ' + datos.nino.alerg_medicamentos); }
            $('#escaneo-res-alergias').text(alergias.join(' · '));
        } else {
            $('#escaneo-res-nino').text('');
            $('#escaneo-res-alergias').text('');
        }
        $('#escaneo-res-detalle').text(datos.msg || '');
        paso('confirmacion');
    }

    $(function () {
        $(document).on('click', '.js-abrir-escanear', abrir);
        $('#escaneo-cerrar, #escaneo-terminar').on('click', cerrar);
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && !$('#escaneo-velo').prop('hidden')) { cerrar(); }
        });

        // Tocar fuera de la ventana la cierra, salvo con un registro a medias:
        // ahí un toque accidental tiraría la captura. La ✕ y Escape siguen
        // cerrando siempre, como salida deliberada.
        $('#escaneo-velo').on('click', function (e) {
            if (e.target !== this) { return; }
            if (registroEnCurso()) {
                toastr.info('Termina o cancela la captura para cerrar.', 'Registro a medias');
                return;
            }
            cerrar();
        });

        $('.escaneo-servicio').on('click', function () { elegirServicio(this); });
        $('#escaneo-cambiar').on('click', function () {
            detenerCamara().then(ocultarCaptura);
            paso('servicio');
        });

        $('#escaneo-btn-camara').on('click', iniciarCamara);

        $('#escaneo-form').on('submit', function (e) {
            e.preventDefault();
            var codigo = $('#escaneo-codigo').val().trim().toUpperCase();
            if (codigo.length === 6) {
                registrar(codigo);
            } else {
                toastr.warning('El código debe tener 6 caracteres.', 'Código incompleto');
            }
        });

        // Registrar otro: conserva el servicio elegido y limpia solo el código.
        $('#escaneo-otro').on('click', function () {
            $('#escaneo-codigo').val('');
            $('#escaneo-error').prop('hidden', true).text('');
            paso('codigo');
            $('#escaneo-codigo').trigger('focus');
        });
    });
})();
