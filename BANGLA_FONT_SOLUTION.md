# Bangla Font Issue - Solution

## The Problem

DejaVu Sans (the default font in Dompdf) has **limited Bangla Unicode support**. Many Bangla characters will appear as boxes or squares.

## Immediate Solutions

### Solution 1: Use Nikosh Font (Recommended - Works Out of Box)

Nikosh is included with Dompdf and has better Bangla support:

```php
$exporter = new DocumentExporter();
$pdf = $exporter->export('pdf', $data, [
    'font' => 'nikosh'  // Use lowercase
]);
```

### Solution 2: Install Proper Bangla Fonts

For full Bangla support, install these fonts in Dompdf:

#### Step 1: Download Fonts
- **Kalpurush**: https://www.omicronlab.com/kalpurush-download.html
- **SolaimanLipi**: https://www.omicronlab.com/solaimanlipi-download.html
- **Noto Sans Bengali**: https://fonts.google.com/noto/specimen/Noto+Sans+Bengali

#### Step 2: Install Font in Dompdf

```bash
# Navigate to dompdf directory
cd vendor/dompdf/dompdf

# Load font (example with Kalpurush)
php load_font.php Kalpurush /path/to/Kalpurush.ttf
```

#### Step 3: Use the Font

```php
$pdf = $exporter->export('pdf', $data, [
    'font' => 'Kalpurush'
]);
```

## Quick Test

```php
<?php
require 'vendor/autoload.php';

use Shoaib3375\PhpDocExporter\DocumentExporter;

$data = [
    ['নাম' => 'শোয়েব', 'ঠিকানা' => 'ঢাকা'],
    ['নাম' => 'মাইনুল', 'ঠিকানা' => 'সিলেট']
];

$exporter = new DocumentExporter();

// Test with nikosh (should work better)
$pdf = $exporter->export('pdf', $data, ['font' => 'nikosh']);
file_put_contents('bangla-nikosh.pdf', $pdf);

echo "PDF created with nikosh font\n";
```

## For Blade Templates

```blade
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'nikosh', sans-serif; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>গ্রাহক: {{ $customerName }}</p>
</body>
</html>
```

## Recommended Default

Update your code to always specify a Bangla-compatible font:

```php
// In your controller or service
class DocumentService
{
    private function getDefaultOptions(): array
    {
        return [
            'font' => 'nikosh',  // Better Bangla support
            'paper' => 'A4',
            'orientation' => 'portrait'
        ];
    }
    
    public function exportPdf($data)
    {
        $exporter = new DocumentExporter();
        return $exporter->export('pdf', $data, $this->getDefaultOptions());
    }
}
```

## Font Comparison

| Font | Bangla Support | Availability | Recommendation |
|------|---------------|--------------|----------------|
| DejaVu Sans | ⚠️ Limited | Default | ❌ Not for Bangla |
| nikosh | ✅ Good | Included with Dompdf | ✅ Use this |
| Kalpurush | ✅ Excellent | Need to install | ✅ Best quality |
| SolaimanLipi | ✅ Excellent | Need to install | ✅ Best quality |
| Noto Sans Bengali | ✅ Excellent | Need to install | ✅ Best quality |

## Verify Font Installation

```php
<?php
// Check available fonts in Dompdf
$fontDir = __DIR__ . '/vendor/dompdf/dompdf/lib/fonts';
$fonts = scandir($fontDir);
foreach ($fonts as $font) {
    if (strpos($font, '.ttf') !== false || strpos($font, '.ufm') !== false) {
        echo $font . "\n";
    }
}
```

## Bottom Line

**Don't use DejaVu Sans for Bangla content.** Use `nikosh` (included) or install a proper Bangla font.
