<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\DocumentExporter;
use Shoaib3375\PhpDocExporter\Exceptions\InvalidTokenException;

class BladeExportTest extends TestCase
{
    public function testExportFromHtmlDirectly()
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>body { font-family: "DejaVu Sans", sans-serif; }</style>
        </head>
        <body>
            <h1>Test Invoice</h1>
            <p>Customer: মোহাম্মদ শোয়েব</p>
            <table>
                <tr><th>Item</th><th>Price</th></tr>
                <tr><td>Product A</td><td>500</td></tr>
            </table>
        </body>
        </html>';

        $exporter = new DocumentExporter();
        $pdfExporter = new \Shoaib3375\PhpDocExporter\Exporters\PdfExporter();
        
        $content = $pdfExporter->exportFromHtml($html, [
            'paper' => 'A4',
            'orientation' => 'portrait'
        ]);

        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function testExportWithInvalidToken()
    {
        $this->expectException(InvalidTokenException::class);

        $exporter = new DocumentExporter();
        $data = [['name' => 'Test', 'value' => 100]];
        
        $exporter->export('pdf', $data, [], 'invalid-token');
    }

    public function testExportFromHtmlWithBanglaCharacters()
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>body { font-family: "DejaVu Sans", sans-serif; }</style>
        </head>
        <body>
            <h1>বাংলা টেস্ট</h1>
            <p>নাম: শোয়েব</p>
            <p>শহর: ঢাকা</p>
        </body>
        </html>';

        $pdfExporter = new \Shoaib3375\PhpDocExporter\Exporters\PdfExporter();
        $content = $pdfExporter->exportFromHtml($html);

        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF', $content);
    }
}
