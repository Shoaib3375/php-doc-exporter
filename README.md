# PHP Doc Exporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shoaib3375/php-doc-exporter.svg?style=flat-square)](https://packagist.org/packages/shoaib3375/php-doc-exporter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

An all-in-one PHP/Laravel library to generate professional documents in **PDF, Excel, Word, and CSV** formats with specialized support for **Bangla Unicode** and built-in **API Token Security**.

## 🚀 Features
- **Blade Template Support**: Use Laravel Blade views for custom PDF layouts
  - **Bangla Support**: Auto-detects Bangla Unicode and switches to bundled NotoSansBengali font automatically.
  - **Multi-Format**: One interface for 4 major document types.
  - **Secure**: Built-in logic for Main and Safe API tokens for external integrations.
  - **Performant**: Optimized for speed and minimal memory footprint.
  - **API Ready**: Perfect for REST APIs with token-based authentication

---

## 🛠 Installation

You can install the package via composer:

```bash
composer require shoaib3375/php-doc-exporter
```

### Laravel Setup (Optional)

The package auto-registers in Laravel. Optionally publish the config:

```bash
php artisan vendor:publish --provider="Shoaib3375\PhpDocExporter\PhpDocExporterServiceProvider"
```

Then configure tokens in `.env`:
```env
PHP_DOC_EXPORTER_MAIN_TOKEN=your-main-token
PHP_DOC_EXPORTER_SAFE_TOKEN=your-safe-token
```

## 📖 Quick Start

### Basic Usage (Plain PHP)
```php
use Shoaib3375\PhpDocExporter\DocumentExporter;

$data = [
    ['name' => 'Shoaib', 'age' => 25, 'city' => 'Dhaka'],
    ['name' => 'মাইনুল', 'age' => 30, 'city' => 'Sylhet']
];

$exporter = new DocumentExporter();
$content = $exporter->export('pdf', $data);

// Save to file
file_put_contents('report.pdf', $content);
```

### Laravel Controller Example
```php
use Shoaib3375\PhpDocExporter\DocumentExporter;
use Illuminate\Http\Request;

public function export(Request $request)
{
    $data = [
        ['name' => 'Shoaib', 'age' => 25, 'city' => 'Dhaka'],
        ['name' => 'মাইনুল', 'age' => 30, 'city' => 'Sylhet']
    ];

    $exporter = new DocumentExporter();
    $format = $request->input('format', 'pdf'); // pdf, excel, word, csv
    
    $content = $exporter->export($format, $data);
    
    $extension = match($format) {
        'excel' => 'xlsx',
        'word' => 'docx',
        default => $format
    };
    
    return response($content)
        ->header('Content-Type', $this->getMimeType($format))
        ->header('Content-Disposition', 'attachment; filename="report.' . $extension . '"');
}

private function getMimeType($format) {
    return match($format) {
        'pdf' => 'application/pdf',
        'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'word' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'csv' => 'text/csv',
    };
}
```

### Using Blade Templates (NEW)
```php
use Shoaib3375\PhpDocExporter\DocumentExporter;

public function exportInvoice(Request $request)
{
    $data = [
        'title' => 'Sales Invoice',
        'invoiceNumber' => 'INV-2024-001',
        'customerName' => 'মোহাম্মদ শোয়েব',
        'items' => [
            ['name' => 'Product A', 'quantity' => 2, 'price' => 500],
            ['name' => 'পণ্য বি', 'quantity' => 1, 'price' => 1000],
        ],
        'total' => 2000
    ];

    $exporter = new DocumentExporter();
    $content = $exporter->exportFromView('pdf', 'invoice', $data, [
        'paper' => 'A4',
        'orientation' => 'portrait'
    ]);

    return response($content)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="invoice.pdf"');
}
```

### API with Token Authentication
```php
Route::middleware('auth:sanctum')->post('/export/invoice', function (Request $request) {
    $token = $request->bearerToken();
    
    $data = ['title' => 'Invoice', 'items' => [...]];
    
    $exporter = new DocumentExporter();
    $content = $exporter->exportFromView('pdf', 'invoice', $data, [], $token);
    
    return response($content)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="invoice.pdf"');
});
```

---

## 🎨 Blade Template Support

### Creating a Blade Template
Create a view file at `resources/views/invoice.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Invoice #{{ $invoiceNumber }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ $item['price'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p><strong>Total: {{ $total }}</strong></p>
</body>
</html>
```

### Using the Template
```php
$exporter = new DocumentExporter();
$content = $exporter->exportFromView('pdf', 'invoice', [
    'title' => 'Sales Invoice',
    'invoiceNumber' => 'INV-001',
    'items' => [...],
    'total' => 2000
]);
```

### Benefits
- **Custom Layouts**: Design complex documents with full HTML/CSS control
  - **Reusable Templates**: Share templates across your application
  - **Dynamic Content**: Use Blade directives (@if, @foreach, @include)
  - **Bangla Support**: Full Unicode support in templates

---

## 🎨 Advanced Options

### PDF Customization
```php
$options = [
    'title' => 'Sales Report',
    'paper' => 'A4',           // A4, Letter, Legal
    'orientation' => 'landscape', // portrait or landscape
    'font' => 'NotoSansBengali' // Optional: override auto-detected font
];

$content = $exporter->export('pdf', $data, $options);
```

### Word/Excel Customization
```php
$options = [
    'title' => 'Monthly Report'
];

$content = $exporter->export('word', $data, $options);
```

### Data Format
Use **associative arrays** where keys become column headers:
```php
$data = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'জন', 'email' => 'john@example.bd']
];
```

---

## 🔒 API Token Security (Optional)

### Using Tokens for Protected Exports
```php
$exporter = new DocumentExporter();

// Export with token validation
$content = $exporter->export('pdf', $data, [], $apiToken);
// Throws InvalidArgumentException if token is invalid
```

### Custom Token Configuration
```php
use Shoaib3375\PhpDocExporter\Config;

$config = new Config();

// Validate tokens
if ($config->canAccessFullApi($token)) {
    // Full access: create, edit, update
}

if ($config->canAccessSafeApi($token)) {
    // Safe access: export only
}
```

---

## 🇧🇩 Bangla Unicode Support

All formats support Bangla Unicode **automatically** — no configuration needed.

The package detects Bangla characters in your data and applies the correct font.

### PDF
Bangla font (NotoSansBengali) is bundled inside the package — no manual font installation required.

```php
$data = [
    ['নাম' => 'শোয়েব', 'বয়স' => '২৫', 'শহর' => 'ঢাকা'],
];
$pdf = $exporter->export('pdf', $data); // font is chosen automatically
```

### Excel / Word / CSV
These formats use Unicode-native libraries and work out of the box.

### Manual font override (optional)
If you need a specific font, you can still pass it explicitly:

```php
$pdf = $exporter->export('pdf', $data, ['font' => 'Kalpurush']);
```

---

## 📄 API Reference

### `DocumentExporter`

#### `exportFromView(string $format, string $view, array $data = [], array $options = [], string $token = null): string`
Generates PDF from Blade template (Laravel only).

**Parameters:**
- `$format` - Currently only `'pdf'` supported for views
  - `$view` - Blade view name (e.g., `'invoice'` for `resources/views/invoice.blade.php`)
  - `$data` - Data to pass to the Blade view
  - `$options` - Optional settings:
    - `paper` - PDF paper size (A4, Letter, Legal)
    - `orientation` - PDF orientation (portrait, landscape)
    - `font` - PDF font (auto-detected: Bangla → NotoSansBengali, else DejaVu Sans)
  - `$token` - Optional API token for validation

**Returns:** PDF content as string

**Throws:** `InvalidFormatException` or `InvalidTokenException`

---

#### `export(string $format, array $data, array $options = [], string $token = null): string`
Generates document content and returns as string.

**Parameters:**
- `$format` - Format type: `'pdf'`, `'excel'`, `'word'`, `'csv'`
  - `$data` - Associative array of data (keys = headers)
  - `$options` - Optional settings:
    - `title` - Document title
    - `paper` - PDF paper size (A4, Letter, Legal)
    - `orientation` - PDF orientation (portrait, landscape)
    - `font` - PDF font (auto-detected: Bangla → NotoSansBengali, else DejaVu Sans)
  - `$token` - Optional API token for validation

**Returns:** Document content as string

**Throws:** `InvalidArgumentException` for invalid format or token

---

### `Config`

| Method | Description |
| :--- | :--- |
| `canAccessFullApi(string $token): bool` | Validates main API token |
| `canAccessSafeApi(string $token): bool` | Validates safe or main token |
| `getMainApiToken(): string` | Returns primary token |
| `getSafeApiToken(): string` | Returns secondary token |

---

## 📜 License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
