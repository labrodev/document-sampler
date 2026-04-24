# DocumentSampler

[![Latest Version on Packagist](https://img.shields.io/packagist/v/labrodev/document-sampler.svg)](https://packagist.org/packages/labrodev/document-sampler)
[![License](https://img.shields.io/packagist/l/labrodev/document-sampler.svg)](LICENSE)

Pure PHP library that extracts a structured, representative sample from a document of any length. No framework dependency, no HTTP calls, no AI — just text processing.

Designed as the input layer for downstream AI-powered packages such as relevance checkers, prompt injection detectors, and depersonalisation services.

---

## Requirements

- PHP `^8.5`

---

## Installation

```bash
composer require labrodev/document-sampler
```

---

## Basic usage

```php
use Labrodev\DocumentSampler\DocumentSampler;

$result = (new DocumentSampler())->sample($rawText);

$result->intro             // opening chars — title and introduction
$result->outline           // extracted section headings from anywhere in the document
$result->middle            // fixed window centred on the document midpoint
$result->tail              // closing chars — conclusion and sign-off
$result->text              // all samples joined with separators
$result->charCount         // character count of the combined sample
$result->originalCharCount // character count of the original document
```

---

## Custom window sizes

By default each zone uses the window defined on the `DocumentPart` enum. Pass any subset to the constructor to override:

```php
// Override specific zones — unset zones use the enum defaults
$sampler = new DocumentSampler(
    intro:   2000,
    middle:  300,
);

$result = $sampler->sample($rawText);
```

---

## How it works

The sampler partitions every document into four fixed-size windows regardless of document length:

| Zone | Default window | What it captures |
|------|---------------|-----------------|
| `intro` | 1000 chars | Title, abstract, opening paragraphs |
| `outline` | 500 chars | Section headings (`# Markdown`, `1.1 Numbered`, `ALL-CAPS` lines) |
| `middle` | 500 chars | Window centred on the document midpoint |
| `tail` | 500 chars | Closing paragraphs, conclusion, signature |

Windows are fixed — a 400-page PDF gets the same sized sample as a one-page memo. The goal is a compact, representative fingerprint of the document, not a summary.

---

## Exporting results

### JSON

```php
$result->toJson();
```

```json
{
    "meta": {
        "originalCharCount": 50000,
        "sampledCharCount": 2300
    },
    "samples": {
        "intro": "...",
        "outline": "...",
        "middle": "...",
        "tail": "..."
    }
}
```

### Markdown

```php
$result->toMd();
```

```markdown
## Document Sample

**Original size:** 50,000 chars
**Sampled size:** 2,300 chars

### Intro
...

### Outline
...

### Middle
...

### Tail
...
```

Empty zones are omitted from both outputs.

---

## Default window sizes

Window sizes are defined on the `DocumentPart` enum and can be read at runtime:

```php
use Labrodev\DocumentSampler\Enums\DocumentPart;

DocumentPart::Intro->chars();   // 1000
DocumentPart::Outline->chars(); // 500
DocumentPart::Middle->chars();  // 500
DocumentPart::Tail->chars();    // 500
```

---

## When to use this

- **Before calling an AI API** — reduce a large document to a structured excerpt that fits in a context window without losing structural information.
- **Relevance checking** — feed `$result->text` to a classifier to decide whether a document is relevant before processing it in full.
- **Prompt injection detection** — scan a compact sample for malicious instructions before passing untrusted documents to an LLM.
- **Depersonalisation** — run PII detection over a representative sample before deciding whether to redact the full document.
- **Document classification** — use the outline and intro zones to classify document type without reading the entire file.

---

## Testing

```bash
composer test
```

## Static analysis

```bash
composer analyse
```

---

## Author

**Petro Lashyn** — [contact@labrodev.com](mailto:contact@labrodev.com)

---

## License

MIT
