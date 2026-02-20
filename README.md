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

## 📖 Usage in Laravel

### 1. Basic Export in a Controller
Generate any format and return it as a download response.

```php
use Shoaib3375\PhpDocExporter\DocumentExporter;
use Illuminate\Http\Request;

public function exportData(Request $request)
{
    $data = [
        ['Name', 'Age', 'City'],
        ['Shoaib', 25, 'Dhaka'],
        ['মাইনুল', 30, 'Sylhet']
    ];

    $exporter = new DocumentExporter();
    
    // Formats supported: 'pdf', 'excel', 'word', 'csv'
    $format = $request->input('format', 'pdf'); 
    $fileName = 'report_' . time() . '.' . $this->getExtension($format);
    $filePath = storage_path('app/public/' . $fileName);

    $exporter->export($format, $data, $filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
}

private function getExtension($format) {
    return match($format) {
        'excel' => 'xlsx',
        'word'  => 'docx',
        default => $format
    };
}
```

---

## 🔒 Security & API Tokens (Optional)
The package includes an **optional** `Config` class for API token authentication. This is only needed if you're exposing document generation through an API and want to control access.


### Usage
```php
use Shoaib3375\PhpDocExporter\Config;

$config = new Config();

// Check if a token has full access (package create, edit, update)
if ($config->canAccessFullApi($providedToken)) {
    // Logic for main token
}

// Check if a token has safe access (package update, export)
if ($config->canAccessSafeApi($providedToken)) {
    // Logic for safe token
}
```

---

## 🇧🇩 Bangla Unicode Support
PDF generation in PHP often fails with Bangla characters (appearing as boxes or question marks). This package is pre-configured to use the `DejaVu Sans` font which renders Bangla correctly.

**Example Data:**
```php
$data = [
    ['নাম', 'বয়স'],
    ['শোয়েব', '২৫'],
    ['মাইনুল', '৩০']
];
```

---

## 📄 API Reference

### `DocumentExporter`
| Method | Description |
| :--- | :--- |
| `export(string $type, array $data, string $outputPath)` | Generates the document based on type and saves to path. |

### `Config`
| Method | Description |
| :--- | :--- |
| `getMainApiToken()` | Returns the primary secret token. |
| `getSafeApiToken()` | Returns the secondary less-sensitive token. |
| `canAccessFullApi(string $token)` | Returns true if token matches main token. |
| `canAccessSafeApi(string $token)` | Returns true if token matches either main or safe token. |

---

## 📜 License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
