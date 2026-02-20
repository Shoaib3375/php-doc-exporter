<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shoaib3375\PhpDocExporter\DocumentExporter;

// Test 1: Default font (DejaVu Sans)
$data = [
    ['নাম' => 'শোয়েব', 'শহর' => 'ঢাকা'],
    ['নাম' => 'মাইনুল', 'শহর' => 'সিলেট']
];

$exporter = new DocumentExporter();

echo "Test 1: Default font (DejaVu Sans)\n";
$pdf1 = $exporter->export('pdf', $data);
file_put_contents(__DIR__ . '/test-default.pdf', $pdf1);
echo "Created: test-default.pdf\n\n";

// Test 2: Explicitly set DejaVu Sans
echo "Test 2: Explicit DejaVu Sans\n";
$pdf2 = $exporter->export('pdf', $data, ['font' => 'DejaVu Sans']);
file_put_contents(__DIR__ . '/test-dejavu.pdf', $pdf2);
echo "Created: test-dejavu.pdf\n\n";

// Test 3: Try with HTML directly
echo "Test 3: Direct HTML with Bangla\n";
$html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; }
    </style>
</head>
<body>
    <h1>বাংলা টেস্ট</h1>
    <p>নাম: শোয়েব</p>
    <p>শহর: ঢাকা</p>
    <table border="1">
        <tr><th>নাম</th><th>শহর</th></tr>
        <tr><td>শোয়েব</td><td>ঢাকা</td></tr>
        <tr><td>মাইনুল</td><td>সিলেট</td></tr>
    </table>
</body>
</html>';

$pdfExporter = new \Shoaib3375\PhpDocExporter\Exporters\PdfExporter();
$pdf3 = $pdfExporter->exportFromHtml($html);
file_put_contents(__DIR__ . '/test-html.pdf', $pdf3);
echo "Created: test-html.pdf\n\n";

echo "All tests complete. Check the PDF files to see Bangla rendering.\n";
echo "If you see boxes/squares, DejaVu Sans doesn't support those Bangla characters.\n";
