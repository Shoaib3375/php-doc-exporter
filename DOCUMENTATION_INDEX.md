# Documentation Index

Welcome to the PHP Doc Exporter documentation! This guide will help you find the information you need quickly.

## 📚 Quick Navigation

### Getting Started
1. **[README.md](README.md)** - Start here!
   - Installation instructions
   - Quick start guide
   - Basic usage examples
   - Feature overview

2. **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick lookup
   - Common code snippets
   - API reference
   - MIME types
   - Error handling patterns

### Implementation Guides

3. **[BLADE_API_GUIDE.md](BLADE_API_GUIDE.md)** - Complete implementation guide
   - Step-by-step setup
   - Blade template creation
   - API controller examples
   - Security best practices
   - cURL, JavaScript, Postman examples
   - Troubleshooting

4. **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - What's new
   - New features overview
   - File structure
   - Usage examples
   - Migration guide
   - Benefits

### Technical Documentation

5. **[ARCHITECTURE.md](ARCHITECTURE.md)** - System design
   - Architecture diagrams
   - Request flow
   - Component interaction
   - Data flow
   - Deployment architecture

6. **[TESTING_GUIDE.md](TESTING_GUIDE.md)** - Testing instructions
   - Running tests
   - Manual testing
   - Automated tests
   - Integration testing
   - Performance testing
   - Debugging tips

### Project Information

7. **[CHANGELOG.md](CHANGELOG.md)** - Version history
   - New features
   - Changes
   - Security fixes

8. **[CONTRIBUTING.md](CONTRIBUTING.md)** - Contribution guidelines
   - How to contribute
   - Code standards
   - Pull request process

## 🎯 Find What You Need

### I want to...

#### Install the package
→ [README.md - Installation](README.md#-installation)

#### Export a simple PDF from array data
→ [README.md - Basic Usage](README.md#-quick-start)
→ [QUICK_REFERENCE.md - Array Export](QUICK_REFERENCE.md#array-export-all-formats)

#### Use Blade templates for custom PDFs
→ [README.md - Blade Template Support](README.md#-blade-template-support)
→ [BLADE_API_GUIDE.md - Creating Templates](BLADE_API_GUIDE.md#3-create-blade-template)

#### Build an API with token authentication
→ [BLADE_API_GUIDE.md - API Implementation](BLADE_API_GUIDE.md#api-implementation)
→ [README.md - API with Token Authentication](README.md#api-with-token-authentication)

#### Handle Bangla/Unicode characters
→ [README.md - Bangla Unicode Support](README.md#-bangla-unicode-support)
→ [BLADE_API_GUIDE.md - Bangla Support](BLADE_API_GUIDE.md#bangla-characters-showing-as-boxes)

#### Understand the architecture
→ [ARCHITECTURE.md](ARCHITECTURE.md)

#### Test my implementation
→ [TESTING_GUIDE.md](TESTING_GUIDE.md)

#### See working examples
→ [examples/](examples/)
  - [ExportController.php](examples/ExportController.php)
  - [api-routes.php](examples/api-routes.php)
  - [views/invoice.blade.php](examples/views/invoice.blade.php)

#### Troubleshoot issues
→ [BLADE_API_GUIDE.md - Troubleshooting](BLADE_API_GUIDE.md#troubleshooting)
→ [TESTING_GUIDE.md - Common Issues](TESTING_GUIDE.md#common-issues--solutions)

#### Contribute to the project
→ [CONTRIBUTING.md](CONTRIBUTING.md)

## 📖 Documentation by Topic

### Installation & Setup
- [README.md - Installation](README.md#-installation)
- [README.md - Laravel Setup](README.md#laravel-setup-optional)
- [BLADE_API_GUIDE.md - Setup](BLADE_API_GUIDE.md#setup)

### Basic Usage
- [README.md - Quick Start](README.md#-quick-start)
- [QUICK_REFERENCE.md - Basic Usage](QUICK_REFERENCE.md#basic-usage)

### Blade Templates
- [README.md - Blade Template Support](README.md#-blade-template-support)
- [BLADE_API_GUIDE.md - Creating Templates](BLADE_API_GUIDE.md#3-create-blade-template)
- [examples/views/invoice.blade.php](examples/views/invoice.blade.php)

### API Integration
- [README.md - API with Token Authentication](README.md#api-with-token-authentication)
- [BLADE_API_GUIDE.md - API Implementation](BLADE_API_GUIDE.md#api-implementation)
- [examples/ExportController.php](examples/ExportController.php)
- [examples/api-routes.php](examples/api-routes.php)

### Security
- [README.md - API Token Security](README.md#-api-token-security-optional)
- [BLADE_API_GUIDE.md - Security Best Practices](BLADE_API_GUIDE.md#security-best-practices)

### Advanced Features
- [README.md - Advanced Options](README.md#-advanced-options)
- [QUICK_REFERENCE.md - Options](QUICK_REFERENCE.md#options)

### Testing
- [TESTING_GUIDE.md](TESTING_GUIDE.md)
- [tests/BladeExportTest.php](tests/BladeExportTest.php)

### Architecture
- [ARCHITECTURE.md](ARCHITECTURE.md)
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)

## 🔍 Code Examples by Use Case

### Use Case 1: Simple PDF Report
```php
// See: README.md - Basic Usage
$exporter = new DocumentExporter();
$pdf = $exporter->export('pdf', $data);
```
📄 [Full Example](README.md#basic-usage-plain-php)

### Use Case 2: Custom Invoice with Blade
```php
// See: BLADE_API_GUIDE.md - Using the Template
$pdf = $exporter->exportFromView('pdf', 'invoice', $data);
```
📄 [Full Example](BLADE_API_GUIDE.md#using-the-template)

### Use Case 3: API Endpoint with Authentication
```php
// See: examples/ExportController.php
public function exportInvoice(Request $request) {
    $token = $request->bearerToken();
    $content = $exporter->exportFromView('pdf', 'invoice', $data, [], $token);
    return response($content)->header('Content-Type', 'application/pdf');
}
```
📄 [Full Example](examples/ExportController.php)

### Use Case 4: Multi-Format Export
```php
// See: QUICK_REFERENCE.md - Array Export
$pdf = $exporter->export('pdf', $data);
$excel = $exporter->export('excel', $data);
$word = $exporter->export('word', $data);
```
📄 [Full Example](QUICK_REFERENCE.md#array-export-all-formats)

## 📦 Package Structure

```
php-doc-exporter/
├── src/                          # Source code
│   ├── DocumentExporter.php      # Main class
│   ├── Exporters/                # Format-specific exporters
│   └── Exceptions/               # Custom exceptions
├── examples/                     # Working examples
│   ├── ExportController.php      # API controller
│   ├── api-routes.php            # Route definitions
│   └── views/                    # Blade templates
├── tests/                        # Test files
├── config/                       # Configuration
└── docs/                         # Documentation (you are here!)
```

## 🆘 Getting Help

### Common Questions
1. **How do I install?** → [README.md - Installation](README.md#-installation)
2. **How do I use Blade templates?** → [BLADE_API_GUIDE.md](BLADE_API_GUIDE.md)
3. **How do I secure my API?** → [README.md - API Token Security](README.md#-api-token-security-optional)
4. **Why aren't Bangla characters showing?** → [BLADE_API_GUIDE.md - Troubleshooting](BLADE_API_GUIDE.md#bangla-characters-showing-as-boxes)

### Still Need Help?
- Check [TESTING_GUIDE.md - Common Issues](TESTING_GUIDE.md#common-issues--solutions)
- Review [examples/](examples/) for working code
- Open an issue on GitHub

## 📝 Documentation Maintenance

### For Contributors
When adding new features:
1. Update [README.md](README.md) with basic usage
2. Add detailed guide to [BLADE_API_GUIDE.md](BLADE_API_GUIDE.md) if applicable
3. Update [QUICK_REFERENCE.md](QUICK_REFERENCE.md) with code snippets
4. Add tests to [TESTING_GUIDE.md](TESTING_GUIDE.md)
5. Update [CHANGELOG.md](CHANGELOG.md)
6. Update this index if adding new documentation files

## 🎓 Learning Path

### Beginner
1. Read [README.md](README.md)
2. Try [QUICK_REFERENCE.md](QUICK_REFERENCE.md) examples
3. Run examples from [examples/](examples/)

### Intermediate
1. Study [BLADE_API_GUIDE.md](BLADE_API_GUIDE.md)
2. Build an API endpoint
3. Create custom Blade templates

### Advanced
1. Review [ARCHITECTURE.md](ARCHITECTURE.md)
2. Study [TESTING_GUIDE.md](TESTING_GUIDE.md)
3. Contribute to the project

## 📊 Documentation Stats

- **Total Documentation Files**: 8
- **Code Examples**: 50+
- **Use Cases Covered**: 10+
- **Languages**: PHP, Blade, JavaScript, Bash
- **Diagrams**: 8 (in ARCHITECTURE.md)

## 🔗 External Resources

- [Dompdf Documentation](https://github.com/dompdf/dompdf)
- [PHPSpreadsheet Documentation](https://phpspreadsheet.readthedocs.io/)
- [PHPWord Documentation](https://phpword.readthedocs.io/)
- [Laravel Blade Documentation](https://laravel.com/docs/blade)
- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)

## 📅 Last Updated

This documentation was last updated with the Blade Template + API implementation.

---

**Quick Links:**
[Installation](README.md#-installation) | 
[Quick Start](README.md#-quick-start) | 
[Blade Guide](BLADE_API_GUIDE.md) | 
[API Reference](QUICK_REFERENCE.md) | 
[Examples](examples/) | 
[Testing](TESTING_GUIDE.md)
