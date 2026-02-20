<?php

namespace Shoaib3375\PhpDocExporter\Exceptions;

use InvalidArgumentException;

class EmptyDataException extends InvalidArgumentException
{
    public static function noData(): self
    {
        return new self("Cannot export empty data. Please provide at least one row.");
    }
}
