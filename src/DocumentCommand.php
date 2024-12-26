<?php

namespace Chinmay\LaravelCommandDocumentor;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
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
            // hidden filter
            ->when(
                Config::get('documentor.exclude.hidden'),
                fn (Collection $commands) => $commands->filter(fn (array $command) => ! $command['hidden'])
            )
            // namespace filter
            ->when(
                Config::get('documentor.include.namespaces'),
                fn (Collection $commands, array $namespaces) =>
                    $commands->filter(fn (array $command) => Str::startsWith($command['name'], $namespaces))
            )
            ->when(
                Config::get('documentor.exclude.namespaces'),
                fn (Collection $commands, array $namespaces) =>
                    $commands->filter(fn (array $command) => ! Str::startsWith($command['name'], $namespaces))
            )
            // signature filter
            ->when(
                Config::get('documentor.include.signatures'),
                fn (Collection $commands, array $signatures) =>
                    $commands->filter(fn (array $command) => in_array($command['name'], $signatures)),
            )
            ->when(
                Config::get('documentor.exclude.signatures'),
                fn (Collection $commands, array $signatures) =>
                    $commands->filter(fn (array $command) => ! in_array($command['name'], $signatures)),
            )
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

        $this->assortedCommands->each(function (Table $table) {
            $this->contents = $this->contents->append($table->renderMarkdown())->newLine(2);
        });

        $this->writeToFile();

        $this->info('Documentation generated successfully.');
    }

    private function getFilePath(): string
    {
        return Str::of(Config::get('documentor.output.path'))
                ->append('/')
                ->append(Config::get('documentor.output.filename'))
                ->toString();
    }

    private function writeToFile(): void
    {
        Storage::disk(config('documentor.output.disk'))
            ->put($this->getFilePath(), $this->contents->toString());
    }
}
