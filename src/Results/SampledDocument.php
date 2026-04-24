<?php

declare(strict_types=1);

namespace Labrodev\DocumentSampler\Results;

readonly class SampledDocument
{
    public string $text;
    public int $charCount;

    public function __construct(
        public int $originalCharCount,
        public string $intro,
        public string $outline,
        public string $middle,
        public string $tail,
    ) {
        $this->text = trim(implode("\n\n---\n\n", array_filter(
            [$intro, $outline, $middle, $tail],
            fn (string $sample) => $sample !== '',
        )));
        $this->charCount = mb_strlen($this->text);
    }

    public function toJson(): string
    {
        return (string) json_encode([
            'meta' => [
                'originalCharCount' => $this->originalCharCount,
                'sampledCharCount' => $this->charCount,
            ],
            'samples' => [
                'intro' => $this->intro,
                'outline' => $this->outline,
                'middle' => $this->middle,
                'tail' => $this->tail,
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function toMd(): string
    {
        $sections = [];

        $sections[] = implode("\n", [
            '## Document Sample',
            '',
            sprintf('**Original size:** %s chars', number_format($this->originalCharCount)),
            sprintf('**Sampled size:** %s chars', number_format($this->charCount)),
        ]);

        foreach (['intro' => $this->intro, 'outline' => $this->outline, 'middle' => $this->middle, 'tail' => $this->tail] as $label => $content) {
            if ($content !== '') {
                $sections[] = sprintf("### %s\n\n%s", ucfirst($label), $content);
            }
        }

        return implode("\n\n", $sections);
    }
}
