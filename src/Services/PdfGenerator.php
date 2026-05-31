<?php
namespace App\Services;

class PdfGenerator
{
    public static function generate(string $html, string $filename = 'documento.pdf'): string
    {
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
        } elseif (class_exists('\TCPDF')) {
            $pdf = new \TCPDF();
            $pdf->AddPage();
            $pdf->writeHTML($html);
            $output = $pdf->Output($filename, 'S');
        } else {
            throw new \RuntimeException('No hay librería PDF disponible (dompdf/tcpdf)');
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/assets/uploads/reportes-pdf';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $path = rtrim($uploadDir, '/') . '/' . $filename;
        file_put_contents($path, $output);
        return $path;
    }
}
