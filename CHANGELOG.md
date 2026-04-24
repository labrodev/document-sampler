# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] — 2026-04-23

### Added

- `DocumentSampler` — stateless service with a single `sample(string $text): SampledDocument` method; no configuration required
- `SampledDocument` — immutable value object (`Results/`) with public readonly properties: `intro`, `outline`, `middle`, `tail`, `text`, `charCount`
- `DocumentPart` enum — defines the four document zones (`Intro`, `Outline`, `Middle`, `Tail`) each with a fixed `chars()` window
- `SamplerInterface` (`Contracts/`) — contract for custom sampler implementations
- `EmptyDocumentException` (`Exceptions/`) — thrown on empty or whitespace-only input
- Fixed extraction windows regardless of document size: `intro` 1000 chars from the start, `outline` up to 500 chars of extracted heading lines, `middle` 500-char window centred on the document midpoint, `tail` 500 chars from the end
- Outline extraction recognises Markdown headings (`# Heading`), numbered sections (`1.1 Title`), and ALL-CAPS lines
- PHPStan at level `max` with zero errors
