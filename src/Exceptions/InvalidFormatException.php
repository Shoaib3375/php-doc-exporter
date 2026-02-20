<?php

namespace Shoaib3375\PhpDocExporter\Exceptions;

use InvalidArgumentException;

class InvalidFormatException extends InvalidArgumentException
{
    public static function unsupported(string $format): self
    {
        return new self("Unsupported export format: {$format}. Supported formats: pdf, excel, word, csv");
    }
}
