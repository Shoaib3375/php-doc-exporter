<?php

namespace Shoaib3375\PhpDocExporter;

use Shoaib3375\PhpDocExporter\Exporters\PdfExporter;
use Shoaib3375\PhpDocExporter\Exporters\ExcelExporter;
use Shoaib3375\PhpDocExporter\Exporters\WordExporter;
use Shoaib3375\PhpDocExporter\Exporters\CsvExporter;
use Shoaib3375\PhpDocExporter\Exceptions\InvalidFormatException;
use Shoaib3375\PhpDocExporter\Exceptions\EmptyDataException;
use Shoaib3375\PhpDocExporter\Exceptions\InvalidTokenException;

class DocumentExporter
{
    protected Config $config;

    public function __construct(Config $config = null)
    {
        $this->config = $config ?? new Config();
    }

    /**
     * Export data to specified format
     * 
     * @param string $format Export format (pdf, excel, word, csv)
     * @param array $data Associative array of data to export
     * @param array $options Export options (title, paper, orientation, font)
     * @param string|null $token Optional API token for validation
     * @return string Generated document content
     * @throws EmptyDataException If data array is empty
     * @throws InvalidFormatException If format is not supported
     * @throws InvalidTokenException If token validation fails
     */
    public function export(string $format, array $data, array $options = [], string $token = null): string
    {
        if (empty($data)) {
            throw EmptyDataException::noData();
        }

        if ($token && !$this->config->canAccessSafeApi($token)) {
            throw InvalidTokenException::invalid();
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
            default => throw InvalidFormatException::unsupported($format),
        };
    }

    /**
     * Create a new package (requires main token)
     * 
     * @param string $name Package name
     * @param string $token API token
     * @return bool
     * @throws InvalidTokenException If main token is not provided
     */
    public function createPackage(string $name, string $token): bool
    {
        if (!$this->config->canAccessFullApi($token)) {
            throw InvalidTokenException::mainRequired();
        }
        // Logic for package creation
        return true;
    }

    /**
     * Edit an existing package (requires main token)
     * 
     * @param string $id Package ID
     * @param array $data Package data
     * @param string $token API token
     * @return bool
     * @throws InvalidTokenException If main token is not provided
     */
    public function editPackage(string $id, array $data, string $token): bool
    {
        if (!$this->config->canAccessFullApi($token)) {
            throw InvalidTokenException::mainRequired();
        }
        // Logic for package edit
        return true;
    }

    /**
     * Update a package (requires safe or main token)
     * 
     * @param string $id Package ID
     * @param array $data Package data
     * @param string $token API token
     * @return bool
     * @throws InvalidTokenException If valid token is not provided
     */
    public function updatePackage(string $id, array $data, string $token): bool
    {
        if (!$this->config->canAccessSafeApi($token)) {
            throw InvalidTokenException::invalid();
        }
        // Logic for package update
        return true;
    }
}
