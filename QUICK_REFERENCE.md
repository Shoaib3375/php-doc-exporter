# Quick Reference Guide

## Installation
```bash
composer require shoaib3375/php-doc-exporter
```

## Basic Usage

### Array Export (All Formats)
```php
use Shoaib3375\PhpDocExporter\DocumentExporter;

$exporter = new DocumentExporter();
$data = [
    ['name' => 'John', 'age' => 25],
    ['name' => 'জন', 'age' => 30]
];

// PDF
$pdf = $exporter->export('pdf', $data);

// Excel
$excel = $exporter->export('excel', $data);

// Word
$word = $exporter->export('word', $data);

// CSV
$csv = $exporter->export('csv', $data);
```

### Blade Template Export (PDF Only)
```php
// Create view: resources/views/invoice.blade.php
$exporter = new DocumentExporter();
$pdf = $exporter->exportFromView('pdf', 'invoice', [
    'title' => 'Invoice',
    'items' => [...]
]);
```

## API Usage

### Controller
```php
public function export(Request $request)
{
    $token = $request->bearerToken();
    $exporter = new DocumentExporter();
    
    // With Blade template
    $content = $exporter->exportFromView(
        'pdf', 
        'invoice', 
        $data, 
        ['paper' => 'A4'], 
        $token
    );
    
    return response($content)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="doc.pdf"');
}
```

### Routes
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/export', [ExportController::class, 'export']);
});
```

## Options

### PDF Options
```php
$options = [
    'title' => 'Document Title',
    'paper' => 'A4',              // A4, Letter, Legal
    'orientation' => 'portrait',  // portrait, landscape
    'font' => 'DejaVu Sans'       // For Bangla support
];
```

### Excel/Word Options
```php
$options = [
    'title' => 'Document Title'
];
```

## Token Authentication

### Setup (.env)
```env
PHP_DOC_EXPORTER_MAIN_TOKEN=main-token-here
PHP_DOC_EXPORTER_SAFE_TOKEN=safe-token-here
```

### Usage
```php
// With token validation
$content = $exporter->export('pdf', $data, [], $token);

// Check token manually
$config = new \Shoaib3375\PhpDocExporter\Config();
if ($config->canAccessSafeApi($token)) {
    // Export allowed
}
```

## Blade Template Tips

### Required Meta Tag
```html
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
```

### Font for Bangla
```css
body { font-family: 'DejaVu Sans', sans-serif; }
```

### Inline CSS Only
```html
<style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 8px; }
</style>
```

## Common Patterns

### Download Response
```php
return response($content)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="file.pdf"');
```

### Inline Display
```php
return response($content)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="file.pdf"');
```

### Save to File
```php
file_put_contents('document.pdf', $content);
```

## Error Handling

```php
use Shoaib3375\PhpDocExporter\Exceptions\{
    EmptyDataException,
    InvalidFormatException,
    InvalidTokenException
};

try {
    $content = $exporter->export('pdf', $data, [], $token);
} catch (EmptyDataException $e) {
    // Handle empty data
} catch (InvalidFormatException $e) {
    // Handle invalid format
} catch (InvalidTokenException $e) {
    // Handle invalid token
}
```

## MIME Types

| Format | MIME Type | Extension |
|--------|-----------|-----------|
| PDF | application/pdf | .pdf |
| Excel | application/vnd.openxmlformats-officedocument.spreadsheetml.sheet | .xlsx |
| Word | application/vnd.openxmlformats-officedocument.wordprocessingml.document | .docx |
| CSV | text/csv | .csv |

## Examples Location

- Blade Templates: `examples/views/`
- Controllers: `examples/ExportController.php`
- Routes: `examples/api-routes.php`
- Full Guide: `BLADE_API_GUIDE.md`
