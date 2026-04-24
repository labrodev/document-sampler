<?php

declare(strict_types=1);

namespace Labrodev\DocumentSampler;

use Labrodev\DocumentSampler\Contracts\SamplerInterface;
use Labrodev\DocumentSampler\Enums\DocumentPart;
use Labrodev\DocumentSampler\Exceptions\EmptyDocumentException;
use Labrodev\DocumentSampler\Results\SampledDocument;

readonly class DocumentSampler implements SamplerInterface
{
    public function __construct(
        private ?int $intro = null,
        private ?int $outline = null,
        private ?int $middle = null,
        private ?int $tail = null,
    ) {}

    public function sample(string $text): SampledDocument
    {
        if (trim($text) === '') {
            throw EmptyDocumentException::make();
        }

        $length = mb_strlen($text);

        return new SampledDocument(
            originalCharCount: $length,
            intro: mb_substr($text, 0, $this->charsFor(DocumentPart::Intro)),
            outline: $this->extractOutline($text, $this->charsFor(DocumentPart::Outline)),
            middle: $this->extractMiddle($text, $length),
            tail: mb_substr($text, -$this->charsFor(DocumentPart::Tail)),
        );
    }

    private function charsFor(DocumentPart $part): int
    {
        return match ($part) {
            DocumentPart::Intro => $this->intro   ?? $part->chars(),
            DocumentPart::Outline => $this->outline ?? $part->chars(),
            DocumentPart::Middle => $this->middle  ?? $part->chars(),
            DocumentPart::Tail => $this->tail    ?? $part->chars(),
        };
    }

    private function extractOutline(string $text, int $charLimit): string
    {
        $matches = [];

        preg_match_all('/^#{1,6}\s.+$/mu', $text, $md);
        $matches = array_merge($matches, $md[0]);

        preg_match_all('/^\d+(?:\.\d+)*\.?\s+\S.+$/mu', $text, $numbered);
        $matches = array_merge($matches, $numbered[0]);

        preg_match_all('/^[^a-z\n]{3,80}$/mu', $text, $caps);
        foreach ($caps[0] as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '' && preg_match('/[A-Z]/', $trimmed)) {
                $matches[] = $trimmed;
            }
        }

        $result = '';
        foreach (array_unique($matches) as $match) {
            $candidate = $result === '' ? $match : $result."\n".$match;
            if (mb_strlen($candidate) > $charLimit) {
                break;
            }
            $result = $candidate;
        }

        return $result;
    }

    private function extractMiddle(string $text, int $length): string
    {
        $chars = $this->charsFor(DocumentPart::Middle);
        $centre = (int) floor($length / 2);
        $start = max(0, $centre - (int) floor($chars / 2));

        return mb_substr($text, $start, $chars);
    }
}
