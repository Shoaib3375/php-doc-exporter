<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\DocumentExporter;
use InvalidArgumentException;

class DocumentExporterTest extends TestCase
{
    private DocumentExporter $exporter;
    private string $mainToken = '903352ea22c8ab26bf76ee18a452b3377d2a7d5c';
    private string $safeToken = '5de6f212bc0320ed82a4eeb914115b9f450625f6';

    protected function setUp(): void
    {
        $this->exporter = new DocumentExporter();
    }

    public function testCsvExportWorksWithSafeToken()
    {
        $data = [['Name' => 'Shoaib', 'Email' => 'mesta@example.com']];
        $result = $this->exporter->export('csv', $data, [], $this->safeToken);
        $this->assertStringContainsString('Shoaib', $result);
    }

    public function testCsvExportWorksWithMainToken()
    {
        $data = [['Name' => 'Shoaib', 'Email' => 'mesta@example.com']];
        $result = $this->exporter->export('csv', $data, [], $this->mainToken);
        $this->assertStringContainsString('Shoaib', $result);
    }

    public function testExportFailsWithWrongToken()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->exporter->export('csv', [], [], 'wrong-token');
    }

    public function testPackageCreationRequiresMainToken()
    {
        $this->assertTrue($this->exporter->createPackage('Test Package', $this->mainToken));

        $this->expectException(InvalidArgumentException::class);
        $this->exporter->createPackage('Test Package', $this->safeToken);
    }

    public function testPackageUpdateWorksWithSafeToken()
    {
        $this->assertTrue($this->exporter->updatePackage('1', [], $this->safeToken));
        $this->assertTrue($this->exporter->updatePackage('1', [], $this->mainToken));
    }

    public function testBanglaUnicodeSupportInPdf()
    {
        // This only checks if it runs without error, as parsing PDF content is complex
        $data = [['Name' => 'সোহেব', 'Email' => 'mesta@example.com']];
        $result = $this->exporter->export('pdf', $data, ['title' => 'Bangla রিপোর্ট'], $this->safeToken);
        $this->assertNotEmpty($result);
    }
}
