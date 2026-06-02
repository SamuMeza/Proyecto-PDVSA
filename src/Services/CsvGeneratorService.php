<?php
namespace App\Services;

class CsvGeneratorService
{
    /**
     * Genera un archivo CSV basado en un tipo de reporte y datos proporcionados.
     * 
     * @param string $tipoReporte 'fallas', 'cumplimiento', 'resumen-mensual', 'tecnicos'
     * @param array $filtros Filtros aplicados en el formulario
     * @param array $datos Datos a incluir en el CSV
     * @return array ['ruta' => string, 'nombre' => string]
     */
    public static function generarCsv(string $tipoReporte, array $filtros, array $datos): array
    {
        $fecha = date('Y-m-d_H-i-s');
        $nombreArchivo = str_replace('-', '_', $tipoReporte) . '_' . $fecha . '.csv';
        
        // Estructura de carpetas: storage/reportes-pdf/{año}/{mes}/
        $year = date('Y');
        $month = date('m');
        $dirPath = dirname(__DIR__, 2) . "/storage/reportes-pdf/$year/$month/";
        
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        
        $rutaCompleta = $dirPath . $nombreArchivo;
        
        // Crear archivo con BOM UTF-8
        $handle = fopen($rutaCompleta, 'w');
        
        // Escribir BOM para UTF-8 (para que Excel reconozca los acentos)
        fwrite($handle, "\xEF\xBB\xBF");
        
        if (empty($datos)) {
            // Si no hay datos, solo escribir encabezados vacíos
            fwrite($handle, "No hay datos disponibles\r\n");
        } else {
            // Obtener encabezados de las llaves del primer registro
            $headers = array_keys($datos[0]);
            
            // Escribir encabezados
            $headerRow = [];
            foreach ($headers as $header) {
                $headerRow[] = '"' . ucfirst(str_replace('_', ' ', $header)) . '"';
            }
            fwrite($handle, implode(';', $headerRow) . "\r\n");
            
            // Escribir filas de datos
            foreach ($datos as $row) {
                $dataRow = [];
                foreach ($headers as $header) {
                    $val = $row[$header] ?? '';
                    // Escapar comillas dobles
                    $val = str_replace('"', '""', (string)$val);
                    $dataRow[] = '"' . $val . '"';
                }
                fwrite($handle, implode(';', $dataRow) . "\r\n");
            }
        }
        
        fclose($handle);
        
        return [
            'ruta' => $rutaCompleta,
            'nombre' => $nombreArchivo,
        ];
    }
}
