# Changelog

All notable changes to `php-doc-exporter` will be documented in this file.

## [Unreleased]

### Added
- Custom exception classes for better error handling
- Laravel Service Provider for framework integration
- Configuration file support with .env variables
- Input validation for empty data arrays
- Comprehensive PHPDoc blocks
- Support for both associative and indexed arrays in Excel export
- UTF-8 BOM for CSV Unicode support

### Changed
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
