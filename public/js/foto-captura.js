// Campo de foto del expediente: elegir un archivo o tomar la foto con la
// cámara del aparato, y en los dos casos normalizarla antes de subirla.
//
// «Tomar foto» enciende la cámara dentro de la página (getUserMedia) y
// enseña el encuadre en vivo: el atributo capture del <input> solo abre la
// cámara en el teléfono y en una computadora el navegador lo ignora y saca el
// explorador de archivos. El <input capture> queda como respaldo para los
// navegadores que no dan getUserMedia (o sin HTTPS).
//
// El navegador decodifica lo que le dé el aparato (incluido el HEIC del
// iPhone, que Safari sí entiende) y lo vuelve a dibujar como JPEG chico. Así
// el servidor recibe siempre un formato que todos los navegadores muestran, y
// una foto de 12 megapíxeles no se topa con el límite de subida de PHP.
//
// Marcado esperado, dentro de un contenedor con [data-foto-campo]:
//   - [data-foto-archivo] : el <input type="file"> que sí lleva name="image"
//   - [data-foto-camara]  : <input type="file" capture> oculto, sin name
//   - [data-foto-tomar]   : el botón que dispara la cámara
//   - data-foto-previa    : selector de la <img> donde se ve el resultado
//
// La ventana de la cámara vive una sola vez en la página:
// resources/views/parts/foto-camara.blade.php (incluida en el esqueleto).
(function () {
    'use strict';

    // 1600 px de lado alcanzan de sobra para el carnet impreso; el servidor
    // vuelve a reducir a 1200 al guardar.
    var LADO_MAXIMO = 1600;
    var CALIDAD = 0.85;

    // createImageBitmap respeta la orientación EXIF (las fotos de teléfono
    // llegan giradas); el <img> es el respaldo para navegadores sin soporte.
    function decodificar(archivo) {
        if (window.createImageBitmap) {
            try {
                return createImageBitmap(archivo, {imageOrientation: 'from-image'})
                    .catch(function () { return decodificarConImg(archivo); });
            } catch (e) {
                return decodificarConImg(archivo);
            }
        }

        return decodificarConImg(archivo);
    }

    function decodificarConImg(archivo) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(archivo);
            var imagen = new Image();
            imagen.onload = function () { resolve(imagen); };
            imagen.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('formato no soportado por el navegador'));
            };
            imagen.src = url;
        });
    }

    function normalizar(archivo) {
        return decodificar(archivo).then(function (fuente) {
            var ancho = fuente.width;
            var alto = fuente.height;
            var escala = Math.min(1, LADO_MAXIMO / Math.max(ancho, alto));
            var lienzo = document.createElement('canvas');
            lienzo.width = Math.max(1, Math.round(ancho * escala));
            lienzo.height = Math.max(1, Math.round(alto * escala));

            var pincel = lienzo.getContext('2d');
            // Fondo blanco: un PNG transparente no debe quedar negro en JPEG.
            pincel.fillStyle = '#ffffff';
            pincel.fillRect(0, 0, lienzo.width, lienzo.height);
            pincel.drawImage(fuente, 0, 0, lienzo.width, lienzo.height);
            if (fuente.close) { fuente.close(); }

            return new Promise(function (resolve, reject) {
                lienzo.toBlob(function (blob) {
                    if (blob) {
                        resolve({blob: blob, ancho: ancho, alto: alto});
                    } else {
                        reject(new Error('el navegador no pudo generar el JPEG'));
                    }
                }, 'image/jpeg', CALIDAD);
            });
        });
    }

    // DataTransfer permite dejar el JPEG ya normalizado dentro del <input>,
    // así el FormData del formulario sigue funcionando sin cambios.
    function asignarAlCampo(input, blob) {
        if (typeof DataTransfer === 'undefined') { return false; }

        try {
            var transferencia = new DataTransfer();
            transferencia.items.add(new File([blob], 'foto.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now(),
            }));
            input.files = transferencia.files;

            return input.files.length === 1;
        } catch (e) {
            return false;
        }
    }

    // Respaldo para navegadores sin DataTransfer: sube el archivo original y
    // deja que el servidor lo normalice. name="image" viaja al campo que de
    // verdad tiene la foto.
    function moverNombre(conFoto, sinFoto) {
        sinFoto.removeAttribute('name');
        conFoto.setAttribute('name', 'image');
    }

    function mostrarPrevia(previa, blobOArchivo) {
        if (!previa) { return; }

        if (previa.dataset.urlPrevia) {
            URL.revokeObjectURL(previa.dataset.urlPrevia);
        }

        var url = URL.createObjectURL(blobOArchivo);
        previa.dataset.urlPrevia = url;
        previa.src = url;
        previa.style.width = '200px';
    }

    function avisar(tipo, mensaje, titulo) {
        if (window.toastr) {
            toastr[tipo](mensaje, titulo);
        }
    }

    function procesar(campo, origen, archivoInput, camaraInput, previa) {
        var original = origen.files[0];

        normalizar(original).then(function (resultado) {
            if (resultado.ancho !== resultado.alto) {
                avisar('warning', '¡Se recomienda usar imágenes cuadradas!');
            }

            if (asignarAlCampo(archivoInput, resultado.blob)) {
                moverNombre(archivoInput, camaraInput);
            } else {
                moverNombre(origen, origen === archivoInput ? camaraInput : archivoInput);
            }

            mostrarPrevia(previa, resultado.blob);
            campo.dispatchEvent(new CustomEvent('foto:cambiada', {bubbles: true}));
        }).catch(function () {
            // Un HEIC en una computadora con Windows llega hasta aquí: el
            // navegador no lo sabe dibujar y el servidor tampoco lo mostraría.
            origen.value = '';
            avisar(
                'error',
                'Este formato de imagen no se puede usar. Guarda la foto como JPG o PNG e inténtalo de nuevo.',
                'Formato no compatible'
            );
        });
    }

    // --- Cámara dentro de la página ---------------------------------------

    var camara = {
        campo: null,
        archivoInput: null,
        camaraInput: null,
        previa: null,
        flujo: null,
        blob: null,
        urlPrevia: null,
        lado: 'environment',
    };

    function elemento(id) {
        return document.getElementById(id);
    }

    // DataTransfer es lo único que permite dejar la foto tomada dentro del
    // <input file>; sin él la cámara en vivo no tendría cómo entregarla.
    function hayCamaraEnVivo() {
        return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
            && typeof DataTransfer !== 'undefined'
            && window.isSecureContext !== false;
    }

    function abrirCamara(campo, archivoInput, camaraInput, previa) {
        var velo = elemento('camara-velo');

        if (!velo || !hayCamaraEnVivo()) {
            // Respaldo: el <input capture>. En el teléfono abre la cámara del
            // sistema; en una computadora, el explorador de archivos.
            if (window.isSecureContext === false) {
                avisar(
                    'warning',
                    'La cámara en vivo necesita HTTPS o localhost.',
                    'Contexto no seguro'
                );
            }
            camaraInput.click();

            return;
        }

        camara.campo = campo;
        camara.archivoInput = archivoInput;
        camara.camaraInput = camaraInput;
        camara.previa = previa;
        camara.lado = 'environment';

        // El formulario vive en un modal de Bootstrap, que devuelve el foco a
        // su propio contenido: con la ventana de la cámara dentro del modal,
        // sus botones se pueden usar también con el teclado.
        (document.querySelector('.modal.show') || document.body).appendChild(velo);
        velo.hidden = false;
        document.body.classList.add('camara-abierta');
        pasoCaptura();
        encender();
    }

    function encender() {
        var video = elemento('camara-video');
        elemento('camara-encendiendo').hidden = false;
        video.classList.toggle('camara-espejo', camara.lado === 'user');

        return navigator.mediaDevices.getUserMedia({
            audio: false,
            video: {
                facingMode: camara.lado,
                width: {ideal: 1280},
                height: {ideal: 1280},
            },
        }).then(function (flujo) {
            camara.flujo = flujo;
            video.srcObject = flujo;
            var reproduccion = video.play();

            return reproduccion && reproduccion.catch ? reproduccion.catch(function () {}) : null;
        }).then(function () {
            elemento('camara-encendiendo').hidden = true;

            return mostrarCambioDeCamara();
        }).catch(camaraNoDisponible);
    }

    // Cambiar de cámara solo tiene sentido con más de una (frontal y trasera
    // del teléfono); en una computadora con una sola webcam el botón sobra.
    function mostrarCambioDeCamara() {
        if (!navigator.mediaDevices.enumerateDevices) { return null; }

        return navigator.mediaDevices.enumerateDevices().then(function (aparatos) {
            var camaras = aparatos.filter(function (aparato) {
                return aparato.kind === 'videoinput';
            });
            elemento('camara-cambiar').hidden = camaras.length < 2;
        }).catch(function () { /* sin lista de aparatos: el botón sigue oculto */ });
    }

    function camaraNoDisponible(error) {
        var nombre = error && error.name;
        cerrarCamara();

        if (nombre === 'NotAllowedError' || nombre === 'SecurityError') {
            avisar(
                'warning',
                'El navegador bloqueó la cámara. Permítela desde la barra de direcciones, o elige la imagen con el selector de archivo.',
                'Cámara bloqueada'
            );

            return;
        }

        // Sin cámara en el aparato, o la tiene ocupada otro programa.
        avisar(
            'error',
            'No fue posible encender la cámara. Elige la imagen con el selector de archivo.',
            'Cámara no disponible'
        );
    }

    function apagar() {
        if (camara.flujo) {
            camara.flujo.getTracks().forEach(function (pista) { pista.stop(); });
            camara.flujo = null;
        }

        var video = elemento('camara-video');
        if (video) { video.srcObject = null; }
    }

    function soltarInstantanea() {
        if (camara.urlPrevia) {
            URL.revokeObjectURL(camara.urlPrevia);
            camara.urlPrevia = null;
        }

        camara.blob = null;
        elemento('camara-previa').removeAttribute('src');
    }

    function pasoCaptura() {
        soltarInstantanea();
        elemento('camara-previa').hidden = true;
        elemento('camara-video').hidden = false;
        elemento('camara-acciones-tomar').hidden = false;
        elemento('camara-acciones-revisar').hidden = true;
        elemento('camara-guia').textContent = 'Encuadra el rostro y toca «Capturar».';
        elemento('camara-disparar').focus();
    }

    function pasoRevision(blob) {
        camara.blob = blob;
        camara.urlPrevia = URL.createObjectURL(blob);

        var previa = elemento('camara-previa');
        previa.src = camara.urlPrevia;
        previa.hidden = false;
        elemento('camara-video').hidden = true;
        elemento('camara-acciones-tomar').hidden = true;
        elemento('camara-acciones-revisar').hidden = false;
        elemento('camara-guia').textContent = '¿Se ve bien? Puedes repetirla antes de usarla.';
        elemento('camara-usar').focus();
    }

    // Recorta el cuadrado del centro: es exactamente lo que se ve en el marco
    // (object-fit: cover) y deja la foto cuadrada que pide el carnet.
    function disparar() {
        var video = elemento('camara-video');
        var ancho = video.videoWidth;
        var alto = video.videoHeight;

        if (!ancho || !alto) {
            avisar('warning', 'La cámara todavía no está lista.');

            return;
        }

        var recorte = Math.min(ancho, alto);
        var lado = Math.min(recorte, LADO_MAXIMO);
        var lienzo = document.createElement('canvas');
        lienzo.width = lado;
        lienzo.height = lado;

        var pincel = lienzo.getContext('2d');
        pincel.fillStyle = '#ffffff';
        pincel.fillRect(0, 0, lado, lado);
        pincel.drawImage(video, (ancho - recorte) / 2, (alto - recorte) / 2, recorte, recorte, 0, 0, lado, lado);

        lienzo.toBlob(function (blob) {
            if (blob) {
                pasoRevision(blob);
            } else {
                avisar('error', 'No fue posible tomar la foto. Inténtalo de nuevo.');
            }
        }, 'image/jpeg', CALIDAD);
    }

    function cambiarCamara() {
        apagar();
        camara.lado = camara.lado === 'environment' ? 'user' : 'environment';
        encender();
    }

    // La foto ya sale del lienzo como JPEG del tamaño bueno: entra directo al
    // <input> sin volver a pasar por normalizar().
    function usarFoto() {
        if (!camara.blob) { return; }

        if (!asignarAlCampo(camara.archivoInput, camara.blob)) {
            avisar(
                'error',
                'Este navegador no deja adjuntar la foto tomada. Elige la imagen con el selector de archivo.',
                'Navegador no compatible'
            );

            return;
        }

        moverNombre(camara.archivoInput, camara.camaraInput);
        mostrarPrevia(camara.previa, camara.blob);
        camara.campo.dispatchEvent(new CustomEvent('foto:cambiada', {bubbles: true}));
        cerrarCamara();
    }

    function cerrarCamara() {
        var velo = elemento('camara-velo');
        if (!velo) { return; }

        apagar();
        soltarInstantanea();
        velo.hidden = true;
        elemento('camara-cambiar').hidden = true;
        // De vuelta al <body>: si la ventana se quedara dentro del modal del
        // expediente, se iría con él la próxima vez que se cierre.
        document.body.appendChild(velo);
        document.body.classList.remove('camara-abierta');
        camara.campo = null;
        camara.archivoInput = null;
        camara.camaraInput = null;
        camara.previa = null;
    }

    function prepararVentanaCamara() {
        var velo = elemento('camara-velo');
        if (!velo) { return; }

        elemento('camara-cerrar').addEventListener('click', cerrarCamara);
        elemento('camara-disparar').addEventListener('click', disparar);
        elemento('camara-repetir').addEventListener('click', pasoCaptura);
        elemento('camara-usar').addEventListener('click', usarFoto);
        elemento('camara-cambiar').addEventListener('click', cambiarCamara);

        velo.addEventListener('click', function (evento) {
            if (evento.target === velo) { cerrarCamara(); }
        });

        // En captura, para que Escape cierre solo la cámara y no también el
        // modal del expediente que hay debajo (Bootstrap escucha en burbuja).
        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape' && !velo.hidden) {
                evento.stopPropagation();
                cerrarCamara();
            }
        }, true);
    }

    function iniciar(campo) {
        var archivoInput = campo.querySelector('[data-foto-archivo]');
        var camaraInput = campo.querySelector('[data-foto-camara]');
        var boton = campo.querySelector('[data-foto-tomar]');
        var previa = campo.getAttribute('data-foto-previa')
            ? document.querySelector(campo.getAttribute('data-foto-previa'))
            : null;

        if (!archivoInput || !camaraInput) { return; }

        if (boton) {
            boton.addEventListener('click', function () {
                abrirCamara(campo, archivoInput, camaraInput, previa);
            });
        }

        [archivoInput, camaraInput].forEach(function (input) {
            input.addEventListener('change', function () {
                if (input.files && input.files.length) {
                    procesar(campo, input, archivoInput, camaraInput, previa);
                }
            });
        });
    }

    // Limpia el campo al reiniciar el formulario (alta después de una edición).
    window.limpiarCampoFoto = function (selectorCampo) {
        var campo = document.querySelector(selectorCampo);
        if (!campo) { return; }

        campo.querySelectorAll('input[type="file"]').forEach(function (input) {
            input.value = '';
            input.removeAttribute('name');
        });

        var archivoInput = campo.querySelector('[data-foto-archivo]');
        if (archivoInput) { archivoInput.setAttribute('name', 'image'); }

        var previa = campo.getAttribute('data-foto-previa')
            ? document.querySelector(campo.getAttribute('data-foto-previa'))
            : null;

        if (previa && previa.dataset.urlPrevia) {
            URL.revokeObjectURL(previa.dataset.urlPrevia);
            delete previa.dataset.urlPrevia;
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        prepararVentanaCamara();
        document.querySelectorAll('[data-foto-campo]').forEach(iniciar);
    });
})();
