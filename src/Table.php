<?php

namespace Chinmay\LaravelCommandDocumentor;

use Illuminate\Support\Str;

class Table
{
    private array $headings = [
        'Signature',
        'Description',
    ];

    private array $rows = [];

    private array $rowDataLengths = [];

    public function __construct(public string $namespace = '')
    {
        foreach ($this->headings as $heading) {
            $this->rowDataLengths[$heading] = min(strlen($heading), 3);
        }
    }

    public function addRow(string $signature, string $description): self
    {
        $this->rowDataLengths['Signature'] = max($this->rowDataLengths['Signature'], strlen($signature));
        $this->rowDataLengths['Description'] = max($this->rowDataLengths['Description'], strlen($description));

        $this->rows[] = [
            $signature,
            $description,
        ];

        return $this;
    }

    public function renderMarkdown(): string
    {
        $markdown = Str::of('|');

        foreach ($this->headings as $heading) {
            $markdown = $markdown->append(' '.str_pad($heading, $this->rowDataLengths[$heading], ' ').' |');
        }

        $markdown = $markdown->newLine()->append('|');
        foreach ($this->rowDataLengths as $length) {
            $markdown = $markdown->append($this->getDash($length + 2).'|');
        }

        $markdown = $markdown->newLine();

        foreach ($this->rows as $row) {
            $markdown = $markdown->append('|');
            foreach ($row as $key => $item) {
                $length = $this->rowDataLengths[$this->headings[$key]];
                $markdown = $markdown->append(' '.str_pad($item, $length, ' ').' |');
            }
            $markdown = $markdown->newLine();
        }

        return $markdown->toString();
    }

    private function getDash(int $count): string
    {
        return str_repeat('-', $count);
    }

    public static function make(string $namespace): static
    {
        return new self($namespace);
    }
}
