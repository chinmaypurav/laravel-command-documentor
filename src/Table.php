<?php

namespace Chinmay\LaravelCommandDocumentor;

class Table
{
    private array $headings = [
        'Signature',
        'Description',
    ];

    private array $rows = [];

    public function addRow(string $signature, string $description): self
    {
        $this->rows[] = [
            $signature,
            $description,
        ];

        return $this;
    }

    public function renderMarkdown(): string
    {
        $markdown = '| ' . implode(' | ', $this->headings) . " |\n";
        $markdown .= '| ' . implode(' | ', array_map(fn() => '---', $this->headings)) . " |\n";

        foreach ($this->rows as $row) {
            $markdown .= '| ' . implode(' | ', $row) . " |\n";
        }

        return $markdown;
    }

    public static function make(): static
    {
        return new self();
    }
}