<?php

namespace App\Services;

use App\Models\Nino;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use RuntimeException;

/**
 * Generación del código QR de 6 caracteres (3 dígitos + 3 letras, p. ej. A1B2C3).
 *
 * A diferencia del prototipo: usa random_int (CSPRNG, no mt_rand), verifica
 * colisiones contra la base de datos (validaQR() era un stub que regresaba 1)
 * y el índice único en nino.qr respalda la unicidad a nivel de esquema.
 */
class QrCodeService
{
    private const LETRAS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const DIGITOS = '0123456789';

    private const MAX_INTENTOS = 100;

    public function generar(): string
    {
        for ($intento = 0; $intento < self::MAX_INTENTOS; $intento++) {
            $codigo = $this->codigoAleatorio();

            if (! Nino::where('qr', $codigo)->exists()) {
                return $codigo;
            }
        }

        throw new RuntimeException('No fue posible generar un código QR único tras '.self::MAX_INTENTOS.' intentos.');
    }

    /**
     * PNG del código QR, en memoria. El contenido codificado es el código de
     * 6 caracteres tal cual — el prototipo codificaba una URL http:// de un
     * dominio muerto hacia una ruta inexistente, vía un servicio de terceros
     * (api.qrserver.com) y un archivo temporal compartido con condición de carrera.
     *
     * $tamano en píxeles: 300 basta en pantalla; una impresión en grande
     * puede pedir más para salir nítida.
     */
    public function render(string $codigo, int $tamano = 300): string
    {
        $builder = new Builder(
            writer: new PngWriter,
            data: $codigo,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $tamano,
            margin: intdiv($tamano, 30),
        );

        return $builder->build()->getString();
    }

    protected function codigoAleatorio(): string
    {
        $caracteres = [
            self::LETRAS[random_int(0, 25)],
            self::LETRAS[random_int(0, 25)],
            self::LETRAS[random_int(0, 25)],
            self::DIGITOS[random_int(0, 9)],
            self::DIGITOS[random_int(0, 9)],
            self::DIGITOS[random_int(0, 9)],
        ];

        // Fisher-Yates con random_int para no depender de mt_rand.
        for ($i = count($caracteres) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$caracteres[$i], $caracteres[$j]] = [$caracteres[$j], $caracteres[$i]];
        }

        return implode('', $caracteres);
    }
}
