# Changelog

All notable changes to `php-doc-exporter` will be documented in this file.

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
