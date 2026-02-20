# Testing Guide

## Running Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/BladeExportTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

## Manual Testing

### 1. Test Basic Array Export

```php
use Shoaib3375\PhpDocExporter\DocumentExporter;

$exporter = new DocumentExporter();
$data = [
    ['name' => 'Test User', 'email' => 'test@example.com'],
    ['name' => 'টেস্ট ইউজার', 'email' => 'test@example.bd']
];

$pdf = $exporter->export('pdf', $data);
file_put_contents('test-array.pdf', $pdf);

// Expected: PDF file with table containing 2 rows
```

### 2. Test Blade Template Export

Create `resources/views/test-invoice.blade.php`:
```blade
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
    </style>
</head>
<body>
    <h1>Test Invoice</h1>
    <p>Customer: {{ $customer }}</p>
    <p>Total: {{ $total }}</p>
</body>
</html>
```

Test code:
```php
$exporter = new DocumentExporter();
$pdf = $exporter->exportFromView('pdf', 'test-invoice', [
    'customer' => 'মোহাম্মদ শোয়েব',
    'total' => '2000 টাকা'
]);

file_put_contents('test-blade.pdf', $pdf);

// Expected: PDF with custom layout and Bangla text
```

### 3. Test Token Authentication

```php
// Set in .env
// PHP_DOC_EXPORTER_SAFE_TOKEN=test-token-123

$exporter = new DocumentExporter();
$data = [['name' => 'Test']];

// Should succeed
try {
    $pdf = $exporter->export('pdf', $data, [], 'test-token-123');
    echo "✓ Valid token accepted\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n";
}

// Should fail
try {
    $pdf = $exporter->export('pdf', $data, [], 'invalid-token');
    echo "✗ Invalid token accepted (should have failed)\n";
} catch (\Shoaib3375\PhpDocExporter\Exceptions\InvalidTokenException $e) {
    echo "✓ Invalid token rejected\n";
}
```

### 4. Test API Endpoint

Create test route in `routes/web.php`:
```php
Route::get('/test-export', function () {
    $exporter = new \Shoaib3375\PhpDocExporter\DocumentExporter();
    
    $pdf = $exporter->exportFromView('pdf', 'test-invoice', [
        'customer' => 'Test Customer',
        'total' => 1000
    ]);
    
    return response($pdf)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="test.pdf"');
});
```

Test in browser:
```
http://localhost:8000/test-export
```

Expected: PDF displays in browser

### 5. Test with cURL

```bash
# Test API endpoint
curl -X POST http://localhost:8000/api/export/invoice \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{"invoiceId": 1}' \
  --output test-api.pdf

# Check file
file test-api.pdf
# Expected: test-api.pdf: PDF document, version 1.7
```

## Automated Test Cases

### Test 1: HTML to PDF Conversion
```php
public function testHtmlToPdfConversion()
{
    $html = '<html><body><h1>Test</h1></body></html>';
    $exporter = new \Shoaib3375\PhpDocExporter\Exporters\PdfExporter();
    $pdf = $exporter->exportFromHtml($html);
    
    $this->assertNotEmpty($pdf);
    $this->assertStringStartsWith('%PDF', $pdf);
}
```

### Test 2: Bangla Character Support
```php
public function testBanglaCharacters()
{
    $html = '<html><head><meta charset="utf-8"/></head>
             <body style="font-family: DejaVu Sans;">
             <p>বাংলা টেক্সট</p></body></html>';
    
    $exporter = new \Shoaib3375\PhpDocExporter\Exporters\PdfExporter();
    $pdf = $exporter->exportFromHtml($html);
    
    $this->assertNotEmpty($pdf);
    $this->assertStringStartsWith('%PDF', $pdf);
}
```

### Test 3: Token Validation
```php
public function testValidToken()
{
    putenv('PHP_DOC_EXPORTER_SAFE_TOKEN=test-token');
    
    $exporter = new DocumentExporter();
    $data = [['name' => 'Test']];
    
    $pdf = $exporter->export('pdf', $data, [], 'test-token');
    $this->assertNotEmpty($pdf);
}

public function testInvalidToken()
{
    $this->expectException(InvalidTokenException::class);
    
    $exporter = new DocumentExporter();
    $data = [['name' => 'Test']];
    
    $exporter->export('pdf', $data, [], 'wrong-token');
}
```

### Test 4: PDF Options
```php
public function testPdfOptions()
{
    $html = '<html><body><h1>Test</h1></body></html>';
    $exporter = new \Shoaib3375\PhpDocExporter\Exporters\PdfExporter();
    
    $options = [
        'paper' => 'A4',
        'orientation' => 'landscape',
        'font' => 'DejaVu Sans'
    ];
    
    $pdf = $exporter->exportFromHtml($html, $options);
    $this->assertNotEmpty($pdf);
}
```

### Test 5: Empty Data Handling
```php
public function testEmptyDataThrowsException()
{
    $this->expectException(EmptyDataException::class);
    
    $exporter = new DocumentExporter();
    $exporter->export('pdf', []);
}
```

## Integration Testing

### Test Full API Flow

```php
// tests/Feature/ExportApiTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExportApiTest extends TestCase
{
    public function testInvoiceExportWithAuthentication()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/export/invoice', [
            'invoiceId' => 1
        ]);
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }
    
    public function testExportWithoutAuthentication()
    {
        $response = $this->postJson('/api/export/invoice', [
            'invoiceId' => 1
        ]);
        
        $response->assertStatus(401);
    }
}
```

## Performance Testing

### Test Export Speed

```php
$start = microtime(true);

$exporter = new DocumentExporter();
$data = array_fill(0, 100, ['name' => 'Test', 'value' => 100]);

$pdf = $exporter->export('pdf', $data);

$duration = microtime(true) - $start;
echo "Export time: " . round($duration * 1000) . "ms\n";

// Expected: < 1000ms for 100 rows
```

### Test Memory Usage

```php
$memStart = memory_get_usage();

$exporter = new DocumentExporter();
$data = array_fill(0, 1000, ['name' => 'Test', 'value' => 100]);

$pdf = $exporter->export('pdf', $data);

$memUsed = memory_get_usage() - $memStart;
echo "Memory used: " . round($memUsed / 1024 / 1024, 2) . "MB\n";

// Expected: < 50MB for 1000 rows
```

## Checklist

### Before Release
- [ ] All unit tests pass
- [ ] Integration tests pass
- [ ] Manual testing completed
- [ ] Bangla characters render correctly
- [ ] Token authentication works
- [ ] API endpoints respond correctly
- [ ] PDF files open without errors
- [ ] Excel/Word/CSV exports work
- [ ] Documentation is accurate
- [ ] Examples run successfully

### Test Environments
- [ ] PHP 8.1
- [ ] PHP 8.2
- [ ] PHP 8.3
- [ ] Laravel 10.x
- [ ] Laravel 11.x
- [ ] Windows
- [ ] Linux
- [ ] macOS

### Browser Testing (for API responses)
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

## Common Issues & Solutions

### Issue: Bangla characters show as boxes
**Solution**: Ensure DejaVu Sans font is used
```php
$options = ['font' => 'DejaVu Sans'];
```

### Issue: PDF is blank
**Solution**: Check HTML structure and charset
```html
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
```

### Issue: Token validation fails
**Solution**: Verify .env configuration
```bash
php artisan config:clear
php artisan cache:clear
```

### Issue: View not found
**Solution**: Check view path
```php
// Correct
$exporter->exportFromView('pdf', 'invoice', $data);

// For subdirectory
$exporter->exportFromView('pdf', 'reports.invoice', $data);
```

## Debugging Tips

### Enable Debug Mode
```php
// In PdfExporter
$options->set('debugPng', true);
$options->set('debugKeepTemp', true);
$options->set('debugCss', true);
```

### Log HTML Output
```php
$html = view('invoice', $data)->render();
file_put_contents('debug.html', $html);
// Open debug.html in browser to check rendering
```

### Check PDF Content
```bash
# Extract text from PDF
pdftotext test.pdf test.txt
cat test.txt
```

### Verify Token
```php
$config = new \Shoaib3375\PhpDocExporter\Config();
var_dump($config->getSafeApiToken());
var_dump($config->getMainApiToken());
```

## Test Data Sets

### Minimal Data
```php
$data = [['name' => 'Test']];
```

### Bangla Data
```php
$data = [
    ['নাম' => 'শোয়েব', 'শহর' => 'ঢাকা'],
    ['নাম' => 'মাইনুল', 'শহর' => 'সিলেট']
];
```

### Large Data
```php
$data = array_fill(0, 1000, [
    'id' => rand(1, 9999),
    'name' => 'User ' . rand(1, 1000),
    'email' => 'user' . rand(1, 1000) . '@example.com',
    'amount' => rand(100, 10000)
]);
```

### Mixed Content
```php
$data = [
    ['name' => 'English Name', 'city' => 'Dhaka'],
    ['name' => 'বাংলা নাম', 'city' => 'ঢাকা'],
    ['name' => 'Mixed নাম', 'city' => 'Mixed শহর']
];
```

## Continuous Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: ./vendor/bin/phpunit
```

## Test Coverage Goals

- Unit Tests: > 80%
- Integration Tests: > 70%
- API Tests: 100% of endpoints
- Edge Cases: All documented scenarios
