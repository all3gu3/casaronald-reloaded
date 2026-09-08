<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Pais;
use Illuminate\Database\Seeder;

/**
 * Cadena geográfica pais → estado → municipio.
 * El prototipo sembraba 81 municipios de Guerrero mientras los datos demo usaban
 * municipios de Puebla; aquí se conservan los de Guerrero y se agrega el bloque
 * de Puebla para que el catálogo sea consistente con la operación de la Casa Puebla.
 */
class GeografiaSeeder extends Seeder
{
    public function run(): void
    {
        $mexico = Pais::firstOrCreate(['pais' => 'México']);
        Pais::firstOrCreate(['pais' => 'Belice']);
        Pais::firstOrCreate(['pais' => 'Guatemala']);

        $estados = [
            'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche',
            'Coahuila de Zaragoza', 'Colima', 'Chiapas', 'Chihuahua', 'Distrito Federal',
            'Durango', 'Guanajuato', 'Guerrero', 'Hidalgo', 'Jalisco', 'México',
            'Michoacán de Ocampo', 'Morelos', 'Nayarit', 'Nuevo León', 'Oaxaca de Juárez',
            'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí', 'Sinaloa', 'Sonora',
            'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz de Ignacio de la Llave',
            'Yucatán', 'Zacatecas',
        ];
        foreach ($estados as $estado) {
            Estado::firstOrCreate(['pais_id' => $mexico->id, 'estado' => $estado]);
        }

        $municipios = [
            'Guerrero' => [
                'Acapulco de Juárez', 'Ahuacuotzingo', 'Ajuchitlán del Progreso',
                'Alcozauca de Guerrero', 'Alpoyeca', 'Apaxtla', 'Arcelia',
                'Atenango del Río', 'Atlamajalcingo del Monte', 'Atlixtac',
                'Atoyac de Álvarez', 'Ayutla de los Libres', 'Azoyú', 'Benito Juárez',
                'Buenavista de Cuéllar', 'Coahuayutla de José María Izazaga', 'Cocula',
                'Copala', 'Copalillo', 'Copanatoyac', 'Coyuca de Benítez',
                'Coyuca de Catalán', 'Cuajinicuilapa', 'Cualác', 'Cuautepec',
                'Cuetzala del Progreso', 'Cutzamala de Pinzón', 'Chilapa de Álvarez',
                'Chilpancingo de los Bravo', 'Florencio Villarreal',
                'General Canuto A. Neri', 'General Heliodoro Castillo', 'Huamuxtitlán',
                'Huitzuco de los Figueroa', 'Iguala de la Independencia', 'Igualapa',
                'Ixcateopan de Cuauhtémoc', 'Zihuatanejo de Azueta', 'Juan R. Escudero',
                'Leonardo Bravo', 'Malinaltepec', 'Mártir de Cuilapan', 'Metlatónoc',
                'Mochitlán', 'Olinalá', 'Ometepec', 'Pedro Ascencio Alquisiras',
                'Petatlán', 'Pilcaya', 'Pungarabato', 'Quechultenango',
                'San Luis Acatlán', 'San Marcos', 'San Miguel Totolapan',
                'Taxco de Alarcón', 'Tecoanapa', 'Técpan de Galeana', 'Teloloapan',
                'Tepecoacuilco de Trujano', 'Tetipac', 'Tixtla de Guerrero',
                'Tlacoachistlahuaca', 'Tlacoapa', 'Tlalchapa',
                'Tlalixtaquilla de Maldonado', 'Tlapa de Comonfort', 'Tlapehuala',
                'La Unión de Isidoro Montes de Oca', 'Xalpatláhuac', 'Xochihuehuetlán',
                'Xochistlahuaca', 'Zapotitlán Tablas', 'Zirándaro', 'Zitlala',
                'Eduardo Neri', 'Acatepec', 'Marquelia', 'Cochoapa el Grande',
                'José Joaquín de Herrera', 'Juchitán', 'Iliatenco',
            ],
            'Puebla' => [
                'Puebla', 'Acatzingo', 'Acteopan', 'Chichiquila', 'Epatlán', 'Amozoc',
                'Atlixco', 'Cholula de Rivadavia', 'Cuetzalan del Progreso',
                'Huauchinango', 'Izúcar de Matamoros', 'Libres', 'San Martín Texmelucan',
                'Tehuacán', 'Teziutlán', 'Zacatlán',
            ],
        ];
        foreach ($municipios as $nombreEstado => $lista) {
            $estado = Estado::where('estado', $nombreEstado)->firstOrFail();
            foreach ($lista as $municipio) {
                Municipio::firstOrCreate(['estado_id' => $estado->id, 'municipio' => $municipio]);
            }
        }
    }
}
