<?php

namespace Shoaib3375\PhpDocExporter\Exceptions;

use InvalidArgumentException;

class InvalidTokenException extends InvalidArgumentException
{
    public static function invalid(): self
    {
        return new self("Invalid or insufficient API token provided.");
    }

    public static function mainRequired(): self
    {
        return new self("Main API token required for this operation.");
    }
}
