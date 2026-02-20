<?php

namespace Shoaib3375\PhpDocExporter\Exporters;

use Shoaib3375\PhpDocExporter\ExporterInterface;

class CsvExporter implements ExporterInterface
{
    public function export(array $data, array $options = []): string
    {
        $output = fopen('php://temp', 'r+');

        if (!empty($data)) {
            $headers = array_keys(reset($data));
            fputcsv($output, $headers);

            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return "\xEF\xBB\xBF" . $content;
    }
}
