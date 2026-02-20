# PHP Doc Exporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shoaib3375/php-doc-exporter.svg?style=flat-square)](https://packagist.org/packages/shoaib3375/php-doc-exporter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

An all-in-one PHP/Laravel library to generate professional documents in **PDF, Excel, Word, and CSV** formats with specialized support for **Bangla Unicode** and built-in **API Token Security**.

## 🚀 Features
- **Bangla Support**: Seamlessly handles Bangla Unicode characters in PDFs using `DejaVu Sans`.
- **Multi-Format**: One interface for 4 major document types.
- **Secure**: Built-in logic for Main and Safe API tokens for external integrations.
- **Performant**: Optimized for speed and minimal memory footprint.

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

---

## 🎨 Advanced Options

### PDF Customization
```php
$options = [
    'title' => 'Sales Report',
    'paper' => 'A4',           // A4, Letter, Legal
    'orientation' => 'landscape', // portrait or landscape
    'font' => 'DejaVu Sans'    // Default font for Unicode support
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
All formats (PDF, Excel, Word, CSV) fully support Bangla and other Unicode characters.

**PDF** uses `DejaVu Sans` font by default to render Bangla correctly (no boxes or question marks).

```php
$data = [
    ['নাম' => 'শোয়েব', 'বয়স' => '২৫', 'শহর' => 'ঢাকা'],
    ['নাম' => 'মাইনুল', 'বয়স' => '৩০', 'শহর' => 'সিলেট']
];

$content = $exporter->export('pdf', $data);
file_put_contents('bangla-report.pdf', $content);
```

---

## 📄 API Reference

### `DocumentExporter`

#### `export(string $format, array $data, array $options = [], string $token = null): string`
Generates document content and returns as string.

**Parameters:**
- `$format` - Format type: `'pdf'`, `'excel'`, `'word'`, `'csv'`
- `$data` - Associative array of data (keys = headers)
- `$options` - Optional settings:
  - `title` - Document title
  - `paper` - PDF paper size (A4, Letter, Legal)
  - `orientation` - PDF orientation (portrait, landscape)
  - `font` - PDF font (default: DejaVu Sans)
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
