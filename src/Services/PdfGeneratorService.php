<?php
namespace App\Services;

use TCPDF;
use App\Core\Session;

/**
 * Clase personalizada para manejar el formato de los reportes PDVSA.
 */
class CustomPDF extends TCPDF
{
    private $usuarioNombre;

    public function setUsuarioNombre(string $nombre): void
    {
        $this->usuarioNombre = $nombre;
    }

    /**
     * Método Footer de TCPDF para paginación y crédito.
     */
    public function Footer()
    {
        // Posición en la parte inferior
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        
        // Página actual y total
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C');
        
        // Crédito al usuario
        $this->SetY(-15);
        $this->Cell(0, 10, 'Generado por: ' . $this->usuarioNombre, 0, false, 'R');
    }
}

/**
 * Servicio encargado de la generación de archivos PDF utilizando TCPDF.
 */
class PdfGeneratorService
{
    /**
     * Genera un archivo PDF basado en un tipo de reporte y datos proporcionados.
     * 
     * @param string $tipoReporte 'fallas', 'cumplimiento', 'resumen-mensual', 'tecnicos'
     * @param array $filtros Filtros aplicados en el formulario
     * @param array $datos Datos a incluir en la tabla del reporte
     * @return array ['ruta' => string, 'nombre' => string, 'tamano' => int, 'duracion' => int]
     */
    public static function generarPdf(string $tipoReporte, array $filtros, array $datos): array
    {
        $inicio = microtime(true);
        $usuarioNombre = Session::get('nombre_completo', 'Usuario');

        // Configuración: CARTA, Portrait, márgenes 15mm/10mm, fuente DejaVu Sans
        $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setUsuarioNombre($usuarioNombre);

        // Información del documento
        $pdf->SetCreator('Sistema PDVSA');
        $pdf->SetAuthor($usuarioNombre);
        $pdf->SetTitle('Reporte ' . ucfirst(str_replace('-', ' ', $tipoReporte)));

        // Eliminar encabezado/pie por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // Márgenes
        $pdf->SetMargins(15, 15, 10);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);

        // Añadir página
        $pdf->AddPage();

        // --- HEADER PERSONALIZADO ---
        // Logo
        $logoPath = dirname(__DIR__, 2) . '/public/assets/images/logo-pdvsa.jpg';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 15, 30, '', 'JPG', '', '', false, 300, '', false, false, 0);
        }

        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->SetXY(45, 15);
        $pdf->Cell(0, 10, 'Sistema de Mantenimiento PDVSA', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->SetX(45);
        $pdf->Cell(0, 5, 'Punta Mata', 0, 1, 'L');

        $pdf->Ln(10);
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetX(15);
        $pdf->Cell(0, 10, 'Reporte: ' . ucfirst(str_replace('-', ' ', $tipoReporte)), 0, 1, 'L');

        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetX(15);
        $pdf->Cell(0, 5, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1, 'L');
        $pdf->SetX(15);
        $pdf->Cell(0, 5, 'Por: ' . $usuarioNombre, 0, 1, 'L');

        // Filtros aplicados
        $pdf->Ln(5);
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->SetX(15);
        $pdf->Cell(0, 5, 'Filtros aplicados:', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 9);
        
        $filtroTextos = [];
        foreach ($filtros as $key => $value) {
            if ($value !== null && $value !== '') {
                $filtroTextos[] = ucfirst(str_replace('_', ' ', $key)) . ": " . $value;
            }
        }
        $filtroStr = !empty($filtroTextos) ? implode(' | ', $filtroTextos) : "Ninguno";
        $pdf->SetX(20);
        $pdf->MultiCell(0, 5, $filtroStr, 0, 'L');
        
        $pdf->Ln(5);
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(5);

        // --- TABLA DE DATOS ---
        if (empty($datos)) {
            $pdf->SetFont('dejavusans', 'I', 12);
            $pdf->Cell(0, 10, 'No se encontraron resultados con los filtros seleccionados.', 0, 1, 'C');
        } else {
            $pdf->SetFont('dejavusans', 'B', 9);
            
            // Obtener encabezados
            $headers = array_keys($datos[0]);
            
            // Calcular ancho de columnas
            $count = count($headers);
            $colWidth = (195 - 20) / $count; // 175 mm de ancho útil aprox

            // Dibujar encabezado de tabla
            $pdf->SetFillColor(230, 230, 230);
            foreach ($headers as $header) {
                $pdf->Cell($colWidth, 7, ucfirst(str_replace('_', ' ', $header)), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Dibujar filas
            $pdf->SetFont('dejavusans', '', 8);
            foreach ($datos as $row) {
                // Check para evitar overflow de página excesivo o líneas vacías
                if ($pdf->GetY() > 260) {
                    $pdf->AddPage();
                }

                foreach ($headers as $header) {
                    $val = $row[$header] ?? '';
                    $pdf->Cell($colWidth, 6, (string)$val, 1, 0, 'C');
                }
                $pdf->Ln();
            }
        }

        // --- GUARDADO DEL ARCHIVO ---
        $fecha = date('Y-m-d_H-i-s');
        $nombreArchivo = str_replace('-', '_', $tipoReporte) . '_' . $fecha . '.pdf';
        
        // Estructura: storage/reportes-pdf/{año}/{mes}/
        $year = date('Y');
        $month = date('m');
        $dirPath = dirname(__DIR__, 2) . "/storage/reportes-pdf/$year/$month/";
        
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        
        $rutaCompleta = $dirPath . $nombreArchivo;
        
        $pdf->Output($rutaCompleta, 'F');
        
        $tamano = filesize($rutaCompleta);
        $duracion = (int)((microtime(true) - $inicio) * 1000);

        return [
            'ruta' => $rutaCompleta,
            'nombre' => $nombreArchivo,
            'tamano' => $tamano,
            'duracion' => $duracion
        ];
    }
}
