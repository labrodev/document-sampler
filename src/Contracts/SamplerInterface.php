<?php

declare(strict_types=1);

namespace Labrodev\DocumentSampler\Contracts;

use Labrodev\DocumentSampler\Results\SampledDocument;

interface SamplerInterface
{
    public function sample(string $text): SampledDocument;
}
