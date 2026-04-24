<?php

declare(strict_types=1);

namespace Labrodev\DocumentSampler\Exceptions;

use InvalidArgumentException;

class EmptyDocumentException extends InvalidArgumentException
{
    public static function make(): EmptyDocumentException
    {
        return new self('The document is empty.');
    }
}
