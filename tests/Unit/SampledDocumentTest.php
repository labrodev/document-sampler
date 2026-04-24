<?php

declare(strict_types=1);

use Labrodev\DocumentSampler\Results\SampledDocument;

$make = fn (string $intro = '', string $outline = '', string $middle = '', string $tail = '') => new SampledDocument(
    originalCharCount: 10000,
    intro:             $intro,
    outline:           $outline,
    middle:            $middle,
    tail:              $tail,
);

test('text joins non-empty zones with separator', function () use ($make): void {
    $doc = $make(intro: 'Intro', outline: 'Outline', middle: 'Middle', tail: 'Tail');

    expect($doc->text)->toContain("\n\n---\n\n");
});

test('text skips empty zones', function () use ($make): void {
    $doc = $make(intro: 'Intro', tail: 'Tail');

    expect($doc->text)->not->toContain("\n\n---\n\n\n\n---\n\n");
});

test('charCount returns mb_strlen of combined text', function () use ($make): void {
    expect($make(intro: 'Hello')->charCount)->toBe(5);
});

test('zone attributes hold the correct content', function () use ($make): void {
    $doc = $make(intro: 'Hello', outline: 'Hi', middle: 'Mid', tail: 'Bye');

    expect($doc->intro)->toBe('Hello')
        ->and($doc->outline)->toBe('Hi')
        ->and($doc->middle)->toBe('Mid')
        ->and($doc->tail)->toBe('Bye');
});

test('originalCharCount is stored correctly', function () use ($make): void {
    expect($make()->originalCharCount)->toBe(10000);
});

test('toJson contains meta and zones keys', function () use ($make): void {
    $json = json_decode($make(intro: 'Hello')->toJson(), true);

    expect($json)->toHaveKeys(['meta', 'samples'])
        ->and($json['meta']['originalCharCount'])->toBe(10000)
        ->and($json['meta']['sampledCharCount'])->toBe(5)
        ->and($json['samples']['intro'])->toBe('Hello');
});

test('toMd contains zone headings and meta', function () use ($make): void {
    $md = $make(intro: 'Opening text', tail: 'Closing text')->toMd();

    expect($md)->toContain('## Document Sample')
        ->and($md)->toContain('**Original size:**')
        ->and($md)->toContain('### Intro')
        ->and($md)->toContain('Opening text')
        ->and($md)->toContain('### Tail')
        ->and($md)->toContain('Closing text');
});

test('toMd skips empty zones', function () use ($make): void {
    $md = $make(intro: 'Hello')->toMd();

    expect($md)->not->toContain('### Outline')
        ->and($md)->not->toContain('### Middle')
        ->and($md)->not->toContain('### Tail');
});
