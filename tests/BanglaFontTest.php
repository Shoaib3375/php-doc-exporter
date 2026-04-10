<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\Exporters\PdfExporter;

class BanglaFontTest extends TestCase
{
    public function test_detects_bangla_in_array(): void
    {
        $data = [['নাম' => 'শোয়েব', 'শহর' => 'ঢাকা']];
        $this->assertTrue(PdfExporter::isBangla($data));
    }

    public function test_detects_bangla_in_string(): void
    {
        $this->assertTrue(PdfExporter::isBangla('আমার সোনার বাংলা'));
    }

    public function test_no_false_positive_for_english(): void
    {
        $data = [['name' => 'Shoaib', 'city' => 'Dhaka']];
        $this->assertFalse(PdfExporter::isBangla($data));
    }

    public function test_pdf_export_returns_binary(): void
    {
        $exporter = new PdfExporter();
        $data = [['নাম' => 'শোয়েব', 'বয়স' => '২৫']];
        $pdf = $exporter->export($data, ['title' => 'টেস্ট']);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    public function test_pdf_export_english_still_works(): void
    {
        $exporter = new PdfExporter();
        $data = [['name' => 'Shoaib', 'age' => 25]];
        $pdf = $exporter->export($data);
        $this->assertStringStartsWith('%PDF', $pdf);
    }
}
