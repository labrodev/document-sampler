<?php

declare(strict_types=1);

namespace Labrodev\DocumentSampler\Enums;

enum DocumentPart: string
{
    case Intro = 'intro';
    case Outline = 'outline';
    case Middle = 'middle';
    case Tail = 'tail';

    public function chars(): int
    {
        return match ($this) {
            self::Intro => 1000,
            self::Outline, self::Middle, self::Tail => 500,
        };
    }
}
