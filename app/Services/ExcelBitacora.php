<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

/**
 * Genera un libro de Excel (.xlsx) sin dependencias externas: un .xlsx es un
 * ZIP con XML adentro y la extensión zip ya viene con PHP. Para una hoja
 * simple de bitácora esto evita cargar PhpSpreadsheet completo, siguiendo la
 * línea del proyecto de mantener las dependencias al mínimo.
 *
 * Todas las celdas se escriben como texto en línea (inlineStr), suficiente
 * para una bitácora que se lee y se filtra en Excel.
 */
class ExcelBitacora
{
    /**
     * @param  list<string>  $encabezados
     * @param  list<list<string>>  $filas
     * @return string El contenido binario del .xlsx
     */
    public function generar(array $encabezados, array $filas, string $hoja = 'Bitácora'): string
    {
        $ruta = tempnam(sys_get_temp_dir(), 'xlsx');

        $zip = new ZipArchive;
        if ($zip->open($ruta, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo crear el archivo temporal del Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->relacionesPaquete());
        $zip->addFromString('xl/workbook.xml', $this->workbook($hoja));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->relacionesWorkbook());
        $zip->addFromString('xl/styles.xml', $this->estilos());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->hoja($encabezados, $filas));
        $zip->close();

        $contenido = file_get_contents($ruta);
        unlink($ruta);

        return $contenido;
    }

    private function contentTypes(): string
    {
        return <<<'XML'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
            <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
            <Default Extension="xml" ContentType="application/xml"/>
            <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
            <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
            <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
        </Types>
        XML;
    }

    private function relacionesPaquete(): string
    {
        return <<<'XML'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
            <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
        </Relationships>
        XML;
    }

    private function workbook(string $hoja): string
    {
        $nombre = $this->escapar($hoja);

        return <<<XML
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
            <sheets><sheet name="{$nombre}" sheetId="1" r:id="rId1"/></sheets>
        </workbook>
        XML;
    }

    private function relacionesWorkbook(): string
    {
        return <<<'XML'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
            <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
            <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
        </Relationships>
        XML;
    }

    /** Dos estilos: 0 = normal, 1 = encabezado en negritas. */
    private function estilos(): string
    {
        return <<<'XML'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
            <fonts count="2">
                <font><sz val="11"/><name val="Calibri"/></font>
                <font><b/><sz val="11"/><name val="Calibri"/></font>
            </fonts>
            <fills count="2">
                <fill><patternFill patternType="none"/></fill>
                <fill><patternFill patternType="gray125"/></fill>
            </fills>
            <borders count="1"><border/></borders>
            <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
            <cellXfs count="2">
                <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
                <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
            </cellXfs>
            <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
        </styleSheet>
        XML;
    }

    /**
     * @param  list<string>  $encabezados
     * @param  list<list<string>>  $filas
     */
    private function hoja(array $encabezados, array $filas): string
    {
        // Ancho de cada columna según su contenido más largo (con tope, para
        // que un nombre kilométrico no deforme la hoja).
        $columnas = '';
        foreach ($encabezados as $indice => $encabezado) {
            $ancho = mb_strlen($encabezado);
            foreach ($filas as $fila) {
                $ancho = max($ancho, mb_strlen($fila[$indice] ?? ''));
            }
            $ancho = min($ancho + 3, 45);
            $numero = $indice + 1;
            $columnas .= "<col min=\"{$numero}\" max=\"{$numero}\" width=\"{$ancho}\" customWidth=\"1\"/>";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            ."<cols>{$columnas}</cols><sheetData>"
            .$this->fila(1, $encabezados, estilo: 1);

        foreach ($filas as $indice => $fila) {
            $xml .= $this->fila($indice + 2, $fila);
        }

        return $xml.'</sheetData></worksheet>';
    }

    /** @param  list<string>  $celdas */
    private function fila(int $numero, array $celdas, int $estilo = 0): string
    {
        $xml = "<row r=\"{$numero}\">";
        foreach ($celdas as $indice => $celda) {
            $referencia = $this->letraColumna($indice).$numero;
            $texto = $this->escapar($celda);
            $xml .= "<c r=\"{$referencia}\" s=\"{$estilo}\" t=\"inlineStr\"><is><t>{$texto}</t></is></c>";
        }

        return $xml.'</row>';
    }

    /** 0 => A, 1 => B, ... 26 => AA. */
    private function letraColumna(int $indice): string
    {
        $letras = '';
        while ($indice >= 0) {
            $letras = chr(65 + ($indice % 26)).$letras;
            $indice = intdiv($indice, 26) - 1;
        }

        return $letras;
    }

    private function escapar(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
