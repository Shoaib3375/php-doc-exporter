<?php

// NOTE: This file is an example for Laravel applications.
// Copy to your Laravel project's routes/api.php file.

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

// Protected API routes with token authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // Export invoice using Blade template
    Route::post('/export/invoice', [ExportController::class, 'exportInvoice']);
    
    // Export report using array data
    Route::post('/export/report', [ExportController::class, 'exportReport']);
    
});

// Public routes (if needed)
function response(string $content)
{
    return new \Illuminate\Http\Response($content);
}

Route::post('/export/public', function () {
    $data = [
        ['name' => 'John', 'email' => 'john@example.com'],
    ];
    
    $exporter = new \Shoaib3375\PhpDocExporter\DocumentExporter();
    $content = $exporter->export('pdf', $data);
    
    return response($content)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="export.pdf"');
});
