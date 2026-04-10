# Changelog

All notable changes to `php-doc-exporter` will be documented in this file.

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

## [Unreleased]

### Added
- **Blade Template Support**: New `exportFromView()` method for Laravel Blade templates
- **HTML to PDF Export**: Direct HTML to PDF conversion with `exportFromHtml()` method
- **API Integration Examples**: Complete API controller and route examples
- **Comprehensive Documentation**: New BLADE_API_GUIDE.md with usage examples
- **Example Templates**: Sample Blade templates for invoices and reports
- Custom exception classes for better error handling
- Laravel Service Provider for framework integration
- Configuration file support with .env variables
- Input validation for empty data arrays
- Comprehensive PHPDoc blocks
- Support for both associative and indexed arrays in Excel export
- UTF-8 BOM for CSV Unicode support

### Changed
- Refactored PdfExporter to separate HTML generation from PDF rendering
- Enhanced README with Blade template usage and API examples
- Removed hardcoded API tokens (security fix)
- Tokens now configurable via environment variables
- Improved error messages with specific exception types

### Security
- Fixed security vulnerability with exposed API tokens
- Added .gitignore to prevent committing sensitive files

## [1.0.0] - Initial Release

### Added
- PDF export with Bangla Unicode support
- Excel export (XLSX format)
- Word export (DOCX format)
- CSV export
- API token authentication system
- Multi-format document generation
