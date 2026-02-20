<?php

namespace Shoaib3375\PhpDocExporter\Exporters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Shoaib3375\PhpDocExporter\ExporterInterface;

class ExcelExporter implements ExporterInterface
{
    public function export(array $data, array $options = []): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($data)) {
            $headers = array_keys(reset($data));
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            $rowIdx = 2;
            foreach ($data as $row) {
                $col = 'A';
                foreach ($row as $cell) {
                    $sheet->setCellValue($col . $rowIdx, $cell);
                    $col++;
                }
                $rowIdx++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
