# Bangla Font Support - Important Note

## Current Status

The package documentation mentions "DejaVu Sans" for Bangla support, but this is **incomplete**.

### DejaVu Sans Limitations
- ✅ Supports basic Latin characters
- ✅ Supports many Unicode characters
- ⚠️ **Limited Bangla support** - may show boxes for some Bangla characters
- ❌ Does not support all Bangla conjuncts and complex characters

## Recommended Fonts for Full Bangla Support

### Option 1: Noto Sans Bengali (Recommended)
```php
$options = ['font' => 'Noto Sans Bengali'];
$pdf = $exporter->export('pdf', $data, $options);
```

### Option 2: Kalpurush
```php
$options = ['font' => 'Kalpurush'];
$pdf = $exporter->export('pdf', $data, $options);
```

### Option 3: SolaimanLipi
```php
$options = ['font' => 'SolaimanLipi'];
$pdf = $exporter->export('pdf', $data, $options);
```

## How to Add Custom Fonts to Dompdf

### Step 1: Download Font
Download Noto Sans Bengali from: https://fonts.google.com/noto/specimen/Noto+Sans+Bengali

### Step 2: Install Font
```bash
# In your Laravel project
mkdir -p storage/fonts
# Copy .ttf files to storage/fonts/
```

### Step 3: Load Font in Dompdf
```php
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('fontDir', storage_path('fonts'));
$options->set('fontCache', storage_path('fonts'));
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Noto Sans Bengali');
```

### Step 4: Use in Blade Template
```blade
<style>
    @font-face {
        font-family: 'Noto Sans Bengali';
        src: url('{{ storage_path("fonts/NotoSansBengali-Regular.ttf") }}');
    }
    body {
        font-family: 'Noto Sans Bengali', sans-serif;
    }
</style>
```

## Quick Fix for Current Package

### Update PdfExporter Default
The package currently defaults to "DejaVu Sans". For better Bangla support, always specify a proper Bangla font:

```php
$exporter = new DocumentExporter();
$pdf = $exporter->export('pdf', $data, [
    'font' => 'Noto Sans Bengali'  // Specify Bangla-compatible font
]);
```

## Testing Bangla Rendering

```php
$data = [
    ['নাম' => 'শোয়েব', 'ঠিকানা' => 'ঢাকা'],
    ['নাম' => 'মাইনুল', 'ঠিকানা' => 'সিলেট']
];

// Test with different fonts
$fonts = ['DejaVu Sans', 'Noto Sans Bengali', 'Kalpurush'];

foreach ($fonts as $font) {
    $pdf = $exporter->export('pdf', $data, ['font' => $font]);
    file_put_contents("test-{$font}.pdf", $pdf);
}
```

## Conclusion

**For production Bangla PDFs:**
- ❌ Don't rely on DejaVu Sans alone
- ✅ Use Noto Sans Bengali or other dedicated Bangla fonts
- ✅ Test with actual Bangla content before deployment
- ✅ Include font files in your project

## Documentation Update Needed

The README and examples should be updated to:
1. Clarify DejaVu Sans limitations
2. Recommend proper Bangla fonts
3. Provide font installation instructions
4. Show how to configure custom fonts
