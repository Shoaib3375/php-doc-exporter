<?php

require_once __DIR__ . '/Illuminate/Http/Response.php';

// Test the Response class
$response = new \Illuminate\Http\Response('PDF content here');
$response->header('Content-Type', 'application/pdf')
         ->header('Content-Disposition', 'attachment; filename="test.pdf"');

echo "Content: " . $response->getContent() . "\n";
echo "Headers:\n";
foreach ($response->getHeaders() as $key => $value) {
    echo "  $key: $value\n";
}
