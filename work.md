# ASSIGN.md — Next Update Tasks
### php-doc-exporter · Bangla Unicode & Quality Improvements

> Work through each task in order. Every task shows **what file to touch**,
> **what exactly to do**, and **why it matters**.

---

## TASK 1 — Add bundled Bangla font files
**Priority: CRITICAL — nothing else works without this**

### What to do
Create the `fonts/` directory at the repo root and add the NotoSansBengali TTF files.

```
php-doc-exporter/
└── fonts/                          ← CREATE THIS FOLDER
    ├── NotoSansBengali-Regular.ttf ← DOWNLOAD & ADD
    └── NotoSansBengali-Bold.ttf    ← DOWNLOAD & ADD
```

Download from: https://fonts.google.com/noto/specimen/Noto+Sans+Bengali  
Click **Download family** → unzip → copy the two `.ttf` files into `fonts/`.

### Why
`PdfExporter.php` (already updated) points to `__DIR__ . '/../../fonts/'`.  
Without the actual font files there, auto-detection works but Dompdf silently  
falls back to DejaVu Sans and Bangla still shows boxes.

---

## TASK 2 — Replace `src/Exporters/PdfExporter.php`
**Priority: CRITICAL**

### What to do
Replace the existing file with the new version already provided in this session.

Key changes inside the new file:
- `isBangla()` — static helper, detects Bangla Unicode (U+0980–U+09FF) in any string or array
- `resolveFontFamily()` — auto-picks `NotoSansBengali` when Bangla is detected; respects manual `$options['font']` override
- `registerBanglaFont()` — registers bundled TTF with Dompdf's FontMetrics at runtime
- `injectFontFace()` — inserts `@font-face` CSS into HTML `<head>` automatically
- `exportFromHtml()` — now sniffs Bangla from the raw HTML string (used by Blade path)

### Backward compatibility
Zero breaking changes. `export()` and `exportFromHtml()` signatures are identical.

---

## TASK 3 — Update `src/DocumentExporter.php`
**Priority: HIGH**

### What to do
In the `export()` method, remove any hardcoded `'DejaVu Sans'` default being  
passed into `PdfExporter`. The new `PdfExporter` resolves fonts internally.

Also add Bangla auto-detection for **Excel and Word** exports using the static helper:

```php
use Shoaib3375\PhpDocExporter\Exporters\PdfExporter;

// Inside export() before calling ExcelExporter / WordExporter:
if (PdfExporter::isBangla($data)) {
    $options['unicode'] = true; // signal to Excel/Word exporters
}
```

For `exportFromView()`, ensure the rendered HTML is passed through  
`PdfExporter::exportFromHtml($html, $options)` — not the old `export()` path.

---

## TASK 4 — Update `src/Exporters/ExcelExporter.php`
**Priority: HIGH**

### What to do
PhpSpreadsheet handles Unicode natively, but needs explicit UTF-8 configuration  
and a wider default column width for Bangla glyphs.

Add these inside the export method:

```php
// Set spreadsheet properties for Unicode
$spreadsheet->getProperties()
    ->setCreator('php-doc-exporter')
    ->setTitle($options['title'] ?? 'Report');

// Auto-size all columns (Bangla chars are wider)
foreach ($sheet->getColumnIterator() as $column) {
    $sheet->getColumnDimension($column->getColumnIndex())
          ->setAutoSize(true);
}

// Explicitly set default style font to support Unicode
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(12);
```

---

## TASK 5 — Update `src/Exporters/WordExporter.php`
**Priority: HIGH**

### What to do
PhpWord needs the default font set to a Unicode-compatible one for Bangla to  
render correctly when the `.docx` is opened in Microsoft Word or LibreOffice.

```php
// At the top of the export method, before adding sections:
$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->getSettings()->setThemeFontLang(
    new \PhpOffice\PhpWord\Style\Language(
        \PhpOffice\PhpWord\Style\Language::BN_BN  // Bengali locale
    )
);
$phpWord->setDefaultFontName('Vrinda');   // Windows Bangla font
$phpWord->setDefaultFontSize(12);
```

Also add a fallback font stack in the paragraph style:

```php
$section->addText(
    $cell,
    ['name' => 'Vrinda', 'size' => 11],
    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]
);
```

---

## TASK 6 — Update `src/Exporters/CsvExporter.php`
**Priority: MEDIUM**

### What to do
CSV already gets a UTF-8 BOM (`\xEF\xBB\xBF`) per the changelog, but verify  
it is applied consistently. If not, add it:

```php
// First line of the output string
$output = "\xEF\xBB\xBF"; // UTF-8 BOM — makes Excel open Bangla correctly

foreach ($data as $row) {
    $output .= implode(',', array_map(
        fn($v) => '"' . str_replace('"', '""', (string) $v) . '"',
        $row
    )) . "\r\n";
}
```

This makes the CSV open correctly in Microsoft Excel without manually  
selecting encoding — critical for Bangla end-users.

---

## TASK 7 — Update `composer.json`
**Priority: MEDIUM**

### What to do
Add `fonts/` to the `extra` autoload-files or include it in the package  
distribution so Composer does not strip it on install.

```json
"extra": {
    "laravel": {
        "providers": [
            "Shoaib3375\\PhpDocExporter\\PhpDocExporterServiceProvider"
        ]
    }
},
"autoload": {
    "psr-4": {
        "Shoaib3375\\PhpDocExporter\\": "src/"
    }
},
"include-path": ["fonts/"]
```

Also bump the version to `1.2.0` in `composer.json`:

```json
"version": "1.2.0"
```

---

## TASK 8 — Add test `tests/BanglaFontTest.php`
**Priority: MEDIUM**

### What to do
Create a new test file that covers the Bangla auto-detection logic:

```php
<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\Exporters\PdfExporter;

class BanglaFontTest extends TestCase
{
    public function test_detects_bangla_in_array(): void
    {
        $data = [['নাম' => 'শোয়েব', 'শহর' => 'ঢাকা']];
        $this->assertTrue(PdfExporter::isBangla($data));
    }

    public function test_detects_bangla_in_string(): void
    {
        $this->assertTrue(PdfExporter::isBangla('আমার সোনার বাংলা'));
    }

    public function test_no_false_positive_for_english(): void
    {
        $data = [['name' => 'Shoaib', 'city' => 'Dhaka']];
        $this->assertFalse(PdfExporter::isBangla($data));
    }

    public function test_pdf_export_returns_binary(): void
    {
        $exporter = new PdfExporter();
        $data = [['নাম' => 'শোয়েব', 'বয়স' => '২৫']];
        $pdf = $exporter->export($data, ['title' => 'টেস্ট']);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    public function test_pdf_export_english_still_works(): void
    {
        $exporter = new PdfExporter();
        $data = [['name' => 'Shoaib', 'age' => 25]];
        $pdf = $exporter->export($data);
        $this->assertStringStartsWith('%PDF', $pdf);
    }
}
```

---

## TASK 9 — Update `CHANGELOG.md`
**Priority: LOW**

### What to do
Add a new `[1.2.0]` section at the top of CHANGELOG.md:

```markdown
## [1.2.0] - 2026-04-11

### Added
- Bundled NotoSansBengali-Regular.ttf and NotoSansBengali-Bold.ttf fonts inside package
- Auto-detection of Bangla Unicode in data arrays and HTML strings (no config needed)
- `PdfExporter::isBangla()` public static helper method
- Runtime Dompdf font registration for bundled Bangla fonts
- Automatic `@font-face` CSS injection for Blade template exports
- UTF-8 BOM ensured for CSV exports (fixes Excel Bangla rendering)
- Bengali locale setting for Word exports
- Auto-sizing columns in Excel for Bangla content
- New test: `tests/BanglaFontTest.php`

### Changed
- `PdfExporter` no longer defaults to DejaVu Sans when Bangla is detected
- Font resolution priority: explicit option → auto-detected Bangla → DejaVu Sans
- `DocumentExporter` no longer passes hardcoded font to PdfExporter

### Fixed
- Bangla text showing as boxes (□□□) in generated PDFs
- Bangla column headers not displaying in Excel
- Word documents not opening correctly with Bangla content on Windows

### Breaking Changes
- None. All existing method signatures unchanged.
```

---

## TASK 10 — Update `README.md`
**Priority: LOW**

### What to do
Replace the current Bangla section with a simpler "it just works" message:

```markdown
## 🇧🇩 Bangla Unicode Support

All formats support Bangla Unicode **automatically** — no configuration needed.

The package detects Bangla characters in your data and applies the correct font.

### PDF
Bangla font (NotoSansBengali) is bundled inside the package — no manual font
installation required.

$data = [
    ['নাম' => 'শোয়েব', 'বয়স' => '২৫', 'শহর' => 'ঢাকা'],
];
$pdf = $exporter->export('pdf', $data); // font is chosen automatically

### Excel / Word / CSV
These formats use Unicode-native libraries and work out of the box.

### Manual font override (optional)
If you need a specific font, you can still pass it explicitly:

$pdf = $exporter->export('pdf', $data, ['font' => 'Kalpurush']);
```

---

## Summary — Files to Change

| # | File | Action |
|---|------|--------|
| 1 | `fonts/NotoSansBengali-Regular.ttf` | CREATE (download from Google Fonts) |
| 2 | `fonts/NotoSansBengali-Bold.ttf` | CREATE (download from Google Fonts) |
| 3 | `src/Exporters/PdfExporter.php` | REPLACE (already provided) |
| 4 | `src/DocumentExporter.php` | UPDATE (remove hardcoded font, add isBangla signal) |
| 5 | `src/Exporters/ExcelExporter.php` | UPDATE (auto-size columns, Unicode style) |
| 6 | `src/Exporters/WordExporter.php` | UPDATE (Bengali locale, Vrinda font default) |
| 7 | `src/Exporters/CsvExporter.php` | UPDATE (verify UTF-8 BOM is applied) |
| 8 | `composer.json` | UPDATE (version 1.2.0, include-path for fonts/) |
| 9 | `tests/BanglaFontTest.php` | CREATE (new test file) |
| 10 | `CHANGELOG.md` | UPDATE (add v1.2.0 entry) |
| 11 | `README.md` | UPDATE (simplify Bangla section) |

---

## Recommended Commit Order

```
git add fonts/
git commit -m "feat: bundle NotoSansBengali font files"

git add src/Exporters/PdfExporter.php
git commit -m "feat: auto-detect Bangla and register bundled font in PdfExporter"

git add src/DocumentExporter.php src/Exporters/ExcelExporter.php \
        src/Exporters/WordExporter.php src/Exporters/CsvExporter.php
git commit -m "feat: Bangla Unicode improvements across all exporters"

git add tests/BanglaFontTest.php
git commit -m "test: add BanglaFontTest coverage"

git add composer.json CHANGELOG.md README.md
git commit -m "chore: bump version to 1.2.0, update docs"

git tag v1.2.0
git push origin main --tags
```