<?php

declare(strict_types=1);

use Labrodev\DocumentSampler\DocumentSampler;
use Labrodev\DocumentSampler\Enums\DocumentPart;
use Labrodev\DocumentSampler\Exceptions\EmptyDocumentException;
use Labrodev\DocumentSampler\Results\SampledDocument;

test('sample returns a SampledDocument', function (): void {
    expect((new DocumentSampler())->sample('Some document text.'))->toBeInstanceOf(SampledDocument::class);
});

test('intro contains the opening characters of the document', function (): void {
    $text = str_repeat('A', 5000);

    expect((new DocumentSampler())->sample($text)->intro)->toBe(str_repeat('A', DocumentPart::Intro->chars()));
});

test('tail contains the closing characters of the document', function (): void {
    $text = str_repeat('A', 5000);

    expect((new DocumentSampler())->sample($text)->tail)->toBe(str_repeat('A', DocumentPart::Tail->chars()));
});

test('middle is centred on the document midpoint', function (): void {
    $pad    = str_repeat('x', 2000);
    $centre = str_repeat('M', DocumentPart::Middle->chars());

    expect((new DocumentSampler())->sample($pad.$centre.$pad)->middle)->toBe($centre);
});

test('outline extracts markdown headings from anywhere in the document', function (): void {
    $text   = "# Introduction\n\nBody text.\n\n## Summary\n\nMore text.";
    $result = (new DocumentSampler())->sample($text);

    expect($result->outline)->toContain('# Introduction')
        ->and($result->outline)->toContain('## Summary')
        ->and($result->outline)->not->toContain('Body text.');
});

test('originalCharCount reflects the full input length', function (): void {
    $text = str_repeat('a', 8000);

    expect((new DocumentSampler())->sample($text)->originalCharCount)->toBe(8000);
});

test('custom intro window overrides the default', function (): void {
    $text = str_repeat('A', 5000);

    expect((new DocumentSampler(intro: 200))->sample($text)->intro)->toBe(str_repeat('A', 200));
});

test('custom middle window overrides the default', function (): void {
    $pad    = str_repeat('x', 2000);
    $centre = str_repeat('M', 100);
    $text   = $pad.$centre.$pad;

    $result = (new DocumentSampler(middle: 100))->sample($text);

    expect(mb_strlen($result->middle))->toBe(100);
});

test('custom tail window overrides the default', function (): void {
    $text = str_repeat('A', 5000);

    expect((new DocumentSampler(tail: 200))->sample($text)->tail)->toBe(str_repeat('A', 200));
});

test('unset zones use enum defaults', function (): void {
    $text   = str_repeat('A', 5000);
    $result = (new DocumentSampler(intro: 300))->sample($text);

    expect(mb_strlen($result->tail))->toBe(DocumentPart::Tail->chars());
});

test('throws EmptyDocumentException for empty string', function (): void {
    (new DocumentSampler())->sample('');
})->throws(EmptyDocumentException::class);

test('throws EmptyDocumentException for whitespace-only string', function (): void {
    (new DocumentSampler())->sample('   ');
})->throws(EmptyDocumentException::class);
