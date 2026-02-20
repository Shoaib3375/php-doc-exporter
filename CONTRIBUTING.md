# Contributing to PHP Doc Exporter

Thank you for considering contributing to PHP Doc Exporter!

## Development Setup

1. Clone the repository
```bash
git clone https://github.com/shoaib3375/php-doc-exporter.git
cd php-doc-exporter
```

2. Install dependencies
```bash
composer install
```

3. Run tests
```bash
vendor/bin/phpunit
```

## Coding Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for API changes
- Add entries to CHANGELOG.md

## Pull Request Process

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Testing

All new features must include tests:
```bash
vendor/bin/phpunit
```

## Reporting Issues

- Use GitHub Issues
- Include PHP version, package version, and error messages
- Provide minimal code to reproduce the issue
