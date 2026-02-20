<?php

namespace Shoaib3375\PhpDocExporter;

use Shoaib3375\PhpDocExporter\Exporters\PdfExporter;
use Shoaib3375\PhpDocExporter\Exporters\ExcelExporter;
use Shoaib3375\PhpDocExporter\Exporters\WordExporter;
use Shoaib3375\PhpDocExporter\Exporters\CsvExporter;
use InvalidArgumentException;

class DocumentExporter
{
    protected Config $config;

    public function __construct(Config $config = null)
    {
        $this->config = $config ?? new Config();
    }

    /**
     * @param string $format
     * @param array $data
     * @param array $options
     * @param string|null $token
     * @return string
     * @throws InvalidArgumentException
     */
    public function export(string $format, array $data, array $options = [], string $token = null): string
    {
        if ($token && !$this->config->canAccessSafeApi($token)) {
            throw new InvalidArgumentException("Invalid or insufficient API token.");
        }

        $exporter = $this->getExporter($format);
        return $exporter->export($data, $options);
    }

    protected function getExporter(string $format): ExporterInterface
    {
        return match (strtolower($format)) {
            'pdf' => new PdfExporter(),
            'excel', 'xlsx' => new ExcelExporter(),
            'word', 'docx' => new WordExporter(),
            'csv' => new CsvExporter(),
            default => throw new InvalidArgumentException("Unsupported format: {$format}"),
        };
    }

    /**
     * These methods represent the "Full APIs" that require the main token
     */
    public function createPackage(string $name, string $token): bool
    {
        if (!$this->config->canAccessFullApi($token)) {
            throw new InvalidArgumentException("Main API token required for package creation.");
        }
        // Logic for package creation
        return true;
    }

    public function editPackage(string $id, array $data, string $token): bool
    {
        if (!$this->config->canAccessFullApi($token)) {
            throw new InvalidArgumentException("Main API token required for package edit.");
        }
        // Logic for package edit
        return true;
    }

    /**
     * This represents a "Safe API"
     */
    public function updatePackage(string $id, array $data, string $token): bool
    {
        if (!$this->config->canAccessSafeApi($token)) {
            throw new InvalidArgumentException("Valid API token required for package update.");
        }
        // Logic for package update
        return true;
    }
}
