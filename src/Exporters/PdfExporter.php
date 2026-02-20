<?php

namespace Shoaib3375\PhpDocExporter\Exporters;

use Dompdf\Dompdf;
use Dompdf\Options;
use Shoaib3375\PhpDocExporter\ExporterInterface;

class PdfExporter implements ExporterInterface
{
    public function export(array $data, array $options = []): string
    {
        $optionsDom = new Options();
        $optionsDom->set('isHtml5ParserEnabled', true);
        $optionsDom->set('isRemoteEnabled', true);
        $optionsDom->set('defaultFont', $options['font'] ?? 'DejaVu Sans'); // DejaVu Sans supports Unicode

        $dompdf = new Dompdf($optionsDom);

        $html = $this->generateHtml($data, $options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($options['paper'] ?? 'A4', $options['orientation'] ?? 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    protected function generateHtml(array $data, array $options): string
    {
        $title = $options['title'] ?? 'Document';
        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>';
        $html .= 'body { font-family: "DejaVu Sans", sans-serif; }';
        $html .= 'table { width: 100%; border-collapse: collapse; }';
        $html .= 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        $html .= '</style></head><body>';
        $html .= "<h1>{$title}</h1>";
        $html .= '<table>';

        if (!empty($data)) {
            $headers = array_keys(reset($data));
            $html .= '<thead><tr>';
            foreach ($headers as $header) {
                $html .= "<th>{$header}</th>";
            }
            $html .= '</tr></thead><tbody>';

            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= "<td>{$cell}</td>";
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        $html .= '</table></body></html>';

        return $html;
    }
}
