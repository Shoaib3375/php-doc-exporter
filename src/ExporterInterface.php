<?php

namespace Shoaib3375\PhpDocExporter;

interface ExporterInterface
{
    /**
     * @param array $data
     * @param array $options
     * @return string The generated file content or path
     */
    public function export(array $data, array $options = []): string;
}
