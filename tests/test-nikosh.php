<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shoaib3375\PhpDocExporter\DocumentExporter;

$data = [
    ['নাম' => 'শোয়েব', 'শহর' => 'ঢাকা'],
    ['নাম' => 'মাইনুল', 'শহর' => 'সিলেট']
];

$exporter = new DocumentExporter();

// Test with nikosh font
echo "Testing with nikosh font (better Bangla support)...\n";
$pdf = $exporter->export('pdf', $data, ['font' => 'nikosh']);
file_put_contents(__DIR__ . '/test-nikosh.pdf', $pdf);
echo "Created: test-nikosh.pdf\n";
echo "Open this file to check if Bangla renders correctly.\n";
