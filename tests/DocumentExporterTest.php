<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\DocumentExporter;
use Shoaib3375\PhpDocExporter\Config;
use Shoaib3375\PhpDocExporter\Exceptions\InvalidTokenException;
use Shoaib3375\PhpDocExporter\Exceptions\EmptyDataException;
use Shoaib3375\PhpDocExporter\Exceptions\InvalidFormatException;

class DocumentExporterTest extends TestCase
{
    private DocumentExporter $exporter;
    private string $mainToken = 'test-main-token';
    private string $safeToken = 'test-safe-token';

    protected function setUp(): void
    {
        $config = new Config($this->mainToken, $this->safeToken);
        $this->exporter = new DocumentExporter($config);
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
        $this->expectException(InvalidTokenException::class);
        $this->exporter->export('csv', [['test' => 'data']], [], 'wrong-token');
    }

    public function testExportFailsWithEmptyData()
    {
        $this->expectException(EmptyDataException::class);
        $this->exporter->export('csv', []);
    }

    public function testExportFailsWithInvalidFormat()
    {
        $this->expectException(InvalidFormatException::class);
        $this->exporter->export('invalid', [['test' => 'data']]);
    }

    public function testPackageCreationRequiresMainToken()
    {
        $this->assertTrue($this->exporter->createPackage('Test Package', $this->mainToken));

        $this->expectException(InvalidTokenException::class);
        $this->exporter->createPackage('Test Package', $this->safeToken);
    }

    public function testPackageUpdateWorksWithSafeToken()
    {
        $this->assertTrue($this->exporter->updatePackage('1', [], $this->safeToken));
        $this->assertTrue($this->exporter->updatePackage('1', [], $this->mainToken));
    }

    public function testBanglaUnicodeSupportInPdf()
    {
        $data = [['Name' => 'সোহেব', 'Email' => 'mesta@example.com']];
        $result = $this->exporter->export('pdf', $data, ['title' => 'Bangla রিপোর্ট'], $this->safeToken);
        $this->assertNotEmpty($result);
    }
}
