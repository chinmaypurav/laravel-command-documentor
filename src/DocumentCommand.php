<?php

namespace Chinmay\LaravelCommandDocumentor;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class DocumentCommand extends Command
{
    protected $signature = 'doc:generate';

    protected $description = 'Generates the documentation for all the commands in the application.';

    protected Collection $assortedCommands;

    protected Stringable $contents;

    public function handle(): void
    {
        $this->assortedCommands = Collection::make();

        $this->contents = Str::of('');

        $this->info('Documenting commands...');

        Artisan::call('list', [
            '--format' => 'json'
        ]);

        $output = json_decode(Artisan::output(), true);

        collect(Arr::get($output, 'commands'))
            ->filter(fn(array $command) => ! $command['hidden'])
            ->filter(fn(array $command) => ! in_array($command['name'], config('documentor.exclude', [])))
            ->each(function (array $command) {

                if (Str::doesntContain($command['name'], ':')) {
                    return;
                }

                $namespace = Str::before($command['name'], ':');

                $table = $this->assortedCommands->get($namespace, Table::make());

                $table->addRow(
                    $command['name'],
                    $command['description'],
                );

                $this->assortedCommands->put($namespace, $table);
            });

        $this->assortedCommands->each(function (Table $table){
           $this->contents = $this->contents->append($table->renderMarkdown())->newLine(2);
        });

        $this->writeToFile();

        $this->info('Documentation generated successfully.');
    }

    private function getFilePath(): string
    {
        return config('documentor.output.path') . '/' . config('documentor.output.filename');
    }

    private function writeToFile(): void
    {
        Storage::disk(config('documentor.output.disk'))
            ->put($this->getFilePath(), $this->contents->toString());
    }
}