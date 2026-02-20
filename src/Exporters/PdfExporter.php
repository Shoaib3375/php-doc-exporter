<?php

namespace Shoaib3375\PhpDocExporter\Exporters;

use Dompdf\Dompdf;
use Dompdf\Options;
use Shoaib3375\PhpDocExporter\ExporterInterface;

class PdfExporter implements ExporterInterface
{
    public function export(array $data, array $options = []): string
    {
        $html = $this->generateHtml($data, $options);
        return $this->exportFromHtml($html, $options);
    }

    /**
     * Export PDF from HTML content (for Blade templates)
     * 
     * @param string $html HTML content
     * @param array $options Export options
     * @return string PDF content
     */
    public function exportFromHtml(string $html, array $options = []): string
    {
        $optionsDom = new Options();
        $optionsDom->set('isHtml5ParserEnabled', true);
        $optionsDom->set('isRemoteEnabled', true);
        $optionsDom->set('defaultFont', $options['font'] ?? 'DejaVu Sans');

        $dompdf = new Dompdf($optionsDom);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($options['paper'] ?? 'A4', $options['orientation'] ?? 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    protected function generateHtml(array $data, array $options): string
    {
        $title = $options['title'] ?? 'Document';
        $font = $options['font'] ?? 'DejaVu Sans';
        
        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>';
        $html .= 'body { font-family: "' . $font . '", sans-serif; }';
        $html .= 'table { width: 100%; border-collapse: collapse; }';
        $html .= 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        $html .= 'thead { background-color: #f2f2f2; }';
        $html .= '</style></head><body>';
        $html .= "<h1>{$title}</h1>";
        $html .= '<table>';

        if (!empty($data)) {
            $firstRow = reset($data);
            $isAssociative = array_keys($firstRow) !== range(0, count($firstRow) - 1);

            $html .= '<thead><tr>';
            if ($isAssociative) {
                foreach (array_keys($firstRow) as $header) {
                    $html .= "<th>" . htmlspecialchars($header) . "</th>";
                }
            } else {
                foreach ($firstRow as $header) {
                    $html .= "<th>" . htmlspecialchars($header) . "</th>";
                }
                array_shift($data); // Remove the header row from data
            }
            $html .= '</tr></thead><tbody>';

            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= "<td>" . htmlspecialchars($cell) . "</td>";
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        $html .= '</table></body></html>';

        return $html;
    }
}
