<?php

namespace Shoaib3375\PhpDocExporter\Exporters;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use Shoaib3375\PhpDocExporter\ExporterInterface;

class WordExporter implements ExporterInterface
{
    public function export(array $data, array $options = []): string
    {
        $phpWord = new PhpWord();

        // Set Bengali locale and default font for Bangla support
        $phpWord->getSettings()->setThemeFontLang(
            new Language(Language::BN_BN)
        );
        $phpWord->setDefaultFontName('Vrinda');
        $phpWord->setDefaultFontSize(12);

        $section = $phpWord->addSection();

        $title = $options['title'] ?? 'Document';
        $section->addTitle($title, 1);

        if (!empty($data)) {
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
            
            $headers = array_keys(reset($data));
            $table->addRow();
            foreach ($headers as $header) {
                $table->addCell(2000)->addText(
                    $header,
                    ['name' => 'Vrinda', 'size' => 11, 'bold' => true],
                    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]
                );
            }

            foreach ($data as $row) {
                $table->addRow();
                foreach ($row as $cell) {
                    $table->addCell(2000)->addText(
                        (string)$cell,
                        ['name' => 'Vrinda', 'size' => 11],
                        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]
                    );
                }
            }
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        
        ob_start();
        $objWriter->save('php://output');
        return ob_get_clean();
    }
}
